<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\User;
use App\Notifications\ReaderArticleNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReaderArticleNotificationsJob implements ShouldQueue
{
     use InteractsWithQueue,Queueable,Dispatchable,SerializesModels;

     public $tries=3;
public $timeout=120;
public $backoff=10;

    /**
     * Create a new job instance.
     */
    public function __construct(public Article $article)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        User::where('role','reader')->chunk(100,function($readers){
            foreach($readers as $reader){
                $reader->notify(new ReaderArticleNotification($this->article));
            }
        });

    }
    public function failed(\Throwable $exception)
    {
        // Handle the failure, e.g., log the error or notify developers
        Log::error('Failed to send reader article notifications: ' . $exception->getMessage());
    }
}
