<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ActorSampleSeeder::class);
        $this->call(MatterSampleSeeder::class);
        $this->call(ClassifierSampleSeeder::class);
        $this->call(EventSampleSeeder::class);
        $this->call(TaskSampleSeeder::class);
    }
}
