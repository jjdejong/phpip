<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
        App\Rule::insert($task_rules);
    }
}
