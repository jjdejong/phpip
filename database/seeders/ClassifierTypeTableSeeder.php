<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClassifierTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('classifier_type')->insertOrIgnore(array(

            array(
                'code' => 'ABS',
                'type' => 'Abstract',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'AGR',
                'type' => 'Agreement',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'BU',
                'type' => 'Business Unit',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'DESC',
                'type' => 'Description',
                'main_display' => 1,
                'for_category' => null,
                'display_order' => 1,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'EVAL',
                'type' => 'Evaluation',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'IMG',
                'type' => 'Image',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'IPC',
                'type' => 'IPC',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'KW',
                'type' => 'Keyword',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'LNK',
                'type' => 'Link',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 1,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'LOC',
                'type' => 'Location',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'ORG',
                'type' => 'Organization',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'PA',
                'type' => 'Prior Art',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'PROD',
                'type' => 'Product',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'PROJ',
                'type' => 'Project',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'TECH',
                'type' => 'Technology',
                'main_display' => 0,
                'for_category' => null,
                'display_order' => 127,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'TIT',
                'type' => 'Title',
                'main_display' => 1,
                'for_category' => null,
                'display_order' => 1,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'TITAL',
                'type' => 'Alt. Title',
                'main_display' => 1,
                'for_category' => 'PAT',
                'display_order' => 4,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'TITEN',
                'type' => 'English Title',
                'main_display' => 1,
                'for_category' => 'PAT',
                'display_order' => 3,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'TITOF',
                'type' => 'Official Title',
                'main_display' => 1,
                'for_category' => 'PAT',
                'display_order' => 2,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'TM',
                'type' => 'Trademark',
                'main_display' => 1,
                'for_category' => 'TM',
                'display_order' => 1,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'TMCL',
                'type' => 'Class (TM)',
                'main_display' => 0,
                'for_category' => 'TM',
                'display_order' => 2,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'TMTYP',
                'type' => 'Type (TM)',
                'main_display' => 0,
                'for_category' => 'TM',
                'display_order' => 3,
                'notes' => null,
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
