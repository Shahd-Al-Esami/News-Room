<?php

namespace App\Listeners;
use App\Events\PublishArticle;
use App\Models\User;
use App\Services\Api\V1\AdminService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class notifyAdminPublishArticle implements ShouldQueue
{
    use InteractsWithQueue,SerializesModels;
public string $queue='Notifications';

public $tries=3;
public $timeout=120;
public $backOff=10;
    /**
     * Create the event listener.
     */
    public function __construct(private AdminService $adminService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PublishArticle $event): void
    {
             User::where('role', 'admin')->get()->each(function ($admin) use ($event) {
                
            $this->adminService->notify($admin->id, ['title' => $event->article->title]);
        });
    }

}
