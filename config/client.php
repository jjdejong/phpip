<?php

/*
 * Settings for client users - every login outside the staff roles.
 */

$visibleTaskCodes = env('CLIENT_VISIBLE_TASK_CODES');

return [
    /*
     * Task codes (event_name.code) that client users may see. Every other task,
     * including any code added later, stays hidden from them: internal work such
     * as Prepare (invoice), Reminder, File by or Draft by is not their business.
     *
     * Override per installation with a comma-separated CLIENT_VISIBLE_TASK_CODES
     * in .env, which replaces this list.
     */
    'visible_task_codes' => $visibleTaskCodes
        ? array_map('trim', explode(',', $visibleTaskCodes))
        : [
            'EHK',  // Extend to Hong Kong
            'FAP',  // File Notice of Appeal
            'FDIV', // File Divisional
            'FOP',  // File Opposition
            'FPR',  // Further Processing
            'FRCE', // File RCE
            'NPH',  // National Phase
            'OPR',  // Oral Proceedings
            'PAY',  // Pay
            'PRID', // Priority Deadline
            'PROD', // Produce
            'REN',  // Renewal
            'REP',  // Respond
            'REQ',  // Request
            'TRF',  // Transformation
            'VAL',  // Validate
        ],
];
