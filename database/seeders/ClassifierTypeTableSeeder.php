<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\ClassifierType;

class ClassifierTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'classifier_type.php';
        ClassifierType::insertOrIgnore($classifier_type);
    }
}
