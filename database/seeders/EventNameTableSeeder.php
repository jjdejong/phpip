<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\EventName;

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
        EventName::insertOrIgnore($event_name);
    }
}
