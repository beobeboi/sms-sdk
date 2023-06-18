<?php

return [
    'fpt' => [
        /*
        |--------------------------------------------------------------------------
        | API Credentials
        |--------------------------------------------------------------------------
        |
        | If you're using API credentials, change these settings. Get your
        | credentials from https://developer.sms.fpt.net/my-app.
        |
        */

        'client_id' => env('FPT_CLIENT_ID', ''),
        'client_secret' => env('FPT_CLIENT_SECRET', ''),

        /*
        |--------------------------------------------------------------------------
        | BrandName
        |--------------------------------------------------------------------------
        */

        'brand_name' => env('FPT_BRAND_NAME', ''),

        /*
        |--------------------------------------------------------------------------
        | Scopes
        |--------------------------------------------------------------------------
        */

        'mode' => env('SMS_MODE', ''),

        /*
        |--------------------------------------------------------------------------
        | Scopes
        |--------------------------------------------------------------------------
        */

        'scopes' => ['send_brandname', 'send_brandname_otp'],
        'timeout' => env('SMS_TIMEOUT', 15),
    ],
    'provider' => env('SMS_PROVIDER'),
    'country_code' => env('DEFAULT_COUNTRY_CODE', 'VN'),
    'log_sms' => env('LOG_SMS', false),
    'whitelist' => env('SMS_WHITELIST', ''),
    'enable_cache' => env('SMS_ENABLE_CACHE', false),
];
