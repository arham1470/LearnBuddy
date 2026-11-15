<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Supported: "payhere", "stripe", "mock"
    |
    */
    'default' => env('PAYMENT_GATEWAY', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | Sri Lankan Rupee (LKR) is the default settlement currency.
    |
    */
    'currency' => env('PAYMENT_DEFAULT_CURRENCY', 'LKR'),

    'gateways' => [
        'stripe' => [
            'sandbox' => env('STRIPE_SANDBOX', true),
            'base_url' => env('STRIPE_BASE_URL', 'https://mockgateway.com/api/base/stripe-zzrjuo'),
            'secret_key' => env('STRIPE_SECRET_KEY', 'sk_test_Bc6KMFUEs5TBCPUFKdl4t5XHr8Y2Fvw9m6jPIglvBDN66oW3IQFDDGX43zTMXQ5t9uOT0s6MF1aFerkiWAR4Ev0p8Siwnzb77qw'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
        ],

        'payhere' => [
            'sandbox' => env('PAYHERE_SANDBOX', true),
            'merchant_id' => env('PAYHERE_MERCHANT_ID', ''),
            'merchant_secret' => env('PAYHERE_MERCHANT_SECRET', ''),
            'app_id' => env('PAYHERE_APP_ID', ''),
            'app_secret' => env('PAYHERE_APP_SECRET', ''),
            'checkout_url' => env('PAYHERE_SANDBOX', true)
                ? 'https://sandbox.payhere.lk/pay/checkout'
                : 'https://www.payhere.lk/pay/checkout',
            'api_url' => env('PAYHERE_SANDBOX', true)
                ? 'https://sandbox.payhere.lk/merchant/v1'
                : 'https://www.payhere.lk/merchant/v1',
            'return_url' => env('PAYHERE_RETURN_URL'),
            'cancel_url' => env('PAYHERE_CANCEL_URL'),
            'notify_url' => env('PAYHERE_NOTIFY_URL'),
        ],

        'mock' => [
            'sandbox' => true,
            'name' => 'Mock Payment Gateway (Testing)',
        ],
    ],
];
