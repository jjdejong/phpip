<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
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
        $tableName = 'country';
        $columnName = 'name';
        
        Log::info("Starting migration for country names to JSON format");

        // Step 1: Create temporary JSON column
        Log::info("Adding temporary JSON column");
        Schema::table($tableName, function (Blueprint $table) use ($columnName) {
            $table->json('name_json')->after($columnName);
        });

        // Step 2: Migrate data to JSON format
        Log::info("Migrating country names to JSON format");
        $countries = DB::table($tableName)->get();
        foreach ($countries as $country) {
            $names = [
                'en' => $country->name,
                'fr' => $country->name_FR,
                'de' => $country->name_DE
            ];
            
            // Filter out null values
            $names = array_filter($names, fn($value) => !is_null($value));
            
            DB::table('country')
                ->where('iso', $country->iso)
                ->update(['name_json' => json_encode($names, JSON_UNESCAPED_UNICODE)]);
        }

        // Step 3: Drop old columns and rename JSON column
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('name_FR');
            $table->dropColumn('name_DE');
            $table->renameColumn('name_json', 'name');
        });

        // Step 5: Create indexes for each locale
        $isMariaDB = str_contains(DB::selectOne('select version() as v')->v ?? '', 'MariaDB');
        foreach ($this->localesToIndex as $locale) {
            $indexName = "idx_{$tableName}_{$columnName}_{$locale}";
            Log::info("Creating index {$indexName} for locale {$locale}");

            if ($isMariaDB) {
                $virtualColumn = "{$columnName}_{$locale}";
                
                // Create virtual column
                Schema::table($tableName, function (Blueprint $table) use ($virtualColumn, $columnName, $locale, $tableName) {
                    if (!Schema::hasColumn($tableName, $virtualColumn)) {
                        DB::statement("ALTER TABLE `{$tableName}` ADD COLUMN `{$virtualColumn}` VARCHAR(45) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(`{$columnName}`, '$.{$locale}'))) STORED");
                    }
                });

                // Create index on virtual column
                DB::statement("CREATE INDEX `{$indexName}` ON `{$tableName}` (`{$virtualColumn}`)");
            } else {
                $sqlIndex = "ALTER TABLE `{$tableName}` ADD INDEX `{$indexName}` ((CAST(JSON_UNQUOTE(JSON_EXTRACT(`{$columnName}`, '$.{$locale}')) AS CHAR(45)) COLLATE {$this->jsonIndexCollation}))";
                DB::statement($sqlIndex);
            }
        }

        // Step 6: Nothing to clean up since we already dropped the old columns

        // Drop the existing view
        DB::statement('DROP VIEW IF EXISTS renewal_list');

        // Recreate the view with JSON extraction
        // Note: This is a simplified version - you may need to adjust based on the full view definition
        DB::statement("CREATE VIEW renewal_list AS
            SELECT 
                `task`.`id` AS `id`,
                `task`.`detail` AS `detail`,
                `task`.`due_date` AS `due_date`,
                `task`.`done` AS `done`,
                `task`.`done_date` AS `done_date`,
                `event`.`matter_id` AS `matter_id`,
                `fees`.`cost` AS `cost`,
                `fees`.`fee` AS `fee`,
                `fees`.`cost_reduced` AS `cost_reduced`,
                `fees`.`fee_reduced` AS `fee_reduced`,
                `fees`.`cost_sup` AS `cost_sup`,
                `fees`.`fee_sup` AS `fee_sup`,
                `fees`.`fee_sup_reduced` AS `fee_sup_reduced`,
                `fees`.`cost_sup_reduced` AS `cost_sup_reduced`,
                `task`.`trigger_id` AS `trigger_id`,
                `matter`.`category_code` AS `category`,
                `matter`.`caseref` AS `caseref`,
                `matter`.`suffix` AS `suffix`,
                `matter`.`country` AS `country`,
                JSON_UNQUOTE(JSON_EXTRACT(`mcountry`.`name`, '$.fr')) AS `country_FR`,
                JSON_UNQUOTE(JSON_EXTRACT(`mcountry`.`name`, '$.de')) AS `country_DE`,
                `matter`.`origin` AS `origin`,
                `matter`.`type_code` AS `type_code`,
                `matter`.`idx` AS `idx`,
                (SELECT 1 FROM `classifier` `sme`
                    WHERE
                        ((`matter`.`id` = `sme`.`matter_id`)
                            AND (`sme`.`type_code` = 'SME'))) AS `sme_status`,
                `event`.`code` AS `event_name`,
                `event`.`event_date` AS `event_date`,
                `event`.`detail` AS `number`,
                GROUP_CONCAT(`pa_app`.`display_name`
                    SEPARATOR ',') AS `applicant_dn`,
                `pa_cli`.`display_name` AS `client_dn`,
                `pa_cli`.`ren_discount` AS `discount`,
                `pmal_cli`.`actor_id` AS `client_id`,
                `pa_cli`.`email` AS `email`,
                IFNULL(`task`.`assigned_to`,
                        `matter`.`responsible`) AS `responsible`,
                `cla`.`value` AS `title`,
                `ev`.`detail` AS `pub_num`,
                `task`.`step` AS `step`,
                `task`.`grace_period` AS `grace_period`,
                `task`.`invoice_step` AS `invoice_step`
            FROM
                ((((((((((`matter`
                LEFT JOIN `matter_actor_lnk` `pmal_app` ON (((IFNULL(`matter`.`container_id`, `matter`.`id`) = `pmal_app`.`matter_id`)
                    AND (`pmal_app`.`role` = 'APP'))))
                LEFT JOIN `actor` `pa_app` ON ((`pa_app`.`id` = `pmal_app`.`actor_id`)))
                LEFT JOIN `matter_actor_lnk` `pmal_cli` ON (((IFNULL(`matter`.`container_id`, `matter`.`id`) = `pmal_cli`.`matter_id`)
                    AND (`pmal_cli`.`role` = 'CLI'))))
                LEFT JOIN `country` `mcountry` ON ((`mcountry`.`iso` = `matter`.`country`)))
                LEFT JOIN `actor` `pa_cli` ON ((`pa_cli`.`id` = `pmal_cli`.`actor_id`)))
                JOIN `event` ON ((`matter`.`id` = `event`.`matter_id`)))
                LEFT JOIN `event` `ev` ON (((`matter`.`id` = `ev`.`matter_id`)
                    AND (`ev`.`code` = 'PUB'))))
                JOIN `task` ON ((`task`.`trigger_id` = `event`.`id`)))
                LEFT JOIN `classifier` `cla` ON (((IFNULL(`matter`.`container_id`, `matter`.`id`) = `cla`.`matter_id`)
                    AND (`cla`.`type_code` = 'TITOF'))))
                LEFT JOIN `fees` ON (((`fees`.`for_country` = `matter`.`country`)
                    AND (`fees`.`for_category` = `matter`.`category_code`)
                    AND (`fees`.`qt` = `task`.`detail`))))
            WHERE
                ((`task`.`code` = 'REN')
                    AND (`matter`.`dead` = 0))
            GROUP BY `task`.`id`
        ");

        Log::info("Completed migration for country names");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = 'country';
        $columnName = 'name';
        $tempColumnName = $columnName . '_en';

        Log::info("Starting rollback of country names from JSON format");

        // Step 1: Create separate language columns
        Schema::table($tableName, function (Blueprint $table) {
            $table->string('name_temp', 45)->nullable()->after('iso');
            $table->string('name_FR', 45)->nullable()->after('name_temp');
            $table->string('name_DE', 45)->nullable()->after('name_FR');
        });

        // Step 2: Extract data from JSON
        Log::info("Extracting data from JSON to separate columns");
        $countries = DB::table($tableName)->get();
        foreach ($countries as $country) {
            $names = json_decode($country->$columnName, true) ?? [];
            
            DB::table('country')
                ->where('iso', $country->iso)
                ->update([
                    'name_temp' => $names['en'] ?? null,
                    'name_FR' => $names['fr'] ?? null,
                    'name_DE' => $names['de'] ?? null
                ]);
        }

        // Step 3: Drop indexes and virtual columns
        $isMariaDB = str_contains(DB::selectOne('select version() as v')->v ?? '', 'MariaDB');
        foreach ($this->localesToIndex as $locale) {
            $indexName = "idx_{$tableName}_{$columnName}_{$locale}";
            $virtualColumn = "{$columnName}_{$locale}";

            if ($isMariaDB && Schema::hasColumn($tableName, $virtualColumn)) {
                DB::statement("DROP INDEX IF EXISTS `{$indexName}` ON `{$tableName}`");
                DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `{$virtualColumn}`");
            } else {
                try {
                    $exists = DB::select("SHOW INDEX FROM `{$tableName}` WHERE Key_name = ?", [$indexName]);
                    if (!empty($exists)) {
                        Schema::table($tableName, fn(Blueprint $table) => $table->dropIndex($indexName));
                    }
                } catch (\Exception $e) {
                    Log::warning("Could not drop index {$indexName}: " . $e->getMessage());
                }
            }
        }

        // Step 4: Drop JSON column and rename temp column
        Schema::table($tableName, function (Blueprint $table) use ($columnName) {
            $table->dropColumn($columnName);
            $table->renameColumn('name_temp', 'name');
        });

        // Step 5: Restore index on name column
        Schema::table($tableName, function (Blueprint $table) use ($columnName, $tableName) {
            try {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($tableName);
                if (!isset($indexes[$columnName])) {
                    $table->index($columnName);
                }
            } catch (\Exception $e) {
                Log::error("Failed to restore index for {$columnName}: " . $e->getMessage());
            }
        });

        // Drop the JSON-based view
        DB::statement('DROP VIEW IF EXISTS renewal_list');

        // Recreate the original view with separate columns
        // This should match the original view definition from the schema
        DB::statement("CREATE VIEW renewal_list AS
            SELECT 
                `task`.`id` AS `id`,
                `task`.`detail` AS `detail`,
                `task`.`due_date` AS `due_date`,
                `task`.`done` AS `done`,
                `task`.`done_date` AS `done_date`,
                `event`.`matter_id` AS `matter_id`,
                `fees`.`cost` AS `cost`,
                `fees`.`fee` AS `fee`,
                `fees`.`cost_reduced` AS `cost_reduced`,
                `fees`.`fee_reduced` AS `fee_reduced`,
                `fees`.`cost_sup` AS `cost_sup`,
                `fees`.`fee_sup` AS `fee_sup`,
                `fees`.`fee_sup_reduced` AS `fee_sup_reduced`,
                `fees`.`cost_sup_reduced` AS `cost_sup_reduced`,
                `task`.`trigger_id` AS `trigger_id`,
                `matter`.`category_code` AS `category`,
                `matter`.`caseref` AS `caseref`,
                `matter`.`suffix` AS `suffix`,
                `matter`.`country` AS `country`,
                `mcountry`.`name_FR` AS `country_FR`,
                `matter`.`origin` AS `origin`,
                `matter`.`type_code` AS `type_code`,
                `matter`.`idx` AS `idx`,
                (SELECT 1 FROM `classifier` `sme`
                    WHERE
                        ((`matter`.`id` = `sme`.`matter_id`)
                            AND (`sme`.`type_code` = 'SME'))) AS `sme_status`,
                `event`.`code` AS `event_name`,
                `event`.`event_date` AS `event_date`,
                `event`.`detail` AS `number`,
                GROUP_CONCAT(`pa_app`.`display_name`
                    SEPARATOR ',') AS `applicant_dn`,
                `pa_cli`.`display_name` AS `client_dn`,
                `pa_cli`.`ren_discount` AS `discount`,
                `pmal_cli`.`actor_id` AS `client_id`,
                `pa_cli`.`email` AS `email`,
                IFNULL(`task`.`assigned_to`,
                        `matter`.`responsible`) AS `responsible`,
                `cla`.`value` AS `title`,
                `ev`.`detail` AS `pub_num`,
                `task`.`step` AS `step`,
                `task`.`grace_period` AS `grace_period`,
                `task`.`invoice_step` AS `invoice_step`
            FROM
                ((((((((((`matter`
                LEFT JOIN `matter_actor_lnk` `pmal_app` ON (((IFNULL(`matter`.`container_id`, `matter`.`id`) = `pmal_app`.`matter_id`)
                    AND (`pmal_app`.`role` = 'APP'))))
                LEFT JOIN `actor` `pa_app` ON ((`pa_app`.`id` = `pmal_app`.`actor_id`)))
                LEFT JOIN `matter_actor_lnk` `pmal_cli` ON (((IFNULL(`matter`.`container_id`, `matter`.`id`) = `pmal_cli`.`matter_id`)
                    AND (`pmal_cli`.`role` = 'CLI'))))
                LEFT JOIN `country` `mcountry` ON ((`mcountry`.`iso` = `matter`.`country`)))
                LEFT JOIN `actor` `pa_cli` ON ((`pa_cli`.`id` = `pmal_cli`.`actor_id`)))
                JOIN `event` ON ((`matter`.`id` = `event`.`matter_id`)))
                LEFT JOIN `event` `ev` ON (((`matter`.`id` = `ev`.`matter_id`)
                    AND (`ev`.`code` = 'PUB'))))
                JOIN `task` ON ((`task`.`trigger_id` = `event`.`id`)))
                LEFT JOIN `classifier` `cla` ON (((IFNULL(`matter`.`container_id`, `matter`.`id`) = `cla`.`matter_id`)
                    AND (`cla`.`type_code` = 'TITOF'))))
                LEFT JOIN `fees` ON (((`fees`.`for_country` = `matter`.`country`)
                    AND (`fees`.`for_category` = `matter`.`category_code`)
                    AND (`fees`.`qt` = `task`.`detail`))))
            WHERE
                ((`task`.`code` = 'REN')
                    AND (`matter`.`dead` = 0))
            GROUP BY `task`.`id`
        ");
    }
};
