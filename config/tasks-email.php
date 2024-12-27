<?php

return [
    'phpip_url' => env('APP_URL', 'http://localhost:8000'),
    'email_from' => env('MAIL_FROM_ADDRESS'),
    'email_to' => env('MAIL_TO'),
    'email_bcc' => env('MAIL_BCC', env('MAIL_TO')),
];
