<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
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
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `users`");
    }
};
