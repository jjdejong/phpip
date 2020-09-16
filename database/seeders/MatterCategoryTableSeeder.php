<?php

namespace Database\Seeders;

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
        $table = new  App\Category();
        foreach($matter_category as $line) {
            $table->insert($line);
        }
        Schema::enableForeignKeyConstraints();
    }
}
