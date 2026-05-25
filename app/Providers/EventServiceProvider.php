<?php

namespace App\Providers;

use App\Events\PublishArticle;
use App\Events\UserRegistered;
use App\Listeners\LogArticlePublishedListener;
use App\Listeners\notifyAdminPublishArticle;
use App\Listeners\notifyReaderPublishArticle;
use App\Listeners\notifyWriterPublishArticle;
use App\Listeners\SendWelcomeEmailListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

protected $listen = [
    PublishArticle::class => [
                notifyReaderPublishArticle::class,
                notifyAdminPublishArticle::class,
                notifyWriterPublishArticle::class,
                LogArticlePublishedListener::class

                  ],

        UserRegistered::class => [
            SendWelcomeEmailListener::class,
        ],
];




    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
