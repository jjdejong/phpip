<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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
        Schema::disableForeignKeyConstraints();
        App\Actor::insert($actor);
        Schema::enableForeignKeyConstraints();
    }
}
