<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MatterTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('matter_type')->insertOrIgnore(array(

            array(
                'code' => 'CIP',
                'type' => 'Continuation in Part',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'CNT',
                'type' => 'Continuation',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'DIV',
                'type' => 'Divisional',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'PRO',
                'type' => 'Provisional',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            
            array(
                'code' => 'REI',
                'type' => 'Reissue',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'REX',
                'type' => 'Re-examination',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
