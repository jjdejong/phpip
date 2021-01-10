<?php

use Illuminate\Database\Migrations\Migration;

class UpdateCategoryProvisional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('matter_type')->insertOrIgnore(['code' => 'PRO', 'type' => 'Provisional']);
        DB::table('matter')->where('category_code', 'PRO')->update(['category_code' => 'PAT', 'type_code' => 'PRO']);
        DB::table('task_rules')->where(['for_category' => 'PRO', 'task' => 'PRID'])->delete();
        DB::table('task_rules')->where('for_category', 'PRO')->update(['for_category' => 'PAT', 'for_type' => 'PRO']);
        DB::table('matter_category')->where('code', 'PRO')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
