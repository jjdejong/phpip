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
                'category' => 'Agreement',
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'DSG',
                'ref_prefix' => 'DSG',
                'category' => 'Design',
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'FTO',
                'ref_prefix' => 'OPI',
                'category' => 'Freedom to Operate',
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'LTG',
                'ref_prefix' => 'LTG',
                'category' => 'Litigation',
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OP',
                'ref_prefix' => 'OPP',
                'category' => 'Opposition (patent]',
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OPI',
                'ref_prefix' => 'OPI',
                'category' => 'Opinion',
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OTH',
                'ref_prefix' => 'OTH',
                'category' => 'Others',
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PAT',
                'ref_prefix' => 'PAT',
                'category' => 'Patent',
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'SO',
                'ref_prefix' => 'PAT',
                'category' => 'Soleau Envelop',
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'SR',
                'ref_prefix' => 'SR-',
                'category' => 'Search',
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TM',
                'ref_prefix' => 'TM-',
                'category' => 'Trademark',
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TMOP',
                'ref_prefix' => 'TOP',
                'category' => 'Opposition (TM]',
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TS',
                'ref_prefix' => 'TS-',
                'category' => 'Trade Secret',
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'UC',
                'ref_prefix' => 'PAT',
                'category' => 'Utility Certificate',
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'UM',
                'ref_prefix' => 'PAT',
                'category' => 'Utility Model',
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'WAT',
                'ref_prefix' => 'WAT',
                'category' => 'Watch',
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
