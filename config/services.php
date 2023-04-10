<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
     "google" => [
        "credentials_path" => env('GOOGLE_CREDENTIALS_PATH'),
        "client_id" => env('GOOGLE_CLIENT_ID')
    ],
    "laravelpassport" => [
        "client_id" => env('NEATLANCER_APP_KEY'),
        "client_secret" => env('NEATLANCER_APP_SECRET'),
        "redirect" => '/oauth/accept',
        "host" => env('SSO_URL')
    ]
];
