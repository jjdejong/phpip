<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Recreate text-returning functions so their return collation matches utf8mb4_0900_ai_ci.
     *
     * Collation mismatch in function return values can still trigger SQLSTATE 1267 inside
     * trigger/procedure comparisons even when table columns and trigger connection collation
     * are already utf8mb4_0900_ai_ci.
     */
    public function up(): void
    {
        DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci');

        $functions = [
            'lowerword' => <<<'SQL'
                CREATE FUNCTION `lowerword`(str TEXT, word VARCHAR(5))
                RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_0900_ai_ci
                BEGIN
                    DECLARE i INT DEFAULT 1;
                    DECLARE loc INT;
                    SET loc = LOCATE(CONCAT(word, ' '), str, 2);
                    IF loc > 1 THEN
                        WHILE i <= LENGTH(str) AND loc <> 0 DO
                            SET str = INSERT(str, loc, LENGTH(word), LCASE(word));
                            SET i = loc + LENGTH(word);
                            SET loc = LOCATE(CONCAT(word, ' '), str, i);
                        END WHILE;
                    END IF;
                    RETURN str;
                END
            SQL,

            'matter_status' => <<<'SQL'
                CREATE FUNCTION `matter_status`(mid INT)
                RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_0900_ai_ci
                BEGIN
                    DECLARE mstatus TEXT;
                    SELECT CONCAT_WS(': ', event_name.name, status.event_date) INTO mstatus
                    FROM `event` status
                    JOIN event_name ON mid = status.matter_ID AND event_name.code = status.code AND event_name.status_event = 1
                    LEFT JOIN (`event` e2, event_name en2)
                        ON e2.code = en2.code
                        AND en2.status_event = 1
                        AND mid = e2.matter_id
                        AND status.event_date < e2.event_date
                    WHERE e2.matter_id IS NULL;

                    RETURN mstatus;
                END
            SQL,

            'tcase' => <<<'SQL'
                CREATE FUNCTION `tcase`(str TEXT)
                RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_0900_ai_ci
                BEGIN
                    DECLARE c CHAR(1);
                    DECLARE s TEXT;
                    DECLARE i INT DEFAULT 1;
                    DECLARE bool INT DEFAULT 1;
                    DECLARE punct CHAR(17) DEFAULT ' ()[]{},.-_!@;:?/';

                    SET s = LCASE(str);

                    WHILE i <= LENGTH(str) DO
                        SET c = SUBSTRING(s, i, 1);
                        IF LOCATE(c, punct) > 0 THEN
                            SET bool = 1;
                        ELSEIF bool = 1 THEN
                            IF c >= 'a' AND c <= 'z' THEN
                                SET s = CONCAT(LEFT(s, i - 1), UCASE(c), SUBSTRING(s, i + 1));
                                SET bool = 0;
                            ELSEIF c >= '0' AND c <= '9' THEN
                                SET bool = 0;
                            END IF;
                        END IF;
                        SET i = i + 1;
                    END WHILE;

                    SET s = lowerword(s, 'A');
                    SET s = lowerword(s, 'An');
                    SET s = lowerword(s, 'And');
                    SET s = lowerword(s, 'As');
                    SET s = lowerword(s, 'At');
                    SET s = lowerword(s, 'But');
                    SET s = lowerword(s, 'By');
                    SET s = lowerword(s, 'For');
                    SET s = lowerword(s, 'If');
                    SET s = lowerword(s, 'In');
                    SET s = lowerword(s, 'Of');
                    SET s = lowerword(s, 'On');
                    SET s = lowerword(s, 'Or');
                    SET s = lowerword(s, 'The');
                    SET s = lowerword(s, 'To');
                    SET s = lowerword(s, 'Via');

                    RETURN s;
                END
            SQL,
        ];

        foreach ($functions as $name => $sql) {
            DB::unprepared("DROP FUNCTION IF EXISTS `{$name}`");
            DB::unprepared($sql);
            echo "Recreated function: {$name}\n";
        }
    }

    public function down(): void
    {
        DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');

        $functions = [
            'lowerword' => <<<'SQL'
                CREATE FUNCTION `lowerword`(str TEXT, word VARCHAR(5))
                RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
                BEGIN
                    DECLARE i INT DEFAULT 1;
                    DECLARE loc INT;
                    SET loc = LOCATE(CONCAT(word, ' '), str, 2);
                    IF loc > 1 THEN
                        WHILE i <= LENGTH(str) AND loc <> 0 DO
                            SET str = INSERT(str, loc, LENGTH(word), LCASE(word));
                            SET i = loc + LENGTH(word);
                            SET loc = LOCATE(CONCAT(word, ' '), str, i);
                        END WHILE;
                    END IF;
                    RETURN str;
                END
            SQL,

            'matter_status' => <<<'SQL'
                CREATE FUNCTION `matter_status`(mid INT)
                RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
                BEGIN
                    DECLARE mstatus TEXT;
                    SELECT CONCAT_WS(': ', event_name.name, status.event_date) INTO mstatus
                    FROM `event` status
                    JOIN event_name ON mid = status.matter_ID AND event_name.code = status.code AND event_name.status_event = 1
                    LEFT JOIN (`event` e2, event_name en2)
                        ON e2.code = en2.code
                        AND en2.status_event = 1
                        AND mid = e2.matter_id
                        AND status.event_date < e2.event_date
                    WHERE e2.matter_id IS NULL;

                    RETURN mstatus;
                END
            SQL,

            'tcase' => <<<'SQL'
                CREATE FUNCTION `tcase`(str TEXT)
                RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
                BEGIN
                    DECLARE c CHAR(1);
                    DECLARE s TEXT;
                    DECLARE i INT DEFAULT 1;
                    DECLARE bool INT DEFAULT 1;
                    DECLARE punct CHAR(17) DEFAULT ' ()[]{},.-_!@;:?/';

                    SET s = LCASE(str);

                    WHILE i <= LENGTH(str) DO
                        SET c = SUBSTRING(s, i, 1);
                        IF LOCATE(c, punct) > 0 THEN
                            SET bool = 1;
                        ELSEIF bool = 1 THEN
                            IF c >= 'a' AND c <= 'z' THEN
                                SET s = CONCAT(LEFT(s, i - 1), UCASE(c), SUBSTRING(s, i + 1));
                                SET bool = 0;
                            ELSEIF c >= '0' AND c <= '9' THEN
                                SET bool = 0;
                            END IF;
                        END IF;
                        SET i = i + 1;
                    END WHILE;

                    SET s = lowerword(s, 'A');
                    SET s = lowerword(s, 'An');
                    SET s = lowerword(s, 'And');
                    SET s = lowerword(s, 'As');
                    SET s = lowerword(s, 'At');
                    SET s = lowerword(s, 'But');
                    SET s = lowerword(s, 'By');
                    SET s = lowerword(s, 'For');
                    SET s = lowerword(s, 'If');
                    SET s = lowerword(s, 'In');
                    SET s = lowerword(s, 'Of');
                    SET s = lowerword(s, 'On');
                    SET s = lowerword(s, 'Or');
                    SET s = lowerword(s, 'The');
                    SET s = lowerword(s, 'To');
                    SET s = lowerword(s, 'Via');

                    RETURN s;
                END
            SQL,
        ];

        foreach ($functions as $name => $sql) {
            DB::unprepared("DROP FUNCTION IF EXISTS `{$name}`");
            DB::unprepared($sql);
        }
    }
};
