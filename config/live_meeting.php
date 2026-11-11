<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Live Class Meeting Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "jitsi", "null"
    |
    */
    'default' => env('LIVE_MEETING_PROVIDER', 'jitsi'),

    /*
    |--------------------------------------------------------------------------
    | Time Window Buffer Settings (Minutes)
    |--------------------------------------------------------------------------
    |
    | Number of minutes prior to scheduled start time that joining is permitted.
    |
    */
    'student_join_buffer_minutes' => (int) env('LIVE_STUDENT_JOIN_BUFFER_MINUTES', 15),
    'instructor_join_buffer_minutes' => (int) env('LIVE_INSTRUCTOR_JOIN_BUFFER_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Jitsi Meet Configuration
    |--------------------------------------------------------------------------
    |
    | For 8x8 JaaS (Jitsi as a Service):
    | - domain: "8x8.vc"
    | - app_id: "vpaas-magic-cookie-xxxx"
    | - api_key_id: "vpaas-magic-cookie-xxxx/yyyy"
    | - private_key: path or string of RSA private key
    |
    | For Self-Hosted Jitsi Meet:
    | - domain: "meet.yourdomain.lk"
    | - secret: JWT shared secret
    |
    */
    'jitsi' => [
        'domain' => env('JITSI_DOMAIN'),
        'app_id' => env('JITSI_APP_ID'),
        'api_key_id' => env('JITSI_API_KEY_ID'),
        'private_key' => env('JITSI_PRIVATE_KEY'),
        'secret' => env('JITSI_SECRET'),
        'room_prefix' => env('JITSI_ROOM_PREFIX', 'learnbuddy'),
    ],

];
