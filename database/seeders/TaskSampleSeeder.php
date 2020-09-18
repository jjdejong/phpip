<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      require 'task-sample.php';
      \DB::table('task')->insertOrIgnore($task);
    }
}
