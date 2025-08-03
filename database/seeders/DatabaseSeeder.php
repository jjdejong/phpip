<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(CountryTableSeeder::class);
        
        // Base seeders for translatable tables (insert English-only JSON)
        $this->call(EventNameTableSeeder::class);
        $this->call(MatterCategoryTableSeeder::class);
        $this->call(ClassifierTypeTableSeeder::class);
        $this->call(MatterTypeTableSeeder::class);
        $this->call(ActorRoleTableSeeder::class);
        
        // Translation seeder (updates with multi-language JSON)
        $this->call(TranslatedAttributesSeeder::class);
        
        $this->call(ActorTableSeeder::class);
        $this->call(FeesTableSeeder::class);
        $this->call(TemplateClassesTableSeeder::class);
        $this->call(TemplateMembersTableSeeder::class);
    }
}
