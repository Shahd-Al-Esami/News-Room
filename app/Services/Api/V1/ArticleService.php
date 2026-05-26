<?php


namespace App\Services\Api\V1;

use App\Events\PublishArticle;
use App\Helpers\SecureFileUploadHelper;
use App\Models\Article;
use App\Models\User;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Services\Api\V1\AdminService;
use App\Services\Api\V1\WriterService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleService
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,private AdminService $adminService,private WriterService $writerService
    ) {}

    public function getPublishedArticlesV1()
    {
      // Using page query param to make unique cache keys per page

        $page = request('page', 1);
        $cacheKey = "published_articles_v1_page_{$page}";
        $lockKey = "lock_" . $cacheKey;

        return Cache::tags(['Articles'])->remember($cacheKey, now()->addDay(), function () use ($cacheKey, $lockKey) {

            return Cache::lock($lockKey, 10)->block(5, function () use ($cacheKey) {
// Double check to avoid cache stampede
$data = Cache::tags(['Articles'])->get($cacheKey);

                if ($data !== null) {
                    return $data;
                }

                Log::info(" Safe DB Fetch Under Remember-Lock Sequence for: {$cacheKey}");
//get data from database and store it in cache
                return $this->articleRepository->getPublishedArticles(
                    ['writer:id,first_name,last_name,email', 'comments:id,user_id,body,created_at'],
                    []
                );
            });
        });
    }


   public function getPublishedArticlesV2()
    {
        // Using page query param to make unique cache keys per page

        $page = request('page', 1);
        $cacheKey = "published_articles_v2_page_{$page}";
        $lockKey = "lock_" . $cacheKey;

        return Cache::tags(['Articles'])->remember($cacheKey, now()->addDay(), function () use ($cacheKey, $lockKey) {

            return Cache::lock($lockKey, 10)->block(5, function () use ($cacheKey) {
// Double check to avoid cache stampede
$data = Cache::tags(['Articles'])->get($cacheKey);

                if ($data !== null) {
                    return $data;
                }

                Log::info(" Safe DB Fetch Under Remember-Lock Sequence for: {$cacheKey}");
//get data from database and store it in cache
        return $this->articleRepository->getPublishedArticles(['writer:id,first_name,last_name,email','comments:id,user_id,body,created_at','tags:id,name,slug'],['comments']);

            });
        });
    }


  public function getOldDraftArticles(int $days)
    {
        return $this->articleRepository->getOldDraftArticles($days);
    }

    public function archiveArticles($articles): int
    {
        if ($articles->isEmpty())
            return 0;

    $archivedArticles = Article::whereIn('id', $articles->pluck('id'))
        ->update([
            'status' => 'archived',
            'updated_at' => now()
        ]);
        //return number of archived articles after update thier status to archived
         return $archivedArticles;
    }


     public function generateMonthlyArticlesReport()
    {
        return User::query()
            ->withCount([
                'articles as published_articles_count'
                    => function ($query) {

                    $query->whereMonth(
                        'published_at',
                        now()->month
                    );
                }
            ])
            ->get();
    }



public function publishArticle(Article $article)
    {
        $publishedArticle = $this->articleRepository->publishArticle($article);

// Dispatch event to notify readers about the new published article
// and to notify the writer about the publication
//and to notify admins about the new published article
//and record published article in log file

            PublishArticle::dispatch($article);


        return $publishedArticle;
    }


    public function createArticle(array $data)
    {
        return DB::transaction(function () use ($data) {

            $tags = $data['tags'] ?? [];
            $attachments = $data['attachments'] ?? [];
            unset($data['tags'], $data['attachments']);

            $article = $this->articleRepository->create($data);


            if($article->status === 'published')
            PublishArticle::dispatch($article)->afterCommit();



            if (!empty($tags)) {
                $article->tags()->sync($tags);
                // Invalidate cache for tags after syncing article tags
                DB::afterCommit(function()  {
                Cache::tags(['Tags'])->flush();
            });            }

            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    $folderPath = "article/{$article->id}/images";
                    $path = SecureFileUploadHelper::store($file, $folderPath);
                    $article->attachments()->create([
                        'file_path' => $path,
                    ]);
                }
            }


            return $article->load(['writer', 'tags']);
        });
    }

    public function updateArticle(Article $article, array $data)
    {
        return DB::transaction(function() use ($article, $data) {

            $tags = $data['tags'] ?? null;
            $attachments = $data['attachments'] ?? [];
            unset($data['tags'], $data['attachments']);

            $this->articleRepository->update($article, $data);

            if (is_array($tags)) {
                $article->tags()->sync($tags);
                // Invalidate cache for tags after updating article tags
                DB::afterCommit(function()  {
                Cache::tags(['Tags'])->flush();
            });

            }

            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    $folderPath = "article/{$article->id}/images";
                    $path = SecureFileUploadHelper::store($file, $folderPath);
                    $article->attachments()->create([
                        'file_path' => $path
                    ]);
                }
            }



            return $article->load(['writer', 'tags']);
        });
    }


    public function deleteArticle(Article $article)
    {
        return DB::transaction(function() use ($article) {

            if($article->tags()->exists()){

            $article->tags()->detach();
// Invalidate cache for tags after detaching article tags
            DB::afterCommit(function()  {
                Cache::tags(['Tags'])->flush();
                    });
            }

            $article->comments()->delete();
            $article->attachments()->delete();


            return $this->articleRepository->delete($article->id);
        });
    }

     public function show($id)
    {
        return $this->articleRepository->findWithDetails($id);
    }
}
