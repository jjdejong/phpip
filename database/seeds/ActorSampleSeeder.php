<?php

use Illuminate\Database\Seeder;

class ActorSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'actor-sample.php';
        App\Actor::insert($actor);
    }
}
