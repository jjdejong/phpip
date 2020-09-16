<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MatterTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'matter_type.php';
        App\Type::insert($matter_type);
    }
}
