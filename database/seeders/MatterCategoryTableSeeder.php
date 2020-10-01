<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MatterCategoryTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('matter_category')->insertOrIgnore(array(

            array(
                'code' => 'AGR',
                'ref_prefix' => 'AGR',
                'category' => 'Agreement',
                'display_with' => 'OTH',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'DSG',
                'ref_prefix' => 'DSG',
                'category' => 'Design',
                'display_with' => 'TM',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'FTO',
                'ref_prefix' => 'OPI',
                'category' => 'Freedom to Operate',
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'LTG',
                'ref_prefix' => 'LTG',
                'category' => 'Litigation',
                'display_with' => 'LTG',
                'creator' => 'system',
                'updater' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ),

            array(
                'code' => 'OP',
                'ref_prefix' => 'OPP',
                'category' => 'Opposition (patent]',
                    'display_with' => 'LTG',
                    'creator' => 'system',
                    'updater' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),

                array(
                    'code' => 'OPI',
                    'ref_prefix' => 'OPI',
                    'category' => 'Opinion',
                    'display_with' => 'LTG',
                    'creator' => 'system',
                    'updater' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),

                array(
                    'code' => 'OTH',
                    'ref_prefix' => 'OTH',
                    'category' => 'Others',
                    'display_with' => 'OTH',
                    'creator' => 'system',
                    'updater' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),

                array(
                    'code' => 'PAT',
                    'ref_prefix' => 'PAT',
                    'category' => 'Patent',
                    'display_with' => 'PAT',
                    'creator' => 'system',
                    'updater' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),

                array(
                    'code' => 'SO',
                    'ref_prefix' => 'PAT',
                    'category' => 'Soleau Envelop',
                    'display_with' => 'PAT',
                    'creator' => 'system',
                    'updater' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),

                array(
                    'code' => 'SR',
                    'ref_prefix' => 'SR-',
                    'category' => 'Search',
                    'display_with' => 'LTG',
                    'creator' => 'system',
                    'updater' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),

                array(
                    'code' => 'TM',
                    'ref_prefix' => 'TM-',
                    'category' => 'Trademark',
                    'display_with' => 'TM',
                    'creator' => 'system',
                    'updater' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),

                array(
                    'code' => 'TMOP',
                    'ref_prefix' => 'TOP',
                    'category' => 'Opposition (TM]',
                        'display_with' => 'TM',
                        'creator' => 'system',
                        'updater' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ),

                    array(
                        'code' => 'TS',
                        'ref_prefix' => 'TS-',
                        'category' => 'Trade Secret',
                        'display_with' => 'PAT',
                        'creator' => 'system',
                        'updater' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ),

                    array(
                        'code' => 'UC',
                        'ref_prefix' => 'PAT',
                        'category' => 'Utility Certificate',
                        'display_with' => 'PAT',
                        'creator' => 'system',
                        'updater' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ),

                    array(
                        'code' => 'UM',
                        'ref_prefix' => 'PAT',
                        'category' => 'Utility Model',
                        'display_with' => 'PAT',
                        'creator' => 'system',
                        'updater' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ),

                    array(
                        'code' => 'WAT',
                        'ref_prefix' => 'WAT',
                        'category' => 'Watch',
                        'display_with' => 'TM',
                        'creator' => 'system',
                        'updater' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ),
                ));
    }
}
