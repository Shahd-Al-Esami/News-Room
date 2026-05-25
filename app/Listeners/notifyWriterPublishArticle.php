<?php

namespace App\Listeners;

use App\Events\PublishArticle;
use App\Services\Api\V1\WriterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class notifyWriterPublishArticle implements ShouldQueue
{
    use InteractsWithQueue,SerializesModels;

public string $queue='Notifications';
public $tries=3;
public $timeout=120;
public $backoff=10;
    /**
     * Create the event listener.
     */
    public function __construct(private WriterService $writerService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PublishArticle $event): void
    {
        $this->writerService->notify($event->article->writer_id, ['title' => $event->article]);

    }
}
