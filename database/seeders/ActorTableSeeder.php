<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Actor;

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
        Actor::insertOrIgnore($actor);
    }
}
