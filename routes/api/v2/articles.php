<?php
namespace Routes\Api\V2;

use App\Http\Controllers\Api\V2\ArticleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    // Define your v2 API routes here, e.g.:
        Route::middleware(['throttle:api','api.logger'])->group(function () {
   // ArticleController v2

    Route::get('/articles', [ArticleController::class, 'index']);

        });

  
});
