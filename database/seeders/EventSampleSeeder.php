<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Event;

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
        Event::insertOrIgnore($event);
    }
}
