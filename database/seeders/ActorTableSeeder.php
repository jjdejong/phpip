<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ActorTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('actor')->insertOrIgnore(array(

            array(
                'id' => 1,
                'name' => 'Client handled',
                'first_name' => null,
                'display_name' => 'CLIENT',
                'login' => null,
                'password' => null,
                'default_role' => 'ANN',
                'function' => null,
                'parent_id' => null,
                'company_id' => null,
                'site_id' => null,
                'phy_person' => 0,
                'nationality' => null,
                'language' => null,
                'small_entity' => 0,
                'address' => null,
                'country' => null,
                'address_mailing' => null,
                'country_mailing' => null,
                'address_billing' => null,
                'country_billing' => null,
                'email' => null,
                'phone' => null,
                'legal_form' => null,
                'registration_no' => null,
                'warn' => 0,
                'ren_discount' => 0.0,
                'notes' => 'DO NOT DELETE - Special actor used for removing renewal tasks that are handled by the client',
                'VAT_number' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'remember_token' => null,
            ),

            array(
                'id' => 2,
                'name' => 'phpIP User',
                'first_name' => null,
                'display_name' => null,
                'login' => 'phpipuser',
                'password' => '$2y$10$auLQHQ3EIsg90hqnQsA1huhks3meaxwfWWEvJtD8R38jzwNN6y3zO',
                'default_role' => 'DBA',
                'function' => null,
                'parent_id' => null,
                'company_id' => null,
                'site_id' => null,
                'phy_person' => 1,
                'nationality' => null,
                'language' => null,
                'small_entity' => 0,
                'address' => null,
                'country' => null,
                'address_mailing' => null,
                'country_mailing' => null,
                'address_billing' => null,
                'country_billing' => null,
                'email' => 'root@localhost',
                'phone' => null,
                'legal_form' => null,
                'registration_no' => null,
                'warn' => 0,
                'ren_discount' => 0.0,
                'notes' => null,
                'VAT_number' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'remember_token' => null,
            ),
        ));
    }
}
