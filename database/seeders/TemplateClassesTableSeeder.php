<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TemplateClassesTableSeeder extends Seeder
{

    public function run()
    {
        \DB::table('template_classes')->insertOrIgnore(array (

            array (
                'id' => 1,
                'name' => 'sys_renewals',
                'notes' => 'Templates used for the renewal management tool',
                'default_role' => NULL,
                'creator' => 'system',
                'updater' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
