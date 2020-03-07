<?php

use Illuminate\Database\Seeder;

class TemplateStylesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $styles = [
        [
        'style' => 'Standard',
        'notes' => 'Default style'
        ]
      ];
          DB::table('template_styles')->insert($styles);
    }
}
