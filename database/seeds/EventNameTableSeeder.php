<?php

use Illuminate\Database\Seeder;

class EventNameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'event_name.php';
        App\EventName::create($event_name);
    }
}
