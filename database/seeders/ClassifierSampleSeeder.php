<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ClassifierSampleSeeder extends Seeder
{
    public function run()
    {
        require 'classifier-sample.php';
        DB::table('classifier')->insertOrIgnore($classifier);
    }
}
