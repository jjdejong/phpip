<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignkeysToRuleClassLnk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rule_class_lnk', function (Blueprint $table) {
            $table->foreign('template_class_id')->references('id')->on('template_classes')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('task_rule_id')->references('id')->on('task_rules')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rule_class_lnk', function (Blueprint $table) {
            $table->dropForeign(['template_class_id']);
            $table->dropForeign(['task_rule_id']);
        });
    }
}
