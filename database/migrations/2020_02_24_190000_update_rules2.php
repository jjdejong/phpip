<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRules2 extends Migration
{
    public function up()
    {
        DB::table('task_rules')->where('id', 3)->update(['detail' => 'Clear']);
        DB::table('task_rules')->where('id', 5)->update(['for_origin' => NULL, 'for_country' => NULL, 'detail' => 'Clear']);
        DB::table('task_rules')->where('id', 24)->update(['for_country' => NULL]);
        DB::table('task_rules')->where('id', 25)->update(['detail' => 'Delete']);
        DB::table('task_rules')->where('id', 29)->update(['detail' => 'Clear']);
        DB::table('task_rules')->where('id', 38)->update(['detail' => 'Clear']);
        DB::table('task_rules')->where('id', 1306)->update(['detail' => 'Delete']);
        DB::table('task_rules')->where('id', 1307)->update(['detail' => 'Delete']);
        DB::table('task_rules')->where('id', 1327)->update(['detail' => 'Clear']);
    }

    public function down()
    {
        // Not worth rolling back
    }
}
