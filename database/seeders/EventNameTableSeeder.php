<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventNameTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('event_name')->insertOrIgnore([
            ['code' => 'ABA', 'name' => json_encode(['en' => 'Abandoned'])],
            ['code' => 'ABO', 'name' => json_encode(['en' => 'Abandon Original'])],
            ['code' => 'ADV', 'name' => json_encode(['en' => 'Advisory Action'])],
            ['code' => 'ALL', 'name' => json_encode(['en' => 'Allowance'])],
            ['code' => 'APL', 'name' => json_encode(['en' => 'Appeal'])],
            ['code' => 'CAN', 'name' => json_encode(['en' => 'Cancelled'])],
            ['code' => 'CLO', 'name' => json_encode(['en' => 'Closed'])],
            ['code' => 'COM', 'name' => json_encode(['en' => 'Communication'])],
            ['code' => 'CRE', 'name' => json_encode(['en' => 'Created'])],
            ['code' => 'DAPL', 'name' => json_encode(['en' => 'Decision on Appeal'])],
            ['code' => 'DBY', 'name' => json_encode(['en' => 'Draft By'])],
            ['code' => 'DEX', 'name' => json_encode(['en' => 'Deadline Extended'])],
            ['code' => 'DPAPL', 'name' => json_encode(['en' => 'Decision on Pre-Appeal'])],
            ['code' => 'DRA', 'name' => json_encode(['en' => 'Drafted'])],
            ['code' => 'DW', 'name' => json_encode(['en' => 'Deemed withrawn'])],
            ['code' => 'EHK', 'name' => json_encode(['en' => 'Extend to Hong Kong'])],
            ['code' => 'ENT', 'name' => json_encode(['en' => 'Entered'])],
            ['code' => 'EOP', 'name' => json_encode(['en' => 'End of Procedure'])],
            ['code' => 'EXA', 'name' => json_encode(['en' => 'Examiner Action'])],
            ['code' => 'EXAF', 'name' => json_encode(['en' => 'Examiner Action (Final)'])],
            ['code' => 'EXP', 'name' => json_encode(['en' => 'Expiry'])],
            ['code' => 'FAP', 'name' => json_encode(['en' => 'File Notice of Appeal'])],
            ['code' => 'FBY', 'name' => json_encode(['en' => 'File by'])],
            ['code' => 'FDIV', 'name' => json_encode(['en' => 'File Divisional'])],
            ['code' => 'FIL', 'name' => json_encode(['en' => 'Filed'])],
            ['code' => 'FOP', 'name' => json_encode(['en' => 'File Opposition'])],
            ['code' => 'FPR', 'name' => json_encode(['en' => 'Further Processing'])],
            ['code' => 'FRCE', 'name' => json_encode(['en' => 'File RCE'])],
            ['code' => 'GRT', 'name' => json_encode(['en' => 'Granted'])],
            ['code' => 'INV', 'name' => json_encode(['en' => 'Invalidated'])],
            ['code' => 'LAP', 'name' => json_encode(['en' => 'Lapsed'])],
            ['code' => 'NPH', 'name' => json_encode(['en' => 'National Phase'])],
            ['code' => 'OPP', 'name' => json_encode(['en' => 'Opposition'])],
            ['code' => 'OPR', 'name' => json_encode(['en' => 'Oral Proceedings'])],
            ['code' => 'ORE', 'name' => json_encode(['en' => 'Opposition rejected'])],
            ['code' => 'PAY', 'name' => json_encode(['en' => 'Pay'])],
            ['code' => 'PDES', 'name' => json_encode(['en' => 'Post designation'])],
            ['code' => 'PFIL', 'name' => json_encode(['en' => 'Parent Filed'])],
            ['code' => 'PR', 'name' => json_encode(['en' => 'Publication of Reg.'])],
            ['code' => 'PREP', 'name' => json_encode(['en' => 'Prepare'])],
            ['code' => 'PRI', 'name' => json_encode(['en' => 'Priority Claim'])],
            ['code' => 'PRID', 'name' => json_encode(['en' => 'Priority Deadline'])],
            ['code' => 'PROD', 'name' => json_encode(['en' => 'Produce'])],
            ['code' => 'PSR', 'name' => json_encode(['en' => 'Publication of SR'])],
            ['code' => 'PUB', 'name' => json_encode(['en' => 'Published'])],
            ['code' => 'RCE', 'name' => json_encode(['en' => 'Request Continued Examination'])],
            ['code' => 'REC', 'name' => json_encode(['en' => 'Received'])],
            ['code' => 'REF', 'name' => json_encode(['en' => 'Refused'])],
            ['code' => 'REG', 'name' => json_encode(['en' => 'Registration'])],
            ['code' => 'REM', 'name' => json_encode(['en' => 'Reminder'])],
            ['code' => 'REN', 'name' => json_encode(['en' => 'Renewal'])],
            ['code' => 'REP', 'name' => json_encode(['en' => 'Respond'])],
            ['code' => 'REQ', 'name' => json_encode(['en' => 'Request'])],
            ['code' => 'RSTR', 'name' => json_encode(['en' => 'Restriction Req.'])],
            ['code' => 'SOL', 'name' => json_encode(['en' => 'Sold'])],
            ['code' => 'SOP', 'name' => json_encode(['en' => 'Summons to Oral Proc.'])],
            ['code' => 'SR', 'name' => json_encode(['en' => 'Search Report'])],
            ['code' => 'SUS', 'name' => json_encode(['en' => 'Suspended'])],
            ['code' => 'TRF', 'name' => json_encode(['en' => 'Transformation'])],
            ['code' => 'TRS', 'name' => json_encode(['en' => 'Transfer'])],
            ['code' => 'VAL', 'name' => json_encode(['en' => 'Validate'])],
            ['code' => 'WAT', 'name' => json_encode(['en' => 'Watch'])],
            ['code' => 'WIT', 'name' => json_encode(['en' => 'Withdrawal'])],
        ]);
    }
}