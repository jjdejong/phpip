<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sharepoint' => [
        'enabled' => env('SHAREPOINT_ENABLED', false),
        'api_url' => env('SHAREPOINT_API_URL'),
        'token_url' => env('SHAREPOINT_TOKEN_URL'),
        'client_id' => env('SHAREPOINT_CLIENT_ID'),
        'client_secret' => env('SHAREPOINT_CLIENT_SECRET'),
        'resource' => env('SHAREPOINT_RESOURCE'),
        'folder_path' => env('SHAREPOINT_FOLDER_PATH'),
        'event_codes' => array_reduce(
            explode(',', env('SHAREPOINT_EVENT_CODES', 'SR:SR,EXA:OA,EXAF:OA,ALL:GRT')),
            function($carry, $item) {
                list($key, $value) = explode(':', $item);
                $carry[$key] = $value;
                return $carry;
            },
            []
        ),
    ],

    'uspto' => [
        'enabled' => env('USPTO_ODP_ENABLED', false),
        'api_key' => env('USPTO_ODP_API_KEY'),
        // Base USPTO ODP API URL; endpoint defaults below are resolved against this value.
        'base_url' => env('USPTO_ODP_BASE_URL', 'https://api.uspto.gov'),
        // Optional override for direct application endpoint template.
        // Leave empty to use the built-in default.
        'application_endpoint' => env('USPTO_ODP_APPLICATION_ENDPOINT', '/api/v1/patent/applications/{applicationNumber}'),
        // Optional override for search endpoint.
        // Leave empty to use the built-in default.
        'search_endpoint' => env('USPTO_ODP_SEARCH_ENDPOINT', '/api/v1/patent/applications/search'),
        'search_field' => env('USPTO_ODP_SEARCH_FIELD', 'applicationNumberText'),
    ],

];
