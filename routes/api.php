<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;




  // Public routes — no token required
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login']);

    // Protected routes — valid Sanctum token required
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me',     [AuthController::class, 'me']);

        });


        Route::prefix('v1')->group(base_path('routes/api/v1/articles.php'));


        Route::prefix('v2')->group(base_path('routes/api/v2/articles.php'));


