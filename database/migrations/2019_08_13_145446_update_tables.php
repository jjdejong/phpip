<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTables extends Migration
{
    protected $tables = [
      'actor',
      'actor_role',
      'classifier',
      'classifier_type',
      'classifier_value',
      'event',
      'event_name',
      'matter',
      'matter_actor_lnk',
      'matter_category',
      'matter_type',
      'task',
      'task_rules'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      foreach ($this->tables as $t) {
        if (Schema::hasColumn($t, 'updated')) {
          Schema::table($t, function (Blueprint $table) use ($t) {
            $table->dateTime('created_at')->after('creator')->nullable();
            $table->dateTime('updated')->nullable()->change();
            $table->renameColumn('updated', 'updated_at');
            if ($t == 'actor_role') {
              if (Schema::hasColumn($t, 'box')) {
                $table->dropColumn(['box', 'box_color']);
              }
        			$table->boolean('shareable')->default(0)->comment('Indicates whether actors with this role are shareable by default with all matters of the same family')->change();
        			$table->boolean('show_ref')->default(0)->change();
        			$table->boolean('show_company')->default(0)->change();
        			$table->boolean('show_rate')->default(0)->change();
        			$table->boolean('show_date')->default(0)->change();
            }
            if ($t == 'actor') {
              $table->boolean('phy_person')->default(1)->comment('Physical person or not')->change();
              $table->boolean('small_entity')->default(0)->comment('Small entity status used in a few countries (FR, US)')->change();
              $table->string('address', 256)->comment('Main address: street, zip and city')->nullable()->change();
              $table->string('address_mailing', 256)->comment('Mailing address: street, zip and city')->nullable()->change();
              $table->string('address_billing', 256)->comment('Billing address: street, zip and city')->nullable()->change();
              $table->boolean('warn')->default(0)->comment('The actor will be displayed in red in the matter view when set')->change();
            }
          });
        }
      }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      foreach ($this->tables as $t) {
        if (Schema::hasColumn($t, 'updated_at')) {
          Schema::table($t, function (Blueprint $table) {
            $table->renameColumn('updated_at', 'updated');
            $table->dropColumn('created_at');
          });
        }
      }
    }
}
