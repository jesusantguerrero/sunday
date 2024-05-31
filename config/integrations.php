<?php

return [
    "google" => [
        "credentials_path" => env('GOOGLE_CREDENTIALS_PATH'),
        "client_id" => env('GOOGLE_CLIENT_ID')
    ],
    "neatlancer" => [
        "client_id" => env('NEATLANCER_APP_KEY'),
        "client_secret" => env('NEATLANCER_APP_SECRET'),
    ]
];
