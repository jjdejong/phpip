<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatterCategoryTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('matter_category')->insertOrIgnore([

            [
                'code' => 'AGR',
                'ref_prefix' => 'AGR',
                'category' => json_encode('{"category_en": "Agreement"}'),
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'DSG',
                'ref_prefix' => 'DSG',
                'category' => json_encode('{"category_en": "Design"}'),
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'FTO',
                'ref_prefix' => 'OPI',
                'category' => json_encode('{"category_en": "Freedom to Operate"}'),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'LTG',
                'ref_prefix' => 'LTG',
                'category' => json_encode('{"category_en": "Litigation"}'),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OP',
                'ref_prefix' => 'OPP',
                'category' => json_encode('{"category_en": "Opposition (patent]"}'),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OPI',
                'ref_prefix' => 'OPI',
                'category' => json_encode('{"category_en": "Opinion"}'),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OTH',
                'ref_prefix' => 'OTH',
                'category' => json_encode('{"category_en": "Others"}'),
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PAT',
                'ref_prefix' => 'PAT',
                'category' => json_encode('{"category_en": "Patent"}'),
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'SO',
                'ref_prefix' => 'PAT',
                'category' => json_encode('{"category_en": "Soleau Envelop"}'),
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'SR',
                'ref_prefix' => 'SR-',
                'category' => json_encode('{"category_en": "Search"}'),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TM',
                'ref_prefix' => 'TM-',
                'category' => json_encode('{"category_en": "Trademark"}'),
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TMOP',
                'ref_prefix' => 'TOP',
                'category' => json_encode('{"category_en": "Opposition (TM]"}'),
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TS',
                'ref_prefix' => 'TS-',
                'category' => json_encode('{"category_en": "Trade Secret"}'),
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'UC',
                'ref_prefix' => 'PAT',
                'category' => json_encode('{"category_en": "Utility Certificate"}'),
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'UM',
                'ref_prefix' => 'PAT',
                'category' => json_encode('{"category_en": "Utility Model"}'),
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'WAT',
                'ref_prefix' => 'WAT',
                'category' => json_encode('{"category_en": "Watch"}'),
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
