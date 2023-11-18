<?php

namespace Database\Seeders;

use App\ActorPivot;
use App\Matter;
use Illuminate\Database\Seeder;

class MatterSampleSeeder extends Seeder
{
    public function run()
    {
        require 'matter-sample.php';
        Matter::insertOrIgnore($matter);
        require 'matter_actor_lnk-sample.php';
        ActorPivot::insertOrIgnore($matter_actor_lnk);
    }
}
