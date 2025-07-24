<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60);
});

Route::middleware(['api'])->group(function () {
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'service' => 'demo-back',
        ]);
    });

    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        // Protected routes can be added here
        Route::prefix('v1')->group(function () {
            // Add your API routes here
            Route::get('/status', function () {
                return response()->json([
                    'message' => 'API is working',
                    'version' => '1.0',
                ]);
            });
            // Resource routes for products
            Route::apiResource('products', ProductController::class)->except(['create', 'edit']);
            Route::get('products/export/excel', [ProductController::class, 'exportExcel']);
            Route::get('products/export/pdf', [ProductController::class, 'exportPdf']);
            // Resource routes for users
            Route::apiResource('users', UserController::class)->except(['create', 'edit']);
            Route::get('users/export/excel', [UserController::class, 'exportExcel']);
            Route::get('users/export/pdf', [UserController::class, 'exportPdf']);
            // Resource routes for roles
            Route::apiResource('roles', RoleController::class)->except(['create', 'edit']);
            Route::get('roles/export/excel', [RoleController::class, 'exportExcel']);
            Route::get('roles/export/pdf', [RoleController::class, 'exportPdf']);
        });
    });
});
