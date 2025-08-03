<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatterCategoryTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('matter_category')->insertOrIgnore([
            ['code' => 'AGR', 'category' => json_encode(['en' => 'Agreement'])],
            ['code' => 'DSG', 'category' => json_encode(['en' => 'Design'])],
            ['code' => 'FTO', 'category' => json_encode(['en' => 'Freedom to Operate'])],
            ['code' => 'LTG', 'category' => json_encode(['en' => 'Litigation'])],
            ['code' => 'OP', 'category' => json_encode(['en' => 'Opposition (patent)'])],
            ['code' => 'OPI', 'category' => json_encode(['en' => 'Opinion'])],
            ['code' => 'OTH', 'category' => json_encode(['en' => 'Others'])],
            ['code' => 'PAT', 'category' => json_encode(['en' => 'Patent'])],
            ['code' => 'SO', 'category' => json_encode(['en' => 'Soleau Envelop'])],
            ['code' => 'SR', 'category' => json_encode(['en' => 'Search'])],
            ['code' => 'TM', 'category' => json_encode(['en' => 'Trademark'])],
            ['code' => 'TMOP', 'category' => json_encode(['en' => 'Opposition (TM)'])],
            ['code' => 'TS', 'category' => json_encode(['en' => 'Trade Secret'])],
            ['code' => 'UC', 'category' => json_encode(['en' => 'Utility Certificate'])],
            ['code' => 'UM', 'category' => json_encode(['en' => 'Utility Model'])],
            ['code' => 'WAT', 'category' => json_encode(['en' => 'Watch'])],
        ]);
    }
}