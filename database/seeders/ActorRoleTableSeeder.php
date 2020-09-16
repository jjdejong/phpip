<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Role;

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
        Role::insertOrIgnore($actor_role);
    }
}
