<?php

use Illuminate\Database\Seeder;

class TemplateCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $categories = [
        [
        'category' => 'General'
        ]
      ];
          DB::table('template_categories')->insert($categories);
    }
}
