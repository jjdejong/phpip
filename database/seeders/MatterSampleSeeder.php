<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Matter;
use App\ActorPivot;

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
