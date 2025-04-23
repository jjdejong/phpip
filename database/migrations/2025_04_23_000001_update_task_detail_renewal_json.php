<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTaskDetailRenewalJson extends Migration
{
    public function up()
    {
        // Update procedures to wrap RYear in JSON
        DB::statement("DROP PROCEDURE IF EXISTS insert_recurring_renewals");
        DB::statement("
        CREATE PROCEDURE insert_recurring_renewals(
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

            SELECT ebase.event_date, estart.event_date, country.renewal_first, matter.expire_date, matter.origin 
            INTO BaseDate, StartDate, FirstRenewal, ExpiryDate, Origin
            FROM country
            JOIN matter ON country.iso = matter.country
            JOIN event estart ON estart.matter_id = matter.id AND estart.id = P_trigger_id
            JOIN event ebase ON ebase.matter_id = matter.id
            WHERE country.renewal_start = estart.code
            AND country.renewal_base = ebase.code;

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
                IF (DueDate < Now() - INTERVAL 6 MONTH AND Origin != 'WO') OR (DueDate < (Now() - INTERVAL 19 MONTH) AND Origin = 'WO') THEN
                    SET RYear = RYear + 1;
                    ITERATE renloop;
                END IF;
                INSERT INTO task (trigger_id, code, due_date, detail, rule_used, assigned_to, creator, created_at, updated_at)
                VALUES (P_trigger_id, 'REN', DueDate, JSON_OBJECT('en', RYear), P_rule_id, P_responsible, P_user, Now(), Now());
                SET RYear = RYear + 1;
            END WHILE;
        END");
    }

    public function down()
    {
        // Restore original procedure
        DB::statement("DROP PROCEDURE IF EXISTS insert_recurring_renewals");
        DB::statement("
        CREATE PROCEDURE insert_recurring_renewals(
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

            SELECT ebase.event_date, estart.event_date, country.renewal_first, matter.expire_date, matter.origin 
            INTO BaseDate, StartDate, FirstRenewal, ExpiryDate, Origin
            FROM country
            JOIN matter ON country.iso = matter.country
            JOIN event estart ON estart.matter_id = matter.id AND estart.id = P_trigger_id
            JOIN event ebase ON ebase.matter_id = matter.id
            WHERE country.renewal_start = estart.code
            AND country.renewal_base = ebase.code;

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
                IF (DueDate < Now() - INTERVAL 6 MONTH AND Origin != 'WO') OR (DueDate < (Now() - INTERVAL 19 MONTH) AND Origin = 'WO') THEN
                    SET RYear = RYear + 1;
                    ITERATE renloop;
                END IF;
                INSERT INTO task (trigger_id, code, due_date, detail, rule_used, assigned_to, creator, created_at, updated_at)
                VALUES (P_trigger_id, 'REN', DueDate, RYear, P_rule_id, P_responsible, P_user, Now(), Now());
                SET RYear = RYear + 1;
            END WHILE;
        END");
    }
}