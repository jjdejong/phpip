<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Type;

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
        Type::insertOrIgnore($matter_type);
    }
}
