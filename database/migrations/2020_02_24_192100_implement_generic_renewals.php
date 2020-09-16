<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImplementGenericRenewals extends Migration {

	/**
	 * Run the migrations.
	 *
	 * IMPORTANT this migration will generate unique key constraint violation errors if the tasks the task_rules table are not unique.
	 * If you get such errors, the task will be identified in the error message and you then need to fix it and run the migration again.
	 * Probably you simply need to delete the task, because it is redundant.
	 * Run the migration as many times as necessary until successful.
	 *
	 * @return void
	 */
	public function up()
	{
		// Fix glitches from original seeder
		DB::table('task_rules')->where('responsible', 'phpip_user')->update(['responsible' => NULL]);
		DB::table('task_rules')->where([['task', 'REM'], ['trigger_event', 'DRA']])->delete();
		DB::table('task_rules')->where([['for_country', 'IN'], ['for_origin', 'WO']])->delete();
		DB::table('task_rules')->where([['for_country', 'IN'], ['detail', 'like', 'Veri%']])->delete();
		DB::table('matter_category')->where('category', 'Trade Mark')->update(['category' => 'Trademark']);

		if (Schema::hasColumn('event_name', 'uqtrigger')) {
			Schema::table('event_name', function (Blueprint $table) {
				$table->dropColumn('uqtrigger');
			});
		}

		Schema::table('task_rules', function (Blueprint $table) {
			if (!Schema::hasColumn('task_rules', 'uid')) {
				$table->string('uid', 32)->unique()->virtualAs("md5(concat(task, trigger_event, clear_task, delete_task, for_category, ifnull(for_country, 'c'), ifnull(for_origin, 'o'), ifnull(for_type, 't'), days, months, years, recurring, ifnull(abort_on, 'a'), ifnull(condition_event, 'c'), use_parent, use_priority, ifnull(detail, 'd')))");
			}
		});

		Schema::table('country', function (Blueprint $table) {
			$table->tinyInteger('renewal_first')->unsigned()->nullable()->default(2)->comment('The first year a renewal is due in this country');
			$table->char('renewal_base', 5)->nullable()->default('FIL')->comment('The base event for calculating renewal deadlines');
			$table->char('renewal_start', 5)->nullable()->default('FIL')->comment('The event from which renewals become due');
			$table->date('checked_on')->nullable()->default(NULL);
		});

		DB::table('country')->where('iso', 'AE')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'AO')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // Up to 15
		DB::table('country')->where('iso', 'AP')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'AR')->update(['renewal_first' => 1, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'AT')->update(['renewal_first' => 6, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'AU')->update(['renewal_first' => 5, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'BE')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'BG')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'BH')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'BO')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'BR')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'CA')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'CH')->update(['renewal_first' => 4, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'CL')->update(['renewal_first' => NULL, 'renewal_base' => NULL, 'renewal_start' => NULL, 'checked_on' => Now()]); // Exception: renewals per decade after grant
		DB::table('country')->where('iso', 'CN')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'CO')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'CR')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'CU')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'CY')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'CZ')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'DE')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'DK')->update(['renewal_first' => 4, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'DO')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'DZ')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'EA')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'EC')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'EE')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'EG')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'EP')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'ES')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'FI')->update(['renewal_first' => 4, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'FR')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'GB')->update(['renewal_first' => 5, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'GC')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // Unknown
		DB::table('country')->where('iso', 'GR')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'GT')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'HK')->update(['renewal_first' => 6, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // Complicated: stage 1 fees due from 6 after filing and stage 2 (via GB or CN) from 4 after grant
		DB::table('country')->where('iso', 'HN')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'HR')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'HU')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'PUB', 'checked_on' => Now()]); // Past renewals due at publication of tranlated patent
		DB::table('country')->where('iso', 'ID')->update(['renewal_first' => 1, 'renewal_base' => 'GRT', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'IE')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'IL')->update(['renewal_first' => NULL, 'renewal_base' => NULL, 'renewal_start' => NULL, 'checked_on' => Now()]); // Exception: 3m after grant, then 6, 10, 14, 18y after filing
		DB::table('country')->where('iso', 'IN')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'IR')->update(['renewal_first' => 2, 'renewal_base' => 'ENT', 'renewal_start' => 'ENT', 'checked_on' => Now()]); // National phase entry anniversary!
		DB::table('country')->where('iso', 'IS')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'IT')->update(['renewal_first' => 5, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'JO')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'JP')->update(['renewal_first' => 4, 'renewal_base' => 'GRT', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'KR')->update(['renewal_first' => 4, 'renewal_base' => 'GRT', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'LB')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'LI')->update(['renewal_first' => 4, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'LT')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'LU')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'LY')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'MA')->update(['renewal_first' => 2, 'renewal_base' => 'GRT', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'MC')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'ME')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'MG')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'MT')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'MX')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'MY')->update(['renewal_first' => 2, 'renewal_base' => 'GRT', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'NG')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'NI')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'NL')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'NO')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'NZ')->update(['renewal_first' => 5, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'OA')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'PH')->update(['renewal_first' => 5, 'renewal_base' => 'PUB', 'renewal_start' => 'PUB', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'PK')->update(['renewal_first' => 5, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'PL')->update(['renewal_first' => 4, 'renewal_base' => 'GRT', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'PT')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'PY')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'QA')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'RO')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'RS')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'RU')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'SA')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'SE')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'SG')->update(['renewal_first' => 5, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'SI')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // EP
		DB::table('country')->where('iso', 'SK')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'SV')->update(['renewal_first' => 3, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'SY')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'TH')->update(['renewal_first' => 5, 'renewal_base' => 'GRT', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'TN')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'TR')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'TT')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'TW')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'UA')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'US')->update(['renewal_first' => NULL, 'renewal_base' => NULL, 'renewal_start' => NULL, 'checked_on' => Now()]); // Exception
		DB::table('country')->where('iso', 'UY')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'GRT', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'VE')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'VN')->update(['renewal_first' => 2, 'renewal_base' => 'GRT', 'renewal_start' => 'GRT', 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'WO')->update(['renewal_first' => NULL, 'renewal_base' => NULL, 'renewal_start' => NULL, 'checked_on' => Now()]);
		DB::table('country')->where('iso', 'YE')->update(['renewal_first' => 2, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]); // OK
		DB::table('country')->where('iso', 'ZA')->update(['renewal_first' => 4, 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]);

		// tr_task rule for handling renewals from the country table above
		DB::table('task_rules')->insertOrIgnore([
			['task' => 'REN', 'trigger_event' => 'FIL', 'for_category' => 'PAT', 'recurring' => 1, 'years' => 19, 'creator' => 'script', 'created_at' => Now(), 'updated_at' => Now(),
			'detail' => 'Recurring', 'notes' => 'Uses the country table information by setting "recurring = 1"'],
			['task' => 'REN', 'trigger_event' => 'GRT', 'for_category' => 'PAT', 'recurring' => 1, 'years' => 19, 'creator' => 'script', 'created_at' => Now(), 'updated_at' => Now(),
			'detail' => 'Recurring', 'notes' => 'Uses the country table information by setting "recurring = 1"']
		]);

		// tr_task rules for the exceptions
		DB::statement("INSERT IGNORE INTO task_rules (id, task, trigger_event, for_category, for_country, detail, months, years, creator, created_at, updated_at)
			VALUES (300, 'REN', 'GRT', 'PAT', 'US', '3.5', 6, 3, 'script', Now(), Now()),
			(301, 'REN', 'GRT', 'PAT', 'US', '7.5', 6, 7, 'script', Now(), Now()),
			(302, 'REN', 'GRT', 'PAT', 'US', '11.5', 6, 11, 'script', Now(), Now()),
			(303, 'REN', 'GRT', 'PAT', 'CL', '1-10', 0, 0, 'script', Now(), Now()),
			(304, 'REN', 'FIL', 'PAT', 'CL', '11-20', 0, 10, 'script', Now(), Now()),
			(305, 'REN', 'FIL', 'PAT', 'IL', '7', 0, 6, 'script', Now(), Now()),
			(306, 'REN', 'FIL', 'PAT', 'IL', '11', 0, 10, 'script', Now(), Now()),
			(307, 'REN', 'FIL', 'PAT', 'IL', '15', 0, 14, 'script', Now(), Now()),
			(308, 'REN', 'FIL', 'PAT', 'IL', '19', 0, 18, 'script', Now(), Now())"
		);

		DB::unprepared("DROP TRIGGER IF EXISTS `event_after_insert`");
		DB::unprepared("CREATE TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW
trig: BEGIN
	DECLARE DueDate, BaseDate, m_expiry DATE DEFAULT NULL;
	DECLARE tr_id, tr_days, tr_months, tr_years, m_pta, lnk_matter_id, CliAnnAgt INT DEFAULT NULL;
	DECLARE tr_task, m_type_code, tr_currency, m_country, m_origin CHAR(5) DEFAULT NULL;
	DECLARE tr_detail, tr_responsible VARCHAR(160) DEFAULT NULL;
	DECLARE Done, tr_clear_task, tr_delete_task, tr_end_of_month, tr_recurring, tr_use_parent, tr_use_priority, m_dead BOOLEAN DEFAULT 0;
	DECLARE tr_cost, tr_fee DECIMAL(6,2) DEFAULT null;

	DECLARE cur_rule CURSOR FOR
		SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_parent, use_priority, cost, fee, currency, task_rules.responsible
		FROM task_rules
		JOIN event_name ON event_name.code = task_rules.task
		JOIN matter ON matter.id = NEW.matter_id
		WHERE task_rules.active = 1
		AND task_rules.for_category = matter.category_code
		AND NEW.code = task_rules.trigger_event
		AND (Now() < use_before OR use_before IS null)
		AND (Now() > use_after OR use_after IS null)
		AND IF (task_rules.for_country IS NOT NULL,
			task_rules.for_country = matter.country,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category, tr.active) = (matter.country, matter.category_code, 1))
		)
		AND IF (task_rules.for_origin IS NOT NULL,
			task_rules.for_origin = matter.origin,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_origin, tr.for_category, tr.active) = (matter.origin, matter.category_code, 1))
		)
		AND IF (task_rules.for_type IS NOT NULL,
			task_rules.for_type = matter.type_code,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category, tr.active) = (matter.type_code, matter.category_code, 1))
		)
		AND NOT EXISTS (SELECT 1 FROM event WHERE event.matter_id = NEW.matter_id AND event.code = task_rules.abort_on)
		AND IF (task_rules.condition_event IS NULL, true, EXISTS (SELECT 1 FROM event WHERE event.matter_id = NEW.matter_id AND event.code = task_rules.condition_event));

	DECLARE cur_linked CURSOR FOR
		SELECT matter_id FROM event WHERE event.alt_matter_id = NEW.matter_id;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	SELECT type_code, dead, expire_date, term_adjust, country, origin INTO m_type_code, m_dead, m_expiry, m_pta, m_country, m_origin FROM matter WHERE matter.id = NEW.matter_id;
	SELECT id INTO CliAnnAgt FROM actor WHERE display_name = 'CLIENT';

	-- Do not change anything in dead cases
	IF (m_dead) THEN
		LEAVE trig;
	END IF;

	OPEN cur_rule;
		create_tasks: LOOP
			SET BaseDate = NEW.event_date;
			FETCH cur_rule INTO tr_id, tr_task, tr_clear_task, tr_delete_task, tr_detail, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_parent, tr_use_priority, tr_cost, tr_fee, tr_currency, tr_responsible;

			IF Done THEN
				LEAVE create_tasks;
			END IF;

			-- Skip renewal tasks if the client is the annuity agent
			IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role = 'ANN' AND lnk.actor_id = CliAnnAgt AND lnk.matter_id = NEW.matter_id) THEN
				ITERATE create_tasks;
			END IF;

			-- Skip recurring renewal tasks if country table has no correspondence
			IF tr_task = 'REN' AND tr_recurring = 1 AND NOT EXISTS (SELECT 1 FROM country WHERE iso = m_country and renewal_start = NEW.code) THEN
				ITERATE create_tasks;
			END IF;

			-- Use earliest parent filing date for task deadline calculation, if PFIL event exists
			IF tr_use_parent THEN
				SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PFIL' AND matter_id = NEW.matter_id;
			END IF;

			-- Use earliest priority date for task deadline calculation, if a PRI event exists
			IF tr_use_priority THEN
				SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = NEW.matter_id;
			END IF;

			-- Clear the task identified by the rule (do not create anything)
			IF tr_clear_task THEN
				UPDATE task JOIN event ON task.trigger_id = event.id
					SET task.done_date = NEW.event_date
					WHERE task.code = tr_task AND event.matter_id = NEW.matter_id AND task.done = 0;
				ITERATE create_tasks;
			END IF;

			-- Delete the task identified by the rule (do not create anything)
			IF tr_delete_task THEN
				DELETE FROM task
					WHERE task.code = tr_task AND task.trigger_id IN (SELECT event.id FROM event WHERE event.matter_id = NEW.matter_id);
				ITERATE create_tasks;
			END IF;

			-- Calculate the deadline
			SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
			IF tr_end_of_month THEN
				SET DueDate = LAST_DAY(DueDate);
			END IF;

			-- Deadline for renewals that have become due in a parent case when filing a divisional (EP 4-months rule)
			IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM event WHERE code = 'PFIL' AND matter_id = NEW.matter_id) AND DueDate < NEW.event_date THEN
				SET DueDate = NEW.event_date + INTERVAL 4 MONTH;
			END IF;

			-- Don't create tasks having a deadline in the past, except for expiry and renewal
			-- Create renewals up to 6 months in the past in general, create renewals up to 19 months in the past for PCT national phases (captures direct PCT filing situations)
			IF (DueDate < Now() AND tr_task NOT IN ('EXP', 'REN'))
			OR (DueDate < (Now() - INTERVAL 6 MONTH) AND tr_task = 'REN' AND m_origin != 'WO')
			OR (DueDate < (Now() - INTERVAL 19 MONTH) AND tr_task = 'REN' AND m_origin = 'WO')
			THEN
				ITERATE create_tasks;
			END IF;

			IF tr_task = 'EXP' THEN
			-- Handle the expiry task (no task created)
				UPDATE matter SET expire_date = DueDate + INTERVAL m_pta DAY WHERE matter.id = NEW.matter_id;
			ELSEIF tr_recurring = 0 THEN
			-- Insert the new task if it is not a recurring one
				INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
				VALUES (NEW.id, tr_task, DueDate, tr_detail, tr_id, tr_cost, tr_fee, tr_currency, tr_responsible, NEW.creator, Now(), Now());
			ELSEIF tr_task = 'REN' THEN
			-- Recurring renewal is the only possibilty here, normally
				CALL insert_recurring_renewals(NEW.id, tr_id, BaseDate, tr_responsible, NEW.creator);
			END IF;
		END LOOP create_tasks;
	CLOSE cur_rule;
	SET Done = 0;

	-- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
	IF NEW.code = 'FIL' THEN
		OPEN cur_linked;
			recalc_linked: LOOP
				FETCH cur_linked INTO lnk_matter_id;
				IF Done THEN
					LEAVE recalc_linked;
				END IF;
				CALL recalculate_tasks(lnk_matter_id, 'FIL', NEW.creator);
			END LOOP recalc_linked;
		CLOSE cur_linked;
	END IF;

	-- Recalculate tasks based on the priority date or a parent case upon inserting a new priority claim or a parent filed event
	IF NEW.code IN ('PRI', 'PFIL') THEN
		CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.creator);
	END IF;

	-- Set matter to dead upon inserting a killer event
	SELECT killer INTO m_dead FROM event_name WHERE NEW.code = event_name.code;
	IF m_dead THEN
		UPDATE matter SET dead = 1 WHERE matter.id = NEW.matter_id;
	END IF;
END trig");

		DB::unprepared("DROP TRIGGER IF EXISTS `event_after_update`");
		DB::unprepared("CREATE TRIGGER `event_after_update` AFTER UPDATE ON `event` FOR EACH ROW
trig: BEGIN

	-- CALL recalculate_tasks(NEW.matter_id, NEW.code, NEW.updater);

	DECLARE DueDate, BaseDate DATE DEFAULT NULL;
  DECLARE t_id, tr_days, tr_months, tr_years, tr_recurring, m_pta, lnk_matter_id INT DEFAULT NULL;
  DECLARE Done, tr_end_of_month, tr_use_parent, tr_use_priority BOOLEAN DEFAULT 0;
  DECLARE m_category, m_country CHAR(5) DEFAULT NULL;

	DECLARE cur_rule CURSOR FOR
		SELECT task.id, days, months, years, recurring, end_of_month, use_parent, use_priority
		FROM task_rules
		JOIN task ON task.rule_used = task_rules.id
		WHERE task.trigger_id = NEW.id;

  DECLARE cur_linked CURSOR FOR
  	SELECT matter_id FROM event WHERE alt_matter_id = NEW.matter_id;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	-- Leave if date hasn't changed
	IF (OLD.event_date = NEW.event_date AND NEW.alt_matter_id <=> OLD.alt_matter_id) THEN
		LEAVE trig;
	END IF;

  SET BaseDate = NEW.event_date;

  OPEN cur_rule;
  update_tasks: LOOP
  	FETCH cur_rule INTO t_id, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_parent, tr_use_priority;

  	IF Done THEN
  	  LEAVE update_tasks;
  	END IF;

		IF tr_recurring = 1 THEN
			-- Recurring tasks are not managed
			ITERATE update_tasks;
		END IF;

    -- Use parent filing date for task deadline calculation, if PFIL event exists
  	IF tr_use_parent THEN
  	  SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PFIL' AND matter_id = NEW.matter_id;
  	END IF;

    -- Use earliest priority date for task deadline calculation, if a PRI event exists
  	IF tr_use_priority THEN
  	  SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = NEW.matter_id;
  	END IF;

    -- Calculate the deadline
  	SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
  	IF tr_end_of_month THEN
  	  SET DueDate = LAST_DAY(DueDate);
  	END IF;

		UPDATE task set due_date = DueDate, updated_at = Now(), updater = NEW.updater WHERE id = t_id;
  END LOOP update_tasks;
  CLOSE cur_rule;
  SET Done = 0;

  -- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
  IF NEW.code = 'FIL' THEN
  	OPEN cur_linked;
  	recalc_linked: LOOP
  	  FETCH cur_linked INTO lnk_matter_id;
  	  IF Done THEN
  		  LEAVE recalc_linked;
  	  END IF;
  	  CALL recalculate_tasks(lnk_matter_id, 'FIL', NEW.updater);
  	  CALL recalculate_tasks(lnk_matter_id, 'PRI', NEW.updater);
  	END LOOP recalc_linked;
  	CLOSE cur_linked;
  END IF;

  -- Recalculate tasks based on the priority date or a parent case upon inserting a new priority claim or a parent filed event
  IF NEW.code IN ('PRI', 'PFIL') THEN
  	CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.updater);
  END IF;

	-- Recalculate expiry
	IF NEW.code IN ('FIL', 'PFIL') THEN
  	SELECT category_code, term_adjust, country INTO m_category, m_pta, m_country FROM matter WHERE matter.id = NEW.matter_id;
  	SELECT months, years INTO tr_months, tr_years FROM task_rules
  		WHERE task = 'EXP'
  		AND for_category = m_category
			AND IF (for_country IS NOT NULL,
				for_country = m_country,
				concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category) = (m_country, m_category))
			);
		SELECT IFNULL(min(event_date), NEW.event_date) INTO BaseDate FROM event_lnk_list WHERE code IN ('FIL', 'PFIL') AND matter_id = NEW.matter_id;
  	SET DueDate = BaseDate + INTERVAL m_pta DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
  	UPDATE matter SET expire_date = DueDate WHERE matter.id = NEW.matter_id;
  END IF;
END trig");

		DB::unprepared('DROP PROCEDURE IF EXISTS `recalculate_tasks`');
		DB::unprepared("CREATE PROCEDURE `recalculate_tasks`(
			IN P_matter_id INT,
			IN P_event_code CHAR(5),
			IN P_user CHAR(16)
		)
proc: BEGIN
	DECLARE e_event_date, DueDate, BaseDate DATE DEFAULT NULL;
	DECLARE t_id, e_id, tr_days, tr_months, tr_years, tr_recurring, m_pta INT DEFAULT NULL;
	DECLARE Done, tr_end_of_month, tr_use_parent, tr_use_priority BOOLEAN DEFAULT 0;
	DECLARE m_category, m_country CHAR(5) DEFAULT NULL;

	DECLARE cur_rule CURSOR FOR
		SELECT task.id, days, months, years, recurring, end_of_month, use_parent, use_priority
		FROM task_rules
		JOIN task ON task.rule_used = task_rules.id
		WHERE task.trigger_id = e_id;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	SELECT id, event_date INTO e_id, e_event_date FROM event_lnk_list WHERE matter_id = P_matter_id AND code = P_event_code ORDER BY event_date LIMIT 1;
	-- Leave if matter does not have the required event
	IF e_id IS NULL THEN
		LEAVE proc;
	END IF;

	OPEN cur_rule;
	update_tasks: LOOP
		FETCH cur_rule INTO t_id, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_parent, tr_use_priority;

		IF Done THEN
			LEAVE update_tasks;
		END IF;

		IF tr_recurring = 1 THEN
			-- Recurring tasks are not managed
			ITERATE update_tasks;
		END IF;

    SET BaseDate = e_event_date;

		-- Use parent filing date for task deadline calculation, if PFIL event exists
		IF tr_use_parent THEN
			SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PFIL' AND matter_id = P_matter_id;
		END IF;

		-- Use earliest priority date for task deadline calculation, if a PRI event exists
		IF tr_use_priority THEN
			SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = P_matter_id;
		END IF;

		-- Calculate the deadline
		SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
		IF tr_end_of_month THEN
			SET DueDate = LAST_DAY(DueDate);
		END IF;

		UPDATE task SET due_date = DueDate, updated_at = Now(), updater = P_user WHERE task.id = t_id;
	END LOOP update_tasks;
	CLOSE cur_rule;

	-- Recalculate expiry
	IF P_event_code IN ('FIL', 'PFIL') THEN
		SELECT category_code, term_adjust, country INTO m_category, m_pta, m_country FROM matter WHERE matter.id = P_matter_id;
		SELECT months, years INTO tr_months, tr_years FROM task_rules
			WHERE task = 'EXP'
			AND for_category = m_category
			AND IF (for_country IS NOT NULL,
				for_country = m_country,
				concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category) = (m_country, m_category))
			);
		SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PFIL' AND matter_id = P_matter_id;
		SET DueDate = BaseDate + INTERVAL m_pta DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
		UPDATE matter SET expire_date = DueDate WHERE matter.id = P_matter_id;
	END IF;
END proc");

		DB::unprepared('DROP PROCEDURE IF EXISTS `recreate_tasks`');
		DB::unprepared("CREATE PROCEDURE `recreate_tasks`(
			IN P_trigger_id INT,
			IN P_user CHAR(16)
		)
proc: BEGIN
	DECLARE e_event_date, DueDate, BaseDate, m_expiry DATE DEFAULT NULL;
	DECLARE e_matter_id, tr_id, tr_days, tr_months, tr_years, m_pta, lnk_matter_id, CliAnnAgt INT DEFAULT NULL;
	DECLARE e_code, tr_task, m_country, m_type_code, tr_currency, m_origin CHAR(5) DEFAULT NULL;
	DECLARE tr_detail, tr_responsible VARCHAR(160) DEFAULT NULL;
	DECLARE Done, tr_clear_task, tr_delete_task, tr_end_of_month, tr_recurring, tr_use_parent, tr_use_priority, m_dead BOOLEAN DEFAULT 0;
	DECLARE tr_cost, tr_fee DECIMAL(6,2) DEFAULT null;

	DECLARE cur_rule CURSOR FOR
		SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_parent, use_priority, cost, fee, currency, task_rules.responsible
		FROM task_rules
		JOIN event_name ON event_name.code = task_rules.task
		JOIN matter ON matter.id = e_matter_id
		WHERE task_rules.active = 1
		AND task_rules.for_category = matter.category_code
		AND e_code = task_rules.trigger_event
		AND (Now() < use_before OR use_before IS null)
		AND (Now() > use_after OR use_after IS null)
		AND IF (task_rules.for_country IS NOT NULL,
			task_rules.for_country = matter.country,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category, tr.active) = (matter.country, matter.category_code, 1))
		)
		AND IF (task_rules.for_origin IS NOT NULL,
			task_rules.for_origin = matter.origin,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_origin, tr.for_category, tr.active) = (matter.origin, matter.category_code, 1))
		)
		AND IF (task_rules.for_type IS NOT NULL,
			task_rules.for_type = matter.type_code,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category, tr.active) = (matter.type_code, matter.category_code, 1))
		)
		AND NOT EXISTS (SELECT 1 FROM event WHERE event.matter_id = e_matter_id AND event.code = task_rules.abort_on)
		AND IF (task_rules.condition_event IS NULL, true, EXISTS (SELECT 1 FROM event WHERE matter_id = e_matter_id AND event.code = task_rules.condition_event));

	DECLARE cur_linked CURSOR FOR
		SELECT matter_id FROM event WHERE event.alt_matter_id = e_matter_id;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	-- Delete the tasks attached to the event by an existing rule
	DELETE FROM task WHERE rule_used IS NOT NULL AND trigger_id = P_trigger_id;

  SELECT e.matter_id, e.event_date, e.code, m.country, m.type_code, m.dead, m.expire_date, m.term_adjust, m.origin INTO e_matter_id, e_event_date, e_code, m_country, m_type_code, m_dead, m_expiry, m_pta, m_origin
	FROM event e
	JOIN matter m ON m.id = e.matter_id
	WHERE e.id = P_trigger_id;

  SELECT id INTO CliAnnAgt FROM actor WHERE display_name = 'CLIENT';

	-- Do not change anything in dead cases
	IF (m_dead OR Now() > m_expiry) THEN
    LEAVE proc;
  END IF;

	OPEN cur_rule;
	  create_tasks: LOOP
			SET BaseDate = e_event_date;
	    FETCH cur_rule INTO tr_id, tr_task, tr_clear_task, tr_delete_task, tr_detail, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_parent, tr_use_priority, tr_cost, tr_fee, tr_currency, tr_responsible;

			IF Done THEN
	      LEAVE create_tasks;
	    END IF;

			-- Skip renewal tasks if the client is the annuity agent
			IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role = 'ANN' AND lnk.actor_id = CliAnnAgt AND lnk.matter_id = e_matter_id) THEN
				ITERATE create_tasks;
			END IF;

			-- Skip recurring renewal tasks if country table has no correspondence
			IF tr_task = 'REN' AND tr_recurring = 1 AND NOT EXISTS (SELECT 1 FROM country WHERE iso = m_country and renewal_start = e_code) THEN
				ITERATE create_tasks;
			END IF;

			-- Use earliest parent filing date for task deadline calculation, if PFIL event exists
			IF tr_use_parent THEN
				SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PFIL' AND matter_id = e_matter_id;
			END IF;

			-- Use earliest priority date for task deadline calculation, if a PRI event exists
			IF tr_use_priority THEN
				SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = e_matter_id;
			END IF;

			-- Clear the task identified by the rule (do not create anything)
	    IF tr_clear_task THEN
	      UPDATE task JOIN event ON task.trigger_id = event.id
					SET task.done_date = e_event_date
					WHERE task.code = tr_task AND event.matter_id = e_matter_id AND task.done = 0;
	      ITERATE create_tasks;
	    END IF;

			-- Delete the task identified by the rule (do not create anything)
	    IF tr_delete_task THEN
				DELETE FROM task
					WHERE task.code = tr_task AND task.trigger_id IN (SELECT event.id FROM event WHERE event.matter_id = e_matter_id);
				ITERATE create_tasks;
	    END IF;

			-- Calculate the deadline
			SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
	    IF tr_end_of_month THEN
	      SET DueDate = LAST_DAY(DueDate);
	    END IF;


			-- Deadline for renewals that have become due in a parent case when filing a divisional (EP 4-months rule)
			IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM event WHERE code = 'PFIL' AND matter_id = e_matter_id) AND DueDate < e_event_date THEN
				SET DueDate = e_event_date + INTERVAL 4 MONTH;
			END IF;

			-- Don't create tasks having a deadline in the past, except for expiry and renewal
      -- Create renewals up to 6 months in the past in general, create renewals up to 19 months in the past for PCT national phases (captures direct PCT filing situations)
			IF (DueDate < Now() AND tr_task NOT IN ('EXP', 'REN'))
			OR (DueDate < (Now() - INTERVAL 6 MONTH) AND tr_task = 'REN' AND m_origin != 'WO')
			OR (DueDate < (Now() - INTERVAL 19 MONTH) AND tr_task = 'REN' AND m_origin = 'WO')
			THEN
	      ITERATE create_tasks;
	    END IF;

	    IF tr_task = 'EXP' THEN
			-- Handle the expiry task (no task created)
				UPDATE matter SET expire_date = DueDate + INTERVAL m_pta DAY WHERE matter.id = e_matter_id;
			ELSEIF tr_recurring = 0 THEN
			-- Insert the new task if it is not a recurring one
				INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
		    VALUES (P_trigger_id, tr_task, DueDate, tr_detail, tr_id, tr_cost, tr_fee, tr_currency, tr_responsible, P_user, Now(), Now());
			ELSEIF tr_task = 'REN' THEN
			-- Recurring renewal is the only possibilty here, normally
				CALL insert_recurring_renewals(P_trigger_id, tr_id, BaseDate, tr_responsible, P_user);
			END IF;
	  END LOOP create_tasks;
  CLOSE cur_rule;
  SET done = 0;

	-- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
	IF e_code = 'FIL' THEN
		OPEN cur_linked;
			recalc_linked: LOOP
				FETCH cur_linked INTO lnk_matter_id;
				IF Done THEN
					LEAVE recalc_linked;
				END IF;
				CALL recalculate_tasks(lnk_matter_id, 'FIL', P_user);
			END LOOP recalc_linked;
		CLOSE cur_linked;
  END IF;

	-- Recalculate tasks based on the priority date or a parent case upon inserting a new priority claim or a parent filed event
  IF e_code IN ('PRI', 'PFIL') THEN
    CALL recalculate_tasks(e_matter_id, 'FIL', P_user);
  END IF;

	-- Set matter to dead upon inserting a killer event
	SELECT killer INTO m_dead FROM event_name WHERE e_code = event_name.code;
  IF m_dead THEN
    UPDATE matter SET dead = 1 WHERE matter.id = e_matter_id;
  END IF;
END proc");

		DB::unprepared('DROP PROCEDURE IF EXISTS `insert_recurring_renewals`');
		DB::unprepared("CREATE PROCEDURE `insert_recurring_renewals`(
			IN P_trigger_id INT,
    	IN P_rule_id INT,
    	IN P_base_date DATE,
    	IN P_responsible CHAR(16),
    	IN P_user CHAR(16)
		)
proc: BEGIN
	DECLARE FirstRenewal, RYear INT;
  DECLARE StartDate, DueDate, ExpiryDate DATE DEFAULT NULL;
	DECLARE Origin CHAR(2) DEFAULT NULL;

  SELECT estart.event_date, country.renewal_first, matter.expire_date, matter.origin INTO StartDate, FirstRenewal, ExpiryDate, Origin
	  FROM country
	  JOIN matter ON country.iso = matter.country
	  JOIN event estart ON estart.matter_id = matter.id AND estart.id = P_trigger_id
	  JOIN event ebase ON ebase.matter_id = matter.id
	  WHERE country.renewal_start = estart.code
	  AND country.renewal_base = ebase.code;

	-- Leave if the country has no parameters (country dealt with specifically in task_rules)
  IF StartDate IS NULL THEN
		LEAVE proc;
	END IF;

	SET RYear = FirstRenewal;
  renloop: WHILE RYear <= 20 DO
		SET DueDate = P_base_date + INTERVAL RYear - 1 YEAR;
		IF DueDate > ExpiryDate THEN
			LEAVE proc;
		END IF;
	  IF DueDate < StartDate THEN
			SET DueDate = StartDate;
		END IF;
		-- Ignore renewals in the past beyond the 6-months grace period unless PCT national phase
	  IF (DueDate < Now() - INTERVAL 6 MONTH AND Origin != 'WO') OR (DueDate < (Now() - INTERVAL 19 MONTH) AND Origin = 'WO') THEN
			SET RYear = RYear + 1;
			ITERATE renloop;
		END IF;
	  INSERT INTO task (trigger_id, code, due_date, detail, rule_used, assigned_to, creator, created_at, updated_at)
		VALUES (P_trigger_id, 'REN', DueDate, RYear, P_rule_id, P_responsible, P_user, Now(), Now());

		SET RYear = RYear + 1;
  END WHILE;
END proc");

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('country', function (Blueprint $table) {
			$table->dropColumn('renewal_start');
			$table->dropColumn('renewal_base');
			$table->dropColumn('renewal_first');
			$table->dropColumn('checked_on');
		});

		Schema::table('task_rules', function (Blueprint $table) {
			$table->dropColumn('uid');
		});

		Schema::table('event_name', function(Blueprint $table)
		{
			$table->boolean('uqtrigger')->default(0)->comment('Can only be triggered by one event');
		});

		DB::unprepared('DROP PROCEDURE IF EXISTS `insert_recurring_renewals`');
	}
}
