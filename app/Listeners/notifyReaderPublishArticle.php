<?php

namespace App\Listeners;

use App\Events\PublishArticle;
use App\Jobs\SendReaderArticleNotificationsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class notifyReaderPublishArticle 
{
     use InteractsWithQueue,SerializesModels;


    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PublishArticle $event): void
    {
        // dispatch job to send notifications to readers
        dispatch(new SendReaderArticleNotificationsJob($event->article))->onQueue('Notifications');
    }
}
