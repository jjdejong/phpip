<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSpecialCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This does not work in Laravel - "tinyInteger" cannot be changed
        // Schema::table('country', function (Blueprint $table) {
        //   $table->tinyInteger('renewal_first')->nullable()->default(2)->comment('The first year a renewal is due in this country from renewal_base. When negative, the date is calculated from renewal_start')->change();
        // });
        // Do this instead:
        DB::statement("ALTER TABLE country CHANGE COLUMN renewal_first renewal_first TINYINT(3) DEFAULT 2 COMMENT 'The first year a renewal is due in this country from renewal_base. When negative, the date is calculated from renewal_start'");

        DB::table('country')->where('iso', 'HK')->update(['renewal_first' => -4, 'renewal_start' => 'GRT']);
        DB::table('country')->where('iso', 'HU')->update(['renewal_start' => 'FIL']);
        //DB::table('country')->where('iso', 'IR')->update(['renewal_first' => 3]);

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
  	DECLARE BaseDate, StartDate, DueDate, ExpiryDate DATE DEFAULT NULL;
  	DECLARE Origin CHAR(2) DEFAULT NULL;

  	SELECT ebase.event_date, estart.event_date, country.renewal_first, matter.expire_date, matter.origin INTO BaseDate, StartDate, FirstRenewal, ExpiryDate, Origin
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
  	SET BaseDate = LEAST(BaseDate, P_base_date);
  	SET RYear = ABS(FirstRenewal);
  	renloop: WHILE RYear <= 20 DO
      IF (FirstRenewal > 0) THEN
  		  SET DueDate = BaseDate + INTERVAL RYear - 1 YEAR;
      ELSE
        SET DueDate = StartDate + INTERVAL RYear - 1 YEAR;
      END IF;
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
        // Not worth rolling back
    }
}
