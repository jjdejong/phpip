<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actor', function (Blueprint $table) {
          if (Schema::hasColumn('actor', 'language')) {
            $table->dropColumn('language');
          }
          $table->char('language', 6)->default('en_GB')->after('warn')->comment('Language code in the form ll_CC where ll is the language code and CC is the country code.');
        });
        DB::statement("CREATE OR REPLACE VIEW `users` AS
SELECT
    `actor`.`id` AS `id`,
    `actor`.`name` AS `name`,
    `actor`.`login` AS `login`,
    `actor`.`password` AS `password`,
    `actor`.`default_role` AS `default_role`,
    `actor`.`company_id` AS `company_id`,
    `actor`.`email` AS `email`,
    `actor`.`phone` AS `phone`,
    `actor`.`notes` AS `notes`,
    `actor`.`language` AS `language`,
    `actor`.`creator` AS `creator`,
    `actor`.`created_at` AS `created_at`,
    `actor`.`updated_at` AS `updated_at`,
    `actor`.`updater` AS `updater`,
    `actor`.`remember_token` AS `remember_token`
FROM
    `actor`
WHERE
    (`actor`.`login` IS NOT NULL)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("CREATE OR REPLACE VIEW `users` AS
SELECT
    `actor`.`id` AS `id`,
    `actor`.`name` AS `name`,
    `actor`.`login` AS `login`,
    `actor`.`password` AS `password`,
    `actor`.`default_role` AS `default_role`,
    `actor`.`company_id` AS `company_id`,
    `actor`.`email` AS `email`,
    `actor`.`phone` AS `phone`,
    `actor`.`notes` AS `notes`,
    `actor`.`creator` AS `creator`,
    `actor`.`created_at` AS `created_at`,
    `actor`.`updated_at` AS `updated_at`,
    `actor`.`updater` AS `updater`,
    `actor`.`remember_token` AS `remember_token`
FROM
    `actor`
WHERE
    (`actor`.`login` IS NOT NULL)
        ");
        Schema::table('actor', function (Blueprint $table) {
          if (Schema::hasColumn('actor', 'language')) {
            $table->dropColumn('language');
          }
        });
        //
    }
};
