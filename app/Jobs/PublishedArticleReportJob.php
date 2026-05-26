<?php

namespace App\Jobs;

use App\Mail\PublishedArticlesReportMail;
use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PublishedArticleReportJob implements ShouldQueue
{
     use InteractsWithQueue,Queueable,Dispatchable,SerializesModels;

   public $tries = 3;
   public $timeout=120;
   public $backOff=10;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */

    public function handle()
    {
        // جلب مقالات الأسبوع الماضي
        $articles = Article::where('status', 'published')
            ->where('published_at', '>=', now()->subWeek())
            ->get();

        // جلب المدراء
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new PublishedArticlesReportMail($articles));
        }
    }

    public function failed(\Throwable $exception)
    {
        // Handle the failure, e.g., log the error or notify developers
        Log::error('Failed to send published articles report: ' . $exception->getMessage());
    }
}
