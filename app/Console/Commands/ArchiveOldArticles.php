<?php

namespace App\Console\Commands;

use App\Jobs\ArchiveOldArticlesJob;
use App\Models\Article;
use App\Services\Api\V1\ArticleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ArchiveOldArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:archive {days=30}
        --dry-run:show the articles that would be archived without actually archiving them';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive articles that are not published and older than {days} days ,30 days by default';

    public function __construct(
        protected ArticleService $articleService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $days = (int) $this->argument('days');

        $dryRun = $this->option('dry-run');

        $articles = $this->articleService
            ->getOldDraftArticles($days);

        if ($articles->isEmpty()) {

            $message = 'No articles found for archiving.';

            $this->info($message);

            Log::info($message);

            return self::SUCCESS;
        }

        $this->info(
            "Found {$articles->count()} articles."
        );

        foreach ($articles as $article) {

            $message =
                "Article #{$article->id} - {$article->title}";

            $this->line($message);

            Log::info($message);
        }

        if ($dryRun) {

            $message =
                'Dry run enabled. No articles archived.';

            $this->warn($message);

            Log::info($message);

            return self::SUCCESS;
        }

        $updatedCount = $this->articleService
            ->archiveArticles($articles);

        $message =
            "{$updatedCount} articles archived successfully.";

        $this->info($message);

        Log::info($message);

        return self::SUCCESS;
    }



}
