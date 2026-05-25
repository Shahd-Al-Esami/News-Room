<?php

namespace App\Listeners;

use App\Events\PublishArticle;

use Illuminate\Support\Facades\Log;

//syncronous listener to log article published event
class LogArticlePublishedListener
{
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
        Log::info('Article published ',[
        'article_id' => $event->article->id,
        'title'=>$event->article->title,
        'author_id'=>$event->article->writer_id
        ]);
    }
}
