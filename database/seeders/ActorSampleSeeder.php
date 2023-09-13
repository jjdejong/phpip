<?php

namespace Database\Seeders;

use App\Actor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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
