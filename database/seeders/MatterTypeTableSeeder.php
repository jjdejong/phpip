<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MatterTypeTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('matter_type')->insertOrIgnore([

            [
                'code' => 'CIP',
                'type' => 'Continuation in Part',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'CNT',
                'type' => 'Continuation',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'DIV',
                'type' => 'Divisional',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PRO',
                'type' => 'Provisional',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'code' => 'REI',
                'type' => 'Reissue',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'REX',
                'type' => 'Re-examination',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
