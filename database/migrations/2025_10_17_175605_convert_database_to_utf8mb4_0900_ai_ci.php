<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $database = DB::getDatabaseName();

        // Step 1: Change database default collation
        DB::statement("ALTER DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");

        // Step 2: Save all foreign key constraints
        $foreignKeys = DB::select("
            SELECT
                kcu.TABLE_NAME,
                kcu.CONSTRAINT_NAME,
                kcu.COLUMN_NAME,
                kcu.REFERENCED_TABLE_NAME,
                kcu.REFERENCED_COLUMN_NAME,
                rc.UPDATE_RULE,
                rc.DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE kcu
            JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
                ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
                AND kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
            WHERE kcu.TABLE_SCHEMA = '{$database}'
            AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
        ");

        // Step 3: Drop all foreign keys
        foreach ($foreignKeys as $fk) {
            echo "Dropping FK: {$fk->CONSTRAINT_NAME} on {$fk->TABLE_NAME}\n";
            DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // Step 4: Get all tables and convert them
        $tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$database}' AND TABLE_TYPE = 'BASE TABLE'");

        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            echo "Converting table: {$tableName}\n";
            DB::statement("ALTER TABLE `{$tableName}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
        }

        // Step 5: Recreate all foreign keys
        foreach ($foreignKeys as $fk) {
            echo "Recreating FK: {$fk->CONSTRAINT_NAME} on {$fk->TABLE_NAME}\n";
            try {
                DB::statement("
                    ALTER TABLE `{$fk->TABLE_NAME}`
                    ADD CONSTRAINT `{$fk->CONSTRAINT_NAME}`
                    FOREIGN KEY (`{$fk->COLUMN_NAME}`)
                    REFERENCES `{$fk->REFERENCED_TABLE_NAME}` (`{$fk->REFERENCED_COLUMN_NAME}`)
                    ON UPDATE {$fk->UPDATE_RULE}
                    ON DELETE {$fk->DELETE_RULE}
                ");
            } catch (\Exception $e) {
                // Find all orphaned rows for this FK
                $orphans = DB::select("
                    SELECT c.`{$fk->COLUMN_NAME}` AS orphan_value
                    FROM `{$fk->TABLE_NAME}` c
                    LEFT JOIN `{$fk->REFERENCED_TABLE_NAME}` p
                        ON c.`{$fk->COLUMN_NAME}` = p.`{$fk->REFERENCED_COLUMN_NAME}`
                    WHERE p.`{$fk->REFERENCED_COLUMN_NAME}` IS NULL
                      AND c.`{$fk->COLUMN_NAME}` IS NOT NULL
                ");

                if (empty($orphans)) {
                    throw $e; // Not an orphan issue — surface the original error
                }

                // Check whether the child column allows NULLs
                $colInfo = DB::selectOne("
                    SELECT IS_NULLABLE
                    FROM information_schema.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME   = ?
                      AND COLUMN_NAME  = ?
                ", [$fk->TABLE_NAME, $fk->COLUMN_NAME]);

                $isNullable = $colInfo && $colInfo->IS_NULLABLE === 'YES';
                $orphanValues = implode(', ', array_column($orphans, 'orphan_value'));

                if ($isNullable) {
                    // Safe fix: NULL out the orphaned references, then retry
                    DB::statement("
                        UPDATE `{$fk->TABLE_NAME}` c
                        LEFT JOIN `{$fk->REFERENCED_TABLE_NAME}` p
                            ON c.`{$fk->COLUMN_NAME}` = p.`{$fk->REFERENCED_COLUMN_NAME}`
                        SET c.`{$fk->COLUMN_NAME}` = NULL
                        WHERE p.`{$fk->REFERENCED_COLUMN_NAME}` IS NULL
                          AND c.`{$fk->COLUMN_NAME}` IS NOT NULL
                    ");
                    echo "[INFO] {$fk->TABLE_NAME}.{$fk->COLUMN_NAME}: "
                        . count($orphans) . " orphan(s) set to NULL (orphaned values were: {$orphanValues})\n";

                    // Retry adding the FK now that orphans are resolved
                    DB::statement("
                        ALTER TABLE `{$fk->TABLE_NAME}`
                        ADD CONSTRAINT `{$fk->CONSTRAINT_NAME}`
                        FOREIGN KEY (`{$fk->COLUMN_NAME}`)
                        REFERENCES `{$fk->REFERENCED_TABLE_NAME}` (`{$fk->REFERENCED_COLUMN_NAME}`)
                        ON UPDATE {$fk->UPDATE_RULE}
                        ON DELETE {$fk->DELETE_RULE}
                    ");
                } else {
                    // Column is NOT NULL — cannot auto-fix safely, refuse and explain
                    echo "[ERROR] {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} → "
                        . "{$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}: "
                        . count($orphans) . " orphan(s) found but column is NOT NULL — cannot auto-fix.\n"
                        . "  Orphaned values: {$orphanValues}\n"
                        . "  Manual options: (a) delete these rows, (b) insert the missing parent rows, "
                        . "or (c) alter the column to allow NULLs first.\n";
                    throw $e;
                }
            }
        }

        // Step 6: Recreate all triggers with new collation
        $triggers = DB::select("SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = '{$database}'");

        if (!empty($triggers)) {
            // MySQL requires SUPER privilege (or log_bin_trust_function_creators=1) to CREATE TRIGGER
            // when binary logging is enabled. Try to enable it; if we can't, leave triggers untouched.
            $canRecreateTriggers = false;
            try {
                DB::statement('SET GLOBAL log_bin_trust_function_creators = 1');
                $canRecreateTriggers = true;
            } catch (\Exception $e) {
                echo "[WARNING] Cannot set log_bin_trust_function_creators=1 (requires SUPER privilege). "
                    . "Trigger recreation skipped — triggers remain with old collation. "
                    . "To fix, ask a DBA to run: SET GLOBAL log_bin_trust_function_creators = 1, "
                    . "then roll back and re-run this migration.\n";
            }

            if ($canRecreateTriggers) {
                // Store trigger definitions before dropping
                $triggerDefinitions = [];
                foreach ($triggers as $trigger) {
                    $result = DB::select("SHOW CREATE TRIGGER `{$trigger->TRIGGER_NAME}`");
                    $triggerDefinitions[$trigger->TRIGGER_NAME] = $result[0]->{'SQL Original Statement'};
                }

                // Drop all triggers
                foreach ($triggers as $trigger) {
                    DB::statement("DROP TRIGGER IF EXISTS `{$trigger->TRIGGER_NAME}`");
                }

                // Recreate triggers with new collation
                DB::statement('SET character_set_client = utf8mb4');
                DB::statement('SET collation_connection = utf8mb4_0900_ai_ci');

                foreach ($triggerDefinitions as $name => $definition) {
                    $definition = preg_replace('/DEFINER\s*=\s*`[^`]+`@`[^`]+`/i', '', $definition);
                    $definition = trim($definition);
                    if (!empty($definition)) {
                        DB::unprepared($definition);
                        echo "Recreated trigger: {$name}\n";
                    } else {
                        echo "WARNING: Empty definition for trigger: {$name}\n";
                    }
                }
            }
        }

        // Step 7: Recreate stored procedures and functions
        $procedures = DB::select("SELECT ROUTINE_NAME, ROUTINE_TYPE FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = '{$database}'");

        $routineDefinitions = [];
        foreach ($procedures as $procedure) {
            $type = $procedure->ROUTINE_TYPE;
            $name = $procedure->ROUTINE_NAME;

            if ($type === 'PROCEDURE') {
                $result = DB::select("SHOW CREATE PROCEDURE `{$name}`");
                $routineDefinitions[$name] = ['type' => 'PROCEDURE', 'sql' => $result[0]->{'Create Procedure'}];
            } else {
                $result = DB::select("SHOW CREATE FUNCTION `{$name}`");
                $routineDefinitions[$name] = ['type' => 'FUNCTION', 'sql' => $result[0]->{'Create Function'}];
            }
        }

        // Drop and recreate routines
        foreach ($routineDefinitions as $name => $routine) {
            DB::statement("DROP {$routine['type']} IF EXISTS `{$name}`");

            // Remove DEFINER clause
            $sql = preg_replace('/DEFINER\s*=\s*`[^`]+`@`[^`]+`/i', '', $routine['sql']);
            $sql = trim($sql);
            if (!empty($sql)) {
                DB::unprepared($sql);
                echo "Recreated {$routine['type']}: {$name}\n";
            } else {
                echo "WARNING: Empty definition for {$routine['type']}: {$name}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $database = DB::getDatabaseName();

        // Revert to utf8mb4_unicode_ci
        DB::statement("ALTER DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$database}' AND TABLE_TYPE = 'BASE TABLE'");

        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            DB::statement("ALTER TABLE `{$tableName}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }

        // Note: Triggers and procedures would need to be recreated with utf8mb4_unicode_ci
        // This is left simplified - in practice you might want to store the original state
    }
};
