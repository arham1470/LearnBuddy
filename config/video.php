<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Video Provider
    |--------------------------------------------------------------------------
    |
    | Supported providers: "mux", "null"
    |
    */
    'default' => env('VIDEO_PROVIDER', 'mux'),

    /*
    |--------------------------------------------------------------------------
    | Playback Token Time-to-Live (Seconds)
    |--------------------------------------------------------------------------
    |
    | Duration before a signed HLS playback JWT expires.
    | Recommended: 300 to 900 seconds (5 - 15 minutes).
    |
    */
    'token_ttl' => (int) env('VIDEO_TOKEN_TTL', 600),

    /*
    |--------------------------------------------------------------------------
    | Mux Configuration
    |--------------------------------------------------------------------------
    */
    'mux' => [
        'token_id' => env('MUX_TOKEN_ID'),
        'token_secret' => env('MUX_TOKEN_SECRET'),
        'signing_key_id' => env('MUX_SIGNING_KEY_ID'),
        'signing_key_private' => env('MUX_SIGNING_KEY_PRIVATE'),
        'webhook_secret' => env('MUX_WEBHOOK_SECRET'),
        'cors_origin' => env('MUX_CORS_ORIGIN', env('APP_URL', '*')),
        'base_url' => env('MUX_BASE_URL', 'https://api.mux.com'),
    ],

];
