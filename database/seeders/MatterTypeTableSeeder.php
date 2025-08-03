<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatterTypeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('matter_type')->insertOrIgnore([
            ['code' => 'CIP', 'type' => json_encode(['en' => 'Continuation in Part'])],
            ['code' => 'CNT', 'type' => json_encode(['en' => 'Continuation'])],
            ['code' => 'DIV', 'type' => json_encode(['en' => 'Divisional'])],
            ['code' => 'PRO', 'type' => json_encode(['en' => 'Provisional'])],
            ['code' => 'REI', 'type' => json_encode(['en' => 'Reissue'])],
            ['code' => 'REX', 'type' => json_encode(['en' => 'Re-examination'])],
        ]);
    }
}