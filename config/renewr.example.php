<?php

return [
    'api_key' => env('RENEWR_API_KEY', ''),
    'url' => env('RENEWR_API_URL', 'https://demo-api.renewr.io/api/external/patent/with-renewal-event-and-fees'),
    'skip_done' => true, // Skip already cleared renewals
    'fee_calculation' => [
        'renewr_fee' => env('RENEWR_FEE', 0), // Base fee charged by Renewr per operation
        'our_fee' => env('RENEWR_OUR_FEE', 0), // Our standard service fee
        'threshold' => 1000, // Threshold amount for progressive fee calculation. Set to 0 if you use a fixed proportional margin
        'below_percentage' => 0.2, // Initial percentage applied below threshold
        'above_percentage' => 0.15, // Percentage reached at threshold. Set to your fixed margin if you don't want progressive fees
    ],
    // For the demo API
    'jwt_url' => 'https://auth.arkyan.com/realms/renewr-demo/protocol/openid-connect/token',
    'demo_username' => 'user',
    'demo_password' => 'password'
];
