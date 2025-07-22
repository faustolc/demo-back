<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['api'])->group(function () {
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'service' => 'demo-back'
        ]);
    });

    // Example API endpoints
    Route::prefix('v1')->group(function () {
        // Add your API routes here
        Route::get('/status', function () {
            return response()->json([
                'message' => 'API is working',
                'version' => '1.0'
            ]);
        });
    });
});
