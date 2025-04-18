<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Database\Seeders\TranslatedAttributesSeeder;

return new class extends Migration
{
    /**
     * Define the structure for tables and columns to migrate based on verified schema.
     */
    private $translatableAttributes = [
        // tableName => [columnName, originalTypeMethod, typeArgs[], wasNullable]
        'matter_category' => ['category', 'string', [45], false],
        'classifier_type' => ['type', 'string', [45], false],
        'event_name' => ['name', 'string', [45], false],
        'matter_type' => ['type', 'string', [45], false],
        'actor_role' => ['name', 'string', [45], false],
        'task_rules' => ['detail', 'string', [45], true],
        'task' => ['detail', 'string', [45], true], // Added task.detail to the list
    ];

    /**
     * The task_rules.uid generated column definition that needs to be handled specially
     */
    private $taskRulesUidDefinition = "ADD COLUMN `uid` varchar(32) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (md5(concat(`task`,`trigger_event`,`clear_task`,`delete_task`,`for_category`,ifnull(`for_country`,_utf8mb4'c'),ifnull(`for_origin`,_utf8mb4'o'),ifnull(`for_type`,_utf8mb4't'),`days`,`months`,`years`,`recurring`,ifnull(`abort_on`,_utf8mb4'a'),ifnull(`condition_event`,_utf8mb4'c'),`use_priority`,ifnull(`detail`,_utf8mb4'd')))) VIRTUAL";

    /**
     * The suffix for the temporary column holding the default English value.
     */
    private $tempSuffix = '_en';

    /**
     * The locales to create indexes for.
     */
    private $localesToIndex = ['en', 'fr', 'de'];

    /**
     * The case-insensitive collation to use for JSON indexes.
     */
    private $jsonIndexCollation = 'utf8mb4_0900_ai_ci';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $activeAttributes = array_filter($this->translatableAttributes);
        if (empty($activeAttributes)) {
            Log::warning('Migration for translatable attributes: No attributes configured. Skipping.');
            return;
        }

        $defaultLocale = 'en';

        foreach ($activeAttributes as $tableName => [$columnName, $originalTypeMethod, $originalTypeArgs, $wasNullable]) {
            Log::info("Starting migration for {$tableName}.{$columnName}");

            $tempColumnName = $columnName . $this->tempSuffix;
            $jsonColumnName = $columnName;

            // --- Special handling for task_rules.uid dependency ---
            if ($tableName === 'task_rules') {
                $this->handleTaskRulesUid('drop');
            }

            // --- Drop original indexes if they exist ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columnName) {
                try {
                    $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($tableName);
                    if (isset($indexes[$columnName])) {
                        $table->dropIndex($columnName);
                        Log::info("Dropped original index '{$columnName}' from table {$tableName}.");
                    }
                } catch (\Exception $e) {
                    Log::warning("Could not check/drop index '{$columnName}' from {$tableName}: " . $e->getMessage());
                }
            });

            // --- Step 1: Rename existing column to have the _en suffix ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $jsonColumnName, $tempColumnName) {
                if (!Schema::hasColumn($tableName, $tempColumnName) && Schema::hasColumn($tableName, $jsonColumnName)) {
                    $table->renameColumn($jsonColumnName, $tempColumnName);
                    Log::info("Renamed column {$jsonColumnName} to {$tempColumnName} on table {$tableName}.");
                }
            });

            // --- Step 2: Add the new JSON column with the original name ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $jsonColumnName, $tempColumnName) {
                if (!Schema::hasColumn($tableName, $jsonColumnName)) {
                    $table->json($jsonColumnName)->nullable()->after($tempColumnName);
                    Log::info("Added new JSON column {$jsonColumnName} to table {$tableName}.");
                } else {
                    DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `{$jsonColumnName}` JSON NULL");
                }
            });

            // --- Step 3: Migrate Data using MySQL JSON_OBJECT ---
            $sql = "UPDATE `{$tableName}`
                    SET `{$jsonColumnName}` = JSON_OBJECT(?, `{$tempColumnName}`)
                    WHERE `{$tempColumnName}` IS NOT NULL AND (`{$jsonColumnName}` IS NULL)";
            try {
                $count = DB::update($sql, [$defaultLocale]);
                Log::info("Migrated data for {$count} rows in {$tableName}.");
            } catch (\Exception $e) {
                Log::error("Data migration failed for {$tableName}.{$jsonColumnName}: " . $e->getMessage());
                throw $e;
            }

            // --- Step 4: Add Functional Indexes for all specified locales ---
            $castType = "CHAR({$originalTypeArgs[0]})";

            foreach ($this->localesToIndex as $locale) {
                $indexName = "idx_{$tableName}_{$jsonColumnName}_{$locale}";
                $sqlIndex = "ALTER TABLE `{$tableName}`
                    ADD INDEX `{$indexName}` ((CAST(`{$jsonColumnName}`->>'$.{$locale}' AS {$castType}) COLLATE {$this->jsonIndexCollation}))";
                try {
                    $exists = DB::select("SHOW INDEX FROM `{$tableName}` WHERE Key_name = ?", [$indexName]);
                    if (empty($exists)) {
                        DB::statement($sqlIndex);
                        Log::info("Added functional index {$indexName} on {$tableName}.");
                    }
                } catch (\Exception $e) {
                    Log::error("Index creation failed for {$tableName}.{$jsonColumnName} (locale: {$locale}): " . $e->getMessage());
                    throw $e;
                }
            }

            // --- Step 5: Drop the temporary _en column ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $tempColumnName) {
                if (Schema::hasColumn($tableName, $tempColumnName)) {
                    $table->dropColumn($tempColumnName);
                    Log::info("Dropped temporary column {$tempColumnName} from table {$tableName}.");
                }
            });

            // --- Re-add task_rules.uid if needed ---
            if ($tableName === 'task_rules') {
                $this->handleTaskRulesUid('add');
            }

            Log::info("Successfully completed migration for {$tableName}.{$columnName}");
        }

        // --- Run the TranslatedAttributesSeeder ---
        Log::info("Running TranslatedAttributesSeeder to populate translations...");
        $seeder = new TranslatedAttributesSeeder();
        $seeder->run();
        Log::info("Completed running TranslatedAttributesSeeder.");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $activeAttributes = array_filter($this->translatableAttributes);
        if (empty($activeAttributes)) {
            Log::warning('Reverting translation migration: No attributes configured. Skipping.');
            return;
        }

        $defaultLocale = 'en';

        foreach (array_reverse($activeAttributes) as $tableName => [$columnName, $originalTypeMethod, $originalTypeArgs, $wasNullable]) {
            Log::info("Reverting migration for {$tableName}.{$columnName}");

            $tempColumnName = $columnName . $this->tempSuffix;
            $jsonColumnName = $columnName;

            // --- Special handling for task_rules.uid dependency ---
            if ($tableName === 'task_rules') {
                $this->handleTaskRulesUid('drop');
            }

            // --- Step 1: Re-add the temporary _en column ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $jsonColumnName, $tempColumnName, $originalTypeMethod, $originalTypeArgs) {
                if (!Schema::hasColumn($tableName, $tempColumnName)) {
                    $table->{$originalTypeMethod}($tempColumnName, ...$originalTypeArgs)
                        ->nullable()
                        ->after($jsonColumnName);
                }
            });

            // --- Step 2: Extract data back to VARCHAR column ---
            $sqlExtract = "UPDATE `{$tableName}`
                          SET `{$tempColumnName}` = JSON_UNQUOTE(JSON_EXTRACT(`{$jsonColumnName}`, '$.{$defaultLocale}'))
                          WHERE `{$jsonColumnName}` IS NOT NULL
                            AND JSON_CONTAINS_PATH(`{$jsonColumnName}`, 'one', '$.{$defaultLocale}') = 1
                            AND `{$tempColumnName}` IS NULL";
            try {
                $count = DB::update($sqlExtract);
                Log::info("Extracted '{$defaultLocale}' data back to {$tempColumnName} for {$count} rows.");
            } catch (\Exception $e) {
                Log::error("Data extraction failed for {$tableName}.{$tempColumnName}: " . $e->getMessage());
            }

            // --- Step 3: Drop all locale-specific indexes ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $jsonColumnName) {
                foreach ($this->localesToIndex as $locale) {
                    $indexName = "idx_{$tableName}_{$jsonColumnName}_{$locale}";
                    try {
                        $exists = DB::select("SHOW INDEX FROM `{$tableName}` WHERE Key_name = ?", [$indexName]);
                        if (!empty($exists)) {
                            $table->dropIndex($indexName);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Could not drop index {$indexName}: " . $e->getMessage());
                    }
                }
            });

            // --- Step 4: Drop the JSON column ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $jsonColumnName) {
                if (Schema::hasColumn($tableName, $jsonColumnName)) {
                    $table->dropColumn($jsonColumnName);
                }
            });

            // --- Step 5: Rename _en column back to original name ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $jsonColumnName, $tempColumnName) {
                if (Schema::hasColumn($tableName, $tempColumnName)) {
                    $table->renameColumn($tempColumnName, $jsonColumnName);
                }
            });

            // --- Step 6: Restore original index ---
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columnName) {
                if (Schema::hasColumn($tableName, $columnName)) {
                    try {
                        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($tableName);
                        if (!isset($indexes[$columnName])) {
                            $table->index($columnName);
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to restore index for {$columnName}: " . $e->getMessage());
                    }
                }
            });

            // --- Step 7: Restore original nullability ---
            if (Schema::hasColumn($tableName, $jsonColumnName)) {
                Schema::table($tableName, function (Blueprint $table) use ($jsonColumnName, $originalTypeMethod, $originalTypeArgs, $wasNullable) {
                    try {
                        $table->{$originalTypeMethod}($jsonColumnName, ...$originalTypeArgs)
                            ->nullable($wasNullable)
                            ->change();
                    } catch (\Exception $e) {
                        Log::error("Failed to restore nullability: " . $e->getMessage());
                    }
                });
            }

            // --- Re-add task_rules.uid if needed ---
            if ($tableName === 'task_rules') {
                $this->handleTaskRulesUid('add');
            }

            Log::info("Finished reverting migration for {$tableName}.{$columnName}");
        }
    }

    /**
     * Helper to handle the task_rules.uid special case
     */
    private function handleTaskRulesUid(string $action): void
    {
        try {
            if ($action === 'drop') {
                if (Schema::hasColumn('task_rules', 'uid')) {
                    DB::statement("ALTER TABLE `task_rules` DROP COLUMN `uid`");
                    Log::info("Dropped 'uid' column from task_rules.");
                }
            } else if ($action === 'add') {
                if (!Schema::hasColumn('task_rules', 'uid')) {
                    DB::statement("ALTER TABLE `task_rules` {$this->taskRulesUidDefinition}");
                    Log::info("Added 'uid' column to task_rules.");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to {$action} 'uid' column on task_rules: " . $e->getMessage());
            throw $e;
        }
    }
};