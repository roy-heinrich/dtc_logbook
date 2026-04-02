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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'phpmailer' => [
        'host' => env('PHPMAILER_HOST'),
        'port' => env('PHPMAILER_PORT', 587),
        'debug' => (int) env('PHPMAILER_DEBUG', 0),
        'timeout' => (int) env('PHPMAILER_TIMEOUT', 10),
        'timelimit' => (int) env('PHPMAILER_TIMELIMIT', 15),
        'username' => env('PHPMAILER_USERNAME'),
        'password' => env('PHPMAILER_PASSWORD'),
        'encryption' => env('PHPMAILER_ENCRYPTION', 'tls'),
        'from_address' => env('PHPMAILER_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')),
        'from_name' => env('PHPMAILER_FROM_NAME', env('MAIL_FROM_NAME')),
    ],

];
