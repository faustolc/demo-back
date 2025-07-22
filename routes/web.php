<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - API Only Mode
|--------------------------------------------------------------------------
|
| This application is configured for API use only.
| Please use the /api routes instead.
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'This is an API-only application. Please use /api endpoints.',
        'api_documentation' => '/api/v1/status',
        'health_check' => '/api/health'
    ], 200);
});
