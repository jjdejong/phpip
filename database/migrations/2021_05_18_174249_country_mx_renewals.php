<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        DB::table('country')->where('iso', 'MX')->update([
            'renewal_first' => null,
            'renewal_base' => null,
            'renewal_start' => null,
            'checked_on' => '2021-05-18',
        ]);

        DB::table('country')->where('iso', 'IR')->update([
            'renewal_first' => 2,
            'renewal_base' => 'FIL',
            'renewal_start' => 'FIL',
            'checked_on' => '2021-05-18',
        ]);

        DB::table('task_rules')->insertOrIgnore([
            [
                'active' => 1,
                'task' => 'REN',
                'trigger_event' => 'FIL',
                'clear_task' => 0,
                'delete_task' => 0,
                'for_category' => 'PAT',
                'for_country' => 'MX',
                'for_origin' => null,
                'for_type' => null,
                'detail' => '6-10',
                'days' => 0,
                'months' => 0,
                'years' => 5,
                'recurring' => 0,
                'end_of_month' => 0,
                'abort_on' => null,
                'condition_event' => null,
                'use_priority' => 0,
                'use_before' => null,
                'use_after' => null,
                'cost' => null,
                'fee' => null,
                'currency' => 'EUR',
                'responsible' => null,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'active' => 1,
                'task' => 'REN',
                'trigger_event' => 'FIL',
                'clear_task' => 0,
                'delete_task' => 0,
                'for_category' => 'PAT',
                'for_country' => 'MX',
                'for_origin' => null,
                'for_type' => null,
                'detail' => '11-15',
                'days' => 0,
                'months' => 0,
                'years' => 10,
                'recurring' => 0,
                'end_of_month' => 0,
                'abort_on' => null,
                'condition_event' => null,
                'use_priority' => 0,
                'use_before' => null,
                'use_after' => null,
                'cost' => null,
                'fee' => null,
                'currency' => 'EUR',
                'responsible' => null,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'active' => 1,
                'task' => 'REN',
                'trigger_event' => 'FIL',
                'clear_task' => 0,
                'delete_task' => 0,
                'for_category' => 'PAT',
                'for_country' => 'MX',
                'for_origin' => null,
                'for_type' => null,
                'detail' => '16-20',
                'days' => 0,
                'months' => 0,
                'years' => 15,
                'recurring' => 0,
                'end_of_month' => 0,
                'abort_on' => null,
                'condition_event' => null,
                'use_priority' => 0,
                'use_before' => null,
                'use_after' => null,
                'cost' => null,
                'fee' => null,
                'currency' => 'EUR',
                'responsible' => null,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        //
    }
};
