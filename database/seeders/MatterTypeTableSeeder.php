<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatterTypeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('matter_type')->insertOrIgnore([

            [
                'code' => 'CIP',
                'type' => json_encode(['en' => 'Continuation in Part', 'fr' => 'Continuation partielle', 'de' => 'Teilfortsetzungsanmeldung']),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'CNT',
                'type' => json_encode(['en' => 'Continuation', 'fr' => 'Continuation', 'de' => 'Fortsetzungsanmeldung']),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'DIV',
                'type' => json_encode(['en' => 'Divisional', 'fr' => 'Divisionnaire', 'de' => 'Teilanmeldung']),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PRO',
                'type' => json_encode(['en' => 'Provisional', 'fr' => 'Provisoire', 'de' => 'Vorläufige Anmeldung']),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'REI',
                'type' => json_encode(['en' => 'Reissue', 'fr' => 'Redélivrance', 'de' => 'Neuerteilung']),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'REX',
                'type' => json_encode(['en' => 'Re-examination', 'fr' => 'Réexamen', 'de' => 'Neuprüfungsverfahren']),
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}