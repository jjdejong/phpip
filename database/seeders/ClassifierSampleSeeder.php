<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClassifierSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'classifier-sample.php';
        \DB::table('classifier')->insertOrIgnore($classifier);
    }
}
