<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassifierTypeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('classifier_type')->insertOrIgnore([
            ['code' => 'ABS', 'type' => json_encode(['en' => 'Abstract']), 'display_order' => 127],
            ['code' => 'AGR', 'type' => json_encode(['en' => 'Agreement']), 'display_order' => 127],
            ['code' => 'BU', 'type' => json_encode(['en' => 'Business Unit']), 'display_order' => 127],
            ['code' => 'DESC', 'type' => json_encode(['en' => 'Description']), 'display_order' => 127],
            ['code' => 'EVAL', 'type' => json_encode(['en' => 'Evaluation']), 'display_order' => 127],
            ['code' => 'IMG', 'type' => json_encode(['en' => 'Image']), 'display_order' => 127],
            ['code' => 'IPC', 'type' => json_encode(['en' => 'Int. Pat. Class.']), 'display_order' => 127],
            ['code' => 'KW', 'type' => json_encode(['en' => 'Keyword']), 'display_order' => 127],
            ['code' => 'LNK', 'type' => json_encode(['en' => 'Link']), 'display_order' => 127],
            ['code' => 'LOC', 'type' => json_encode(['en' => 'Location']), 'display_order' => 127],
            ['code' => 'ORG', 'type' => json_encode(['en' => 'Organization']), 'display_order' => 127],
            ['code' => 'PA', 'type' => json_encode(['en' => 'Prior Art']), 'display_order' => 127],
            ['code' => 'PROD', 'type' => json_encode(['en' => 'Product']), 'display_order' => 127],
            ['code' => 'PROJ', 'type' => json_encode(['en' => 'Project']), 'display_order' => 127],
            ['code' => 'TECH', 'type' => json_encode(['en' => 'Technology']), 'display_order' => 127],
            ['code' => 'TIT', 'type' => json_encode(['en' => 'Title']), 'display_order' => 127],
            ['code' => 'TITAL', 'type' => json_encode(['en' => 'Alt. Title']), 'display_order' => 127],
            ['code' => 'TITEN', 'type' => json_encode(['en' => 'English Title']), 'display_order' => 127],
            ['code' => 'TITOF', 'type' => json_encode(['en' => 'Official Title']), 'display_order' => 127],
            ['code' => 'TM', 'type' => json_encode(['en' => 'Trademark']), 'display_order' => 127],
            ['code' => 'TMCL', 'type' => json_encode(['en' => 'Class (TM)']), 'display_order' => 127],
            ['code' => 'TMTYP', 'type' => json_encode(['en' => 'Type (TM)']), 'display_order' => 127],
        ]);
    }
}