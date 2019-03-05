<?php

use Illuminate\Database\Seeder;

class MatterCategoryTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'matter_category.php';
        App\Category::create($matter_category);
    }
}
