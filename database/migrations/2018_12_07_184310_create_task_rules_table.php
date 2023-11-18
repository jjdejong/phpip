<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_rules', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('active')->default(1)->comment('Indicates whether the rule should be used');
            $table->char('task', 5)->index('task')->comment('Code of task that is created (or cleared)');
            $table->char('trigger_event', 5)->comment('Event that generates this task');
            $table->boolean('clear_task')->default(0)->comment('Identifies an open task in the matter that is cleared when this one is created');
            $table->boolean('delete_task')->default(0)->comment('Identifies a task type to be deleted from the matter when this one is created');
            $table->char('for_category', 5)->default('PAT')->index('fk_category')->comment('Category to which this rule applies.');
            $table->char('for_country', 2)->nullable()->index('for_country')->comment('Country where rule is applicable. If NULL, applies to all countries');
            $table->char('for_origin', 5)->nullable()->index('for_origin');
            $table->char('for_type', 5)->nullable()->comment('Type to which rule is applicable. If null, rule applies to all types');
            $table->string('detail', 45)->nullable()->comment('Additional information on task');
            $table->integer('days')->default(0)->comment('For task deadline calculation');
            $table->integer('months')->default(0)->comment('For task deadline calculation');
            $table->integer('years')->default(0)->comment('For task deadline calculation');
            $table->boolean('recurring')->default(0)->comment('If non zero, indicates the recurring period in months. Mainly for annuities');
            $table->boolean('end_of_month')->default(0)->comment('The deadline is at the end of the month. Mainly for annuities');
            $table->char('abort_on', 5)->nullable()->index('abort_on')->comment('Task won\'t be created if this event exists');
            $table->char('condition_event', 5)->nullable()->index('condition')->comment('Task will only be created if this event exists');
            $table->boolean('use_parent')->default(0)->comment('The due date is calculated from the same event in the top parent (eg. for calculating annuities for a divisional)');
            $table->boolean('use_priority')->default(0);
            $table->date('use_before')->nullable()->comment('Task will be created only if the base event is before this date');
            $table->date('use_after')->nullable()->comment('Task will be created only if the base event is after this date');
            $table->decimal('cost', 6)->nullable();
            $table->decimal('fee', 6)->nullable();
            $table->char('currency', 3)->nullable()->default('EUR');
            $table->char('responsible', 16)->nullable()->comment('The person (login) responsible for this task. If 0, insert the matter responsible.');
            $table->text('notes', 65535)->nullable();
            $table->char('creator', 16)->nullable();
            $table->char('updater', 16)->nullable();
            $table->timestamps();
            $table->index(['trigger_event', 'for_country'], 'trigger_event');
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('task_rules');
    }
};
