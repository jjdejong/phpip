<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatterCategoryTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('matter_category')->insertOrIgnore([

            [
                'code' => 'AGR',
                'ref_prefix' => 'AGR',
                'category' => json_encode(['en' => 'Agreement', 'fr' => 'Accord', 'de' => 'Vereinbarung']),
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'DSG',
                'ref_prefix' => 'DSG',
                'category' => json_encode(['en' => 'Design', 'fr' => 'Dessin ou modèle', 'de' => 'Design']),
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'FTO',
                'ref_prefix' => 'OPI',
                'category' => json_encode(['en' => 'Freedom to Operate', 'fr' => 'Liberté d\'exploitation', 'de' => 'Freedom to Operate']),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'LTG',
                'ref_prefix' => 'LTG',
                'category' => json_encode(['en' => 'Litigation', 'fr' => 'Contentieux', 'de' => 'Rechtsstreit']),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OP',
                'ref_prefix' => 'OP',
                'category' => json_encode(['en' => 'Opposition (patent)', 'fr' => 'Opposition (brevet)', 'de' => 'Einspruch (Patent)']),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OPI',
                'ref_prefix' => 'OPI',
                'category' => json_encode(['en' => 'Opinion', 'fr' => 'Avis', 'de' => 'Gutachten']),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'OTH',
                'ref_prefix' => 'OTH',
                'category' => json_encode(['en' => 'Others', 'fr' => 'Autres', 'de' => 'Sonstige']),
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PAT',
                'ref_prefix' => 'PAT',
                'category' => json_encode(['en' => 'Patent', 'fr' => 'Brevet', 'de' => 'Patent']),
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'SO',
                'ref_prefix' => 'SO',
                'category' => json_encode(['en' => 'Soleau Envelop', 'fr' => 'Enveloppe Soleau', 'de' => 'Soleau-Umschlag']),
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'SR',
                'ref_prefix' => 'OPI',
                'category' => json_encode(['en' => 'Search', 'fr' => 'Recherche', 'de' => 'Recherche']),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TM',
                'ref_prefix' => 'TM',
                'category' => json_encode(['en' => 'Trademark', 'fr' => 'Marque', 'de' => 'Marke']),
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TMOP',
                'ref_prefix' => 'OP',
                'category' => json_encode(['en' => 'Opposition (TM)', 'fr' => 'Opposition (Marque)', 'de' => 'Widerspruch (Marke)']),
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TS',
                'ref_prefix' => 'TS',
                'category' => json_encode(['en' => 'Trade Secret', 'fr' => 'Secret de fabrique', 'de' => 'Geschäftsgeheimnis']),
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'UC',
                'ref_prefix' => 'UC',
                'category' => json_encode(['en' => 'Utility Certificate', 'fr' => 'Certificat d\'utilité', 'de' => 'Gebrauchszertifikat']),
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'UM',
                'ref_prefix' => 'UM',
                'category' => json_encode(['en' => 'Utility Model', 'fr' => 'Modèle d\'utilité', 'de' => 'Gebrauchsmuster']),
                'display_with' => 'PAT',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'WAT',
                'ref_prefix' => 'WAT',
                'category' => json_encode(['en' => 'Watch', 'fr' => 'Surveillance', 'de' => 'Überwachung']),
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}