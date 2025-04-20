<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatterTypeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('matter_type')->insertOrIgnore([

            [
                'code' => 'CIP',
                'type' => json_encode('{"type_en": "Continuation in Part"}'),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'CNT',
                'type' => json_encode('{"type_en": "Continuation"}'),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'DIV',
                'type' => json_encode('{"type_en": "Divisional"}'),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PRO',
                'type' => json_encode('{"type_en": "Provisional"}'),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'REI',
                'type' => json_encode('{"type_en": "Reissue"}'),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'REX',
                'type' => json_encode('{"type_en": "Re-examination"}'),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
