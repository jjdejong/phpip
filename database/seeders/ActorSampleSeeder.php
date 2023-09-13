<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Actor;

class ActorSampleSeeder extends Seeder
{
    public function run()
    {
        require 'actor-sample.php';
        Schema::disableForeignKeyConstraints();
        Actor::insertOrIgnore($actor);
        Schema::enableForeignKeyConstraints();
    }
}
