<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecreateTasksRollbackTest extends TestCase
{
    public function test_rollback_restores_the_preceding_procedure(): void
    {
        DB::shouldReceive('unprepared')->once()->with('DROP PROCEDURE IF EXISTS `recreate_tasks`')->ordered();
        DB::shouldReceive('statement')->once()->with('SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci')->ordered();
        DB::shouldReceive('unprepared')->once()->withArgs(function (string $sql) {
            $this->assertStringContainsString('CREATE PROCEDURE `recreate_tasks`', $sql);
            $this->assertStringContainsString('IN P_trigger_id INT', $sql);
            $this->assertStringContainsString('CALL recalculate_tasks', $sql);
            $this->assertStringNotContainsString('COLLATE', $sql);

            return true;
        })->ordered();

        $migration = require database_path('migrations/2026_07_16_000000_force_recreate_recreate_tasks_with_explicit_collation.php');
        $migration->down();
    }
}
