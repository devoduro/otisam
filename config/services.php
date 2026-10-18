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

    // Which gateway sends SMS: "speso" (production) or "log" (development and tests).
    'sms_provider' => env('SMS_PROVIDER', 'log'),

    // Speso business SMS API, same account as jhs_portal.
    'speso' => [
        'api_key' => env('SPESO_API_KEY'),
        'sender_id' => env('SPESO_SMS_SENDER_ID', 'OTISAM'),
        'base_url' => env('SPESO_BASE_URL', 'https://business.speso.co/api/v1'),
    ],

    // Paystack. Keys live in .env only; never in the database or front-end code.
    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
        // Optional allowlist of webhook source IPs (comma separated). Empty = signature check only.
        'webhook_ips' => array_filter(array_map('trim', explode(',', (string) env('PAYSTACK_WEBHOOK_IPS', '')))),
        // Paystack requires an email; students without one get user{id}@this domain on the transaction.
        'placeholder_email_domain' => env('PAYSTACK_PLACEHOLDER_EMAIL_DOMAIN', 'students.otisam.app'),
    ],

];
