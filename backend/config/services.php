<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
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

    'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
        'sandbox' => env('ZARINPAL_SANDBOX', true),
        'request_url' => env(
            'ZARINPAL_REQUEST_URL',
            env('ZARINPAL_SANDBOX', true)
                ? 'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
                : 'https://payment.zarinpal.com/pg/v4/payment/request.json'
        ),
        'verify_url' => env(
            'ZARINPAL_VERIFY_URL',
            env('ZARINPAL_SANDBOX', true)
                ? 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json'
                : 'https://payment.zarinpal.com/pg/v4/payment/verify.json'
        ),
        'payment_url' => env(
            'ZARINPAL_PAYMENT_URL',
            env('ZARINPAL_SANDBOX', true)
                ? 'https://sandbox.zarinpal.com/pg/StartPay/'
                : 'https://www.zarinpal.com/pg/StartPay/'
        ),
    ],

];
