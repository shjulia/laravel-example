<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'key' => env('MAILGUN_KEY')
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Entities\User\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'checkr' => [
        'app_key' => env('CHECKR_APP_KEY'),
        'url' => env('CHECKR_URL')
    ],

    'ai' => [
        'dl_key' => env('DL_AI_KEY'),
        'dl_url' => env('DL_AI_URL')
    ],

    'map' => [
        'key_matrix' => env('DISTANCE_MATRIX_API_KEY'),
        'key_places' => env('PLACES_API_KEY'),
        'gmap_api_key' => env('GMAP_API_KEY')
    ],

    'dwolla' => [
        #'app_key' => env('DWOLLA_APP_KEY'),
        'app_public_key' => env('DWOLLA_APP_PUBLIC_KEY'),
        #'app_secret' => env('DWOLLA_APP_SECRET'),
        'app_secret_key' => env('DWOLLA_APP_SECRET_KEY'),
        #'api_client' => env('DWOLLA_API_CLIENT'),
        'api_url' => env('DWOLLA_API_URL'),
        #'app_funding_source' => env('APP_FUNDING_SOURCE'),
        'app_fs' => env('DWOLLA_APP_FUNDING_SOURCE')
    ],

    'onesignal' => [
        'app_id' => env('ONESIGNAL_APP_ID')
    ],
    'idscan' => [
        'api_key' => env('ID_SCAN_API_KEY')
    ],
    're_captcha' => [
        'public_key' => env('GOOGLE_RECAPTCHA_KEY'),
        'secret_key' => env('GOOGLE_RECAPTCHA_SECRET')
    ],
    'sms' => [
        'app_key' => env('TWILIO_APP_KEY'),
        'app_secret' => env('TWILIO_APP_SECRET'),
        'from' => env('TWILIO_FROM')
    ]
];
