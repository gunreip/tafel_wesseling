<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------
| GET /healthz
| - Shallow: returns 200 if app boots
| - Deep:    /healthz?deep=1 also pings DB and Cache
| HTTP 200 on success, else 503.
*/
Route::get('/healthz', function (\Illuminate\Http\Request $request) {
    $deep = $request->boolean('deep', false);
    $status = 'ok';
    $checks = ['app' => 'ok'];

    if ($deep) {
        try {
            DB::connection()->getPdo();
            $checks['db'] = 'ok';
        } catch (\Throwable $e) {
            $checks['db'] = 'fail';
            $status = 'fail';
        }

        try {
            $key = 'healthz:ping';
            Cache::put($key, 'pong', 5);
            $checks['cache'] = Cache::get($key) === 'pong' ? 'ok' : 'fail';
            if ($checks['cache'] !== 'ok') {
                $status = 'fail';
            }
        } catch (\Throwable $e) {
            $checks['cache'] = 'fail';
            $status = 'fail';
        }
    }

    $payload = [
        'status' => $status,
        'checks' => $checks,
        'meta' => ['time' => now()->toIso8601String()],
    ];

    return response()->json($payload, $status === 'ok' ? 200 : 503);
})->name('healthz');
