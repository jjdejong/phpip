<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Database\Seeders\TranslatedAttributesSeeder;

return new class extends Migration {
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
    private $taskRulesUidDefinition = "ADD COLUMN `uid` varchar(32) COLLATE utf8mb4_0900_ai_ci GENERATED ALWAYS AS (md5(concat(`task`,`trigger_event`,`clear_task`,`delete_task`,`for_category`,ifnull(`for_country`,_utf8mb4'c'),ifnull(`for_origin`,_utf8mb4'o'),ifnull(`for_type`,_utf8mb4't'),`days`,`months`,`years`,`recurring`,ifnull(`abort_on`,_utf8mb4'a'),ifnull(`condition_event`,_utf8mb4'c'),`use_priority`,ifnull(`detail`,_utf8mb4'd')))) VIRTUAL";

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
        $defaultLocale = 'en';
        $isMariaDB = str_contains(DB::selectOne('select version() as v')->v ?? '', 'MariaDB');

        Log::info("Starting migration for translatable attributes to JSON format");
        foreach ($activeAttributes as $tableName => [$columnName, $originalTypeMethod, $originalTypeArgs, $wasNullable]) {
            $tempColumnName = $columnName . $this->tempSuffix;
            $jsonColumnName = $columnName;

            Log::info("Processing table: {$tableName}, column: {$columnName}");

            if ($tableName === 'task_rules') $this->handleTaskRulesUid('drop');

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columnName) {
                try {
                    $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($tableName);
                    if (isset($indexes[$columnName])) {
                        Log::info("Dropping index on {$tableName}.{$columnName}");
                        $table->dropIndex($columnName);
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to drop index on {$tableName}.{$columnName}: " . $e->getMessage());
                }
            });

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $jsonColumnName, $tempColumnName) {
                if (!Schema::hasColumn($tableName, $tempColumnName) && Schema::hasColumn($tableName, $jsonColumnName)) {
                    Log::info("Renaming column {$jsonColumnName} to {$tempColumnName} in {$tableName}");
                    $table->renameColumn($jsonColumnName, $tempColumnName);
                }
            });

            Schema::table($tableName, function (Blueprint $table) use ($jsonColumnName, $tempColumnName, $tableName) {
                if (!Schema::hasColumn($tableName, $jsonColumnName)) {
                    Log::info("Adding JSON column {$jsonColumnName} to {$tableName}");
                    $table->json($jsonColumnName)->nullable()->after($tempColumnName);
                } else {
                    Log::info("Modifying column {$jsonColumnName} to JSON type in {$tableName}");
                    DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `{$jsonColumnName}` JSON NULL");
                }
            });

            Log::info("Migrating data from {$tempColumnName} to JSON format in {$tableName}");
            DB::update("UPDATE `{$tableName}` SET `{$jsonColumnName}` = JSON_OBJECT(?, `{$tempColumnName}`) WHERE `{$tempColumnName}` IS NOT NULL AND `{$jsonColumnName}` IS NULL", [$defaultLocale]);

            foreach ($this->localesToIndex as $locale) {
                $indexName = "idx_{$tableName}_{$jsonColumnName}_{$locale}";
                Log::info("Creating index {$indexName} for locale {$locale}");

                if ($isMariaDB) {
                    $virtualColumn = "{$columnName}_{$locale}";
                    Schema::table($tableName, function (Blueprint $table) use ($locale, $virtualColumn, $originalTypeArgs, $tableName) {
                        if (!Schema::hasColumn($table->getTable(), $virtualColumn)) {
                            Log::info("Adding virtual column {$virtualColumn} to {$tableName}");
                            DB::statement("ALTER TABLE `{$table->getTable()}` ADD COLUMN `{$virtualColumn}` VARCHAR({$originalTypeArgs[0]}) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(`{$table->getTable()}`.`{$this->translatableAttributes[$table->getTable()][0]}`, '$.{$locale}'))) STORED");
                        }
                    });

                    Schema::table($tableName, function (Blueprint $table) use ($virtualColumn, $indexName, $tableName) {
                        if (!Schema::hasColumn($table->getTable(), $virtualColumn)) return;
                        Log::info("Creating index on virtual column {$virtualColumn} in {$tableName}");
                        DB::statement("CREATE INDEX `{$indexName}` ON `{$table->getTable()}` (`{$virtualColumn}`)");
                    });
                } else {
                    $castType = "CHAR({$originalTypeArgs[0]})";
                    $sqlIndex = "ALTER TABLE `{$tableName}` ADD INDEX `{$indexName}` ((CAST(JSON_UNQUOTE(JSON_EXTRACT(`{$jsonColumnName}`, '$.{$locale}')) AS {$castType}) COLLATE {$this->jsonIndexCollation}))";
                    $exists = DB::select("SHOW INDEX FROM `{$tableName}` WHERE Key_name = ?", [$indexName]);
                    if (empty($exists)) {
                        Log::info("Creating JSON extract index {$indexName} in {$tableName}");
                        DB::statement($sqlIndex);
                    }
                }
            }

            Schema::table($tableName, function (Blueprint $table) use ($tempColumnName, $tableName) {
                if (Schema::hasColumn($table->getTable(), $tempColumnName)) {
                    Log::info("Dropping temporary column {$tempColumnName} from {$tableName}");
                    $table->dropColumn($tempColumnName);
                }
            });

            if ($tableName === 'task_rules') $this->handleTaskRulesUid('add');
            Log::info("Completed migration for {$tableName}.{$columnName}");
        }

        Log::info("Running TranslatedAttributesSeeder");
        (new TranslatedAttributesSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $activeAttributes = array_filter($this->translatableAttributes);
        if (empty($activeAttributes)) return;

        $defaultLocale = 'en';
        $isMariaDB = str_contains(DB::selectOne('select version() as v')->v ?? '', 'MariaDB');

        Log::info("Starting rollback of translatable attributes from JSON format");

        foreach (array_reverse($activeAttributes) as $tableName => [$columnName, $originalTypeMethod, $originalTypeArgs, $wasNullable]) {
            $tempColumnName = $columnName . $this->tempSuffix;
            $jsonColumnName = $columnName;

            Log::info("Processing table: {$tableName}, column: {$columnName}");

            if ($tableName === 'task_rules') $this->handleTaskRulesUid('drop');

            // Recreate temp column
            Schema::table($tableName, function (Blueprint $table) use ($tempColumnName, $originalTypeMethod, $originalTypeArgs, $jsonColumnName) {
                if (!Schema::hasColumn($table->getTable(), $tempColumnName)) {
                    Log::info("Creating temporary column {$tempColumnName}");
                    $table->{$originalTypeMethod}($tempColumnName, ...$originalTypeArgs)->nullable()->after($jsonColumnName);
                }
            });

            // Migrate data back to temp column 
            Log::info("Migrating JSON data back to temporary column in {$tableName}");
            DB::update("UPDATE `{$tableName}` SET `{$tempColumnName}` = JSON_UNQUOTE(JSON_EXTRACT(`{$jsonColumnName}`, '$.{$defaultLocale}')) WHERE `{$jsonColumnName}` IS NOT NULL AND JSON_CONTAINS_PATH(`{$jsonColumnName}`, 'one', '$.{$defaultLocale}') = 1 AND `{$tempColumnName}` IS NULL");

            // Drop indexes and virtual columns
            foreach ($this->localesToIndex as $locale) {
                $indexName = "idx_{$tableName}_{$jsonColumnName}_{$locale}";
                $virtualColumn = "{$columnName}_{$locale}";

                if ($isMariaDB && Schema::hasColumn($tableName, $virtualColumn)) {
                    Log::info("Dropping index and virtual column for locale {$locale} in {$tableName}");
                    DB::statement("DROP INDEX IF EXISTS `{$indexName}` ON `{$tableName}`");
                    DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `{$virtualColumn}`");
                } else {
                    try {
                        $exists = DB::select("SHOW INDEX FROM `{$tableName}` WHERE Key_name = ?", [$indexName]);
                        if (!empty($exists)) {
                            Log::info("Dropping index {$indexName} in {$tableName}");
                            Schema::table($tableName, fn(Blueprint $table) => $table->dropIndex($indexName));
                        }
                    } catch (\Exception $e) {
                        Log::warning("Could not drop index {$indexName}: " . $e->getMessage());
                    }
                }
            }

            // Drop JSON column
            if (Schema::hasColumn($tableName, $jsonColumnName)) {
                Log::info("Dropping JSON column {$jsonColumnName} from {$tableName}");
                Schema::table($tableName, fn(Blueprint $table) => $table->dropColumn($jsonColumnName));
            }

            // Rename temp column back to original
            if (Schema::hasColumn($tableName, $tempColumnName)) {
                Log::info("Renaming temporary column back to {$jsonColumnName} in {$tableName}");
                Schema::table($tableName, fn(Blueprint $table) => $table->renameColumn($tempColumnName, $jsonColumnName));
            }

            // Restore index
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                try {
                    $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table->getTable());
                    if (!isset($indexes[$columnName])) {
                        Log::info("Restoring index on {$columnName}");
                        $table->index($columnName);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to restore index for {$columnName}: " . $e->getMessage());
                }
            });

            // Restore nullability
            if (Schema::hasColumn($tableName, $jsonColumnName)) {
                Log::info("Restoring nullability constraints for {$jsonColumnName} in {$tableName}");
                Schema::table($tableName, function (Blueprint $table) use ($jsonColumnName, $originalTypeMethod, $originalTypeArgs, $wasNullable) {
                    $table->{$originalTypeMethod}($jsonColumnName, ...$originalTypeArgs)->nullable($wasNullable)->change();
                });
            }

            if ($tableName === 'task_rules') $this->handleTaskRulesUid('add');
            Log::info("Completed rollback for {$tableName}.{$columnName}");
        }
        Log::info("Completed rollback of all translatable attributes");
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