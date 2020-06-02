<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTaskTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    
        Schema::table('task', function (Blueprint $table) {
        
        $t = 'task';
        if ( !Schema::hasColumn($t, 'step')) {
            $table->tinyinteger('step')->after('notes')->default(0);
        }
        if ( !Schema::hasColumn($t, 'grace_period')) {
            $table->tinyinteger('grace_period')->after('step')->default(0);
        }
        if ( !Schema::hasColumn($t, 'invoice_step')) {
            $table->tinyinteger('invoice_step')->after('step')->default(0);
        }
        });
        
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $t = 'task';
        if (Schema::hasColumn($t, 'step')) {
          Schema::table($t, function (Blueprint $table) {
            $table->dropColumn('step');
          });
        }
        if (Schema::hasColumn($t, 'invoice_step')) {
          Schema::table($t, function (Blueprint $table) {
            $table->dropColumn('invoice_step');
          });
        }
        if (Schema::hasColumn($t, 'grace_period')) {
          Schema::table($t, function (Blueprint $table) {
            $table->dropColumn('grace_period');
          });
        }
    }
}
