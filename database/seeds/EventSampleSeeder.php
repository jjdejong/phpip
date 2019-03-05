<?php

use Illuminate\Database\Seeder;

class EventSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'event-sample.php';
        App\Event::insert($event);
    }
}
