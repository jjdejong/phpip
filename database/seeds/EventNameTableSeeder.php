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
        
        $table = new  App\EventName();
        foreach($event_name as $line) {
            $table->insert($line);
        }
    }
}
