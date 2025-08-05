<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(CountryTableSeeder::class);
        
        // Translatable table seeders (insert multi-language JSON directly)
        $this->call(EventNameTableSeeder::class);
        $this->call(MatterCategoryTableSeeder::class);
        $this->call(ClassifierTypeTableSeeder::class);
        $this->call(MatterTypeTableSeeder::class);
        $this->call(ActorRoleTableSeeder::class);
        $this->call(TaskRulesTableSeeder::class);
        
        $this->call(ActorTableSeeder::class);
        $this->call(FeesTableSeeder::class);
        $this->call(TemplateClassesTableSeeder::class);
        $this->call(TemplateMembersTableSeeder::class);
    }
}
