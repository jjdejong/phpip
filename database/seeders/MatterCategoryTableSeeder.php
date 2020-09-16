<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Category;

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
		/*$table = new Category();
		foreach($matter_category as $line) {
            $table->insert($line);
		}*/
		Category::insertOrIgnore($matter_category);
		Schema::enableForeignKeyConstraints();
    }
}
