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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Integrasi Kelompok 1
    |--------------------------------------------------------------------------
    |
    | Kelompok 1 memakai database terpisah. Komunikasi antar sistem lewat
    | HTTP API. Konfigurasi endpoint dan tokennya di .env, bukan di sini.
    |
    */

    'kelompok1' => [
        'url' => env('KELOMPOK1_API_URL'),
        'token' => env('KELOMPOK1_API_TOKEN'),
        'timeout' => env('KELOMPOK1_API_TIMEOUT', 10),
    ],

];
