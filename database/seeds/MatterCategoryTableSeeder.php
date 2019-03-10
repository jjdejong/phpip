<?php

use Illuminate\Database\Seeder;

class MatterCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'matter_category.php';
        Schema::disableForeignKeyConstraints();
        App\Category::create($matter_category);
        Schema::enableForeignKeyConstraints();
    }
}
