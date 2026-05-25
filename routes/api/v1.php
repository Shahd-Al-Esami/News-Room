<?php
namespace Routes\Api;
use App\Http\Controllers\Api\V1\ArticleController;
use Illuminate\Support\Facades\Route;

    // Define your v1 API routes here, e.g.:


        Route::middleware(['throttle:api','api.logger'])->group(function () {
   // ArticleController v1

    Route::get('/articles', [ArticleController::class, 'index']);//for all
    });

    // 2. Protected/Authenticated Endpoints (Requires authentication & authorization checks)
    Route::middleware(['auth:sanctum', 'throttle:api','api.logger'])->group(function () {

        Route::post('/articles', [ArticleController::class, 'store']);
        Route::get('/articles/{article}', [ArticleController::class, 'show'])->middleware('check.article.visibility');
        Route::put('/articles/{article}', [ArticleController::class, 'update'])->middleware('check.role');   // Supports complete overrides
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->middleware(['check.role','is.admin']);

        Route::patch('/articles/{article}/publish', [ArticleController::class, 'publishArticle'])->middleware('check.role'); // For partial updates (e.g., just changing the status to "published")



    });
