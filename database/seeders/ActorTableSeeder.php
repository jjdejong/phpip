<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ActorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'actor.php';
        App\Actor::insert($actor);
    }
}
