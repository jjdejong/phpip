<?php

use Illuminate\Database\Seeder;

class ActorRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'actor_role.php';
        DB::table('actor_role')->insert($actor_role);        
    }
}
