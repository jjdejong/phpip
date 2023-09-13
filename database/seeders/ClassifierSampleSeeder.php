<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassifierSampleSeeder extends Seeder
{
    public function run()
    {
        require 'classifier-sample.php';
        DB::table('classifier')->insertOrIgnore($classifier);
    }
}
