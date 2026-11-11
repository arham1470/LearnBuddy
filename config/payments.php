<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Supported: "payhere", "mock"
    |
    */
    'default' => env('PAYMENT_GATEWAY', 'payhere'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | Sri Lankan Rupee (LKR) is the default CBSL-compliant settlement currency.
    |
    */
    'currency' => env('PAYMENT_DEFAULT_CURRENCY', 'LKR'),

    'gateways' => [
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
