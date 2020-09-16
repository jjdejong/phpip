<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Rule;

class TaskRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'task_rules.php';
        Rule::insertOrIgnore($task_rules);
    }
}
