<?php

namespace App\Console\Commands;

use App\Services\Api\V1\ArticleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishedArticlesWriterReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:published-articles-writer-report
    --dry-run:preview report without logging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate report for published articles for each writer and log it';

    public function __construct(
        protected ArticleService $articleService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $report = $this->articleService
            ->generateMonthlyArticlesReport();

        if ($report->isEmpty()) {

            $message = 'No report data found.';

            $this->info($message);

            if (! $dryRun) {
                Log::info($message);
            }

            return self::SUCCESS;
        }

        $this->info('Monthly Articles Report');

        foreach ($report as $writer) {

            $message =
                "{$writer->first_name} {$writer->last_name}: "
                ."{$writer->published_articles_count} articles";

            // terminal output
            $this->line($message);

            // skip logs in dry-run
            if (! $dryRun) {
                Log::info($message);
            }
        }

        if ($dryRun) {

            $this->warn(
                'Dry run enabled. No logs were written.'
            );

            return self::SUCCESS;
        }

        Log::info('Monthly report generated.');

        return self::SUCCESS;
    }
}
