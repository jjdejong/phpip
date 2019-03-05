<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountryTableSeeder::class);
        $this->call(matterCategoryTableSeeder::class);
        $this->call(ClassifierTypeTableSeeder::class);
        $this->call(ActorRoleTableSeeder::class);
        $this->call(ActorTableSeeder::class);
        $this->call(EventNameTableSeeder::class);
        $this->call(MatterTypeTableSeeder::class);
        $this->call(TaskRulesTableSeeder::class);
    }
}
