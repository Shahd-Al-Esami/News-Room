<?php

namespace App\Console\Commands;

use App\Jobs\PublishedArticleReportJob;
use App\Models\User;
use Illuminate\Console\Command;

class AllPublishedArticlesReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:all-articles-published-report --dry-run:show the admins who will receive the report without sending the emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly published articles report to admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('dry-run')) {

//get admins who will receive the report without sending the emails
            $admins = User::where('role', 'admin')->get();

            $this->info('Admins who will receive the report:');
            foreach ($admins as $admin) {
                $this->line($admin->email);
            }
        }
        // dispatch the job to send weekly report to admins
        dispatch(new PublishedArticleReportJob())->onQueue('reports');
        $this->info('Weekly published articles report has been dispatched to the queue.');
    }
}
