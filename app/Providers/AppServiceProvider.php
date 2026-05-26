<?php

namespace App\Providers;


use App\Models\Article;
use App\Notifications\Contracts\NotificationServiceInterface;
use App\Notifications\Implementation\DatabaseNotification;
use App\Notifications\Implementation\EmailNotification;
use App\Observers\ArticleObserver;
use App\Services\Api\V1\AdminService;
use App\Services\Api\V1\WriterService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(WriterService::class)
        ->needs(NotificationServiceInterface::class)
        ->give(EmailNotification::class);

        $this->app->when(AdminService::class)
        ->needs(NotificationServiceInterface::class)
        ->give(DatabaseNotification::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
          Article::observe(ArticleObserver::class);


        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

    //for logging all database queries
    if(!app()->isProduction()) {
        DB::listen(function ($query) {
        Log::info($query->sql, [
            'bindings' => $query->bindings,
            'time' => $query->time . 'ms'
        ]);
    });
    }
    // Model::shouldBeStrict(!app()->isProduction());


    }
}
