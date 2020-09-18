<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTables11 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('actor', function (Blueprint $table) {
        $table->dropColumn('pref_language');
      });
      Schema::table('task_rules', function (Blueprint $table) {
        $table->dropColumn('uid');
        $table->dropColumn('use_parent');
      });
      Schema::table('task_rules', function (Blueprint $table) {
        $table->string('uid', 32)
        ->unique()
        ->virtualAs("md5(concat(task, trigger_event, clear_task, delete_task, for_category, ifnull(for_country, 'c'), ifnull(for_origin, 'o'), ifnull(for_type, 't'), days, months, years, recurring, ifnull(abort_on, 'a'), ifnull(condition_event, 'c'), use_priority, ifnull(detail, 'd')))");
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('actor', function (Blueprint $table) {
        $table->integer('pref_language')->after('warn')->unsigned();
      });
      Schema::table('task_rules', function (Blueprint $table) {
        $table->boolean('use_parent')->after('condition_event')->default(0);
      });
    }
}
