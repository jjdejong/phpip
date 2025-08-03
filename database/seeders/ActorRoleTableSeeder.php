<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActorRoleTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('actor_role')->insertOrIgnore([
            ['code' => 'ADV', 'name' => json_encode(['en' => 'Adversary']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'AGT', 'name' => json_encode(['en' => 'Primary Agent']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'AGT2', 'name' => json_encode(['en' => 'Secondary Agent']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ANN', 'name' => json_encode(['en' => 'Annuity Agent']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'APP', 'name' => json_encode(['en' => 'Applicant']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CLI', 'name' => json_encode(['en' => 'Client']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CNT', 'name' => json_encode(['en' => 'Contact']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DBA', 'name' => json_encode(['en' => 'DB Administrator']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DBRO', 'name' => json_encode(['en' => 'DB Read-Only']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DBRW', 'name' => json_encode(['en' => 'DB Read/Write']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DEL', 'name' => json_encode(['en' => 'Delegate']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FAGT', 'name' => json_encode(['en' => 'Former Agent']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FOWN', 'name' => json_encode(['en' => 'Former Owner']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'INV', 'name' => json_encode(['en' => 'Inventor']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'LCN', 'name' => json_encode(['en' => 'Licensee']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'OFF', 'name' => json_encode(['en' => 'Patent Office']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'OPP', 'name' => json_encode(['en' => 'Opponent']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'OWN', 'name' => json_encode(['en' => 'Owner']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'PAY', 'name' => json_encode(['en' => 'Payor']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'PTNR', 'name' => json_encode(['en' => 'Partner']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'TRA', 'name' => json_encode(['en' => 'Translator']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'WRI', 'name' => json_encode(['en' => 'Writer']), 'display_order' => 127, 'shareable' => 0, 'show_ref' => 0, 'show_company' => 0, 'show_rate' => 0, 'show_date' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}