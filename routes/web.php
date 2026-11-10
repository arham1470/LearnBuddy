<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/up/database', function () {
    try {
        DB::select('SELECT 1');
        $sslStatus = DB::select("SHOW STATUS LIKE 'Ssl_cipher'");
        $cipher = ! empty($sslStatus) ? ($sslStatus[0]->Value ?? $sslStatus[0]->value ?? null) : null;

        return response()->json([
            'status' => 'healthy',
            'driver' => DB::connection()->getDriverName(),
            'ssl_active' => ! empty($cipher),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'unhealthy',
            'message' => 'Database service unavailable',
        ], 503);
    }
});
