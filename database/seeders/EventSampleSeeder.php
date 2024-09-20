<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSampleSeeder extends Seeder
{
    public function run()
    {
        require 'event-sample.php';
        Event::insertOrIgnore($event);
    }
}
