<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassifierTypeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('classifier_type')->insertOrIgnore([

            [
                'code' => 'ABS',
                'type' => json_encode(['en' => 'Abstract', 'fr' => 'Abrégé', 'de' => 'Zusammenfassung']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'AGR',
                'type' => json_encode(['en' => 'Agreement', 'fr' => 'Accord', 'de' => 'Vereinbarung']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'BU',
                'type' => json_encode(['en' => 'Business Unit', 'fr' => 'Unité commerciale', 'de' => 'Geschäftsbereich']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'DESC',
                'type' => json_encode(['en' => 'Description', 'fr' => 'Description', 'de' => 'Beschreibung']),
                'main_display' => 0,
                'for_category' => 'PAT',
                'display_order' => 5,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'EVAL',
                'type' => json_encode(['en' => 'Evaluation', 'fr' => 'Évaluation', 'de' => 'Bewertung']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'IMG',
                'type' => json_encode(['en' => 'Image', 'fr' => 'Image', 'de' => 'Bild']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'IPC',
                'type' => json_encode(['en' => 'Int. Pat. Class.', 'fr' => 'Class. Int. des Brevets', 'de' => 'Int. Pat. Klass.']),
                'main_display' => 1,
                'for_category' => 'PAT',
                'display_order' => 15,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'KW',
                'type' => json_encode(['en' => 'Keyword', 'fr' => 'Mot-clé', 'de' => 'Stichwort']),
                'main_display' => 1,
                'for_category' => null,
                'display_order' => 10,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'LNK',
                'type' => json_encode(['en' => 'Link', 'fr' => 'Lien', 'de' => 'Link']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'LOC',
                'type' => json_encode(['en' => 'Location', 'fr' => 'Lieu', 'de' => 'Standort']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'ORG',
                'type' => json_encode(['en' => 'Organization', 'fr' => 'Organisation', 'de' => 'Organisation']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PA',
                'type' => json_encode(['en' => 'Prior Art', 'fr' => 'Art antérieur', 'de' => 'Stand der Technik']),
                'main_display' => 0,
                'for_category' => 'PAT',
                'display_order' => 20,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PROD',
                'type' => json_encode(['en' => 'Product', 'fr' => 'Produit', 'de' => 'Produkt']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'PROJ',
                'type' => json_encode(['en' => 'Project', 'fr' => 'Projet', 'de' => 'Projekt']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TECH',
                'type' => json_encode(['en' => 'Technology', 'fr' => 'Technologie', 'de' => 'Technologie']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TIT',
                'type' => json_encode(['en' => 'Title', 'fr' => 'Titre', 'de' => 'Titel']),
                'main_display' => 1,
                'for_category' => null,
                'display_order' => 5,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TITAL',
                'type' => json_encode(['en' => 'Alt. Title', 'fr' => 'Titre alternatif', 'de' => 'Alternativer Titel']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TITEN',
                'type' => json_encode(['en' => 'English Title', 'fr' => 'Titre anglais', 'de' => 'Englischer Titel']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TITOF',
                'type' => json_encode(['en' => 'Official Title', 'fr' => 'Titre officiel', 'de' => 'Offizieller Titel']),
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TM',
                'type' => json_encode(['en' => 'Trademark', 'fr' => 'Marque', 'de' => 'Marke']),
                'main_display' => 1,
                'for_category' => 'TM',
                'display_order' => 5,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TMCL',
                'type' => json_encode(['en' => 'Class (TM)', 'fr' => 'Classe (Marque)', 'de' => 'Klasse (Marke)']),
                'main_display' => 1,
                'for_category' => 'TM',
                'display_order' => 10,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'code' => 'TMTYP',
                'type' => json_encode(['en' => 'Type (TM)', 'fr' => 'Type (Marque)', 'de' => 'Typ (Marke)']),
                'main_display' => 0,
                'for_category' => 'TM',
                'display_order' => 15,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}