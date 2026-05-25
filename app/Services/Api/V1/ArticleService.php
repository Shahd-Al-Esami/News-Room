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

class ArticleService
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,private AdminService $adminService,private WriterService $writerService
    ) {}

    public function getPublishedArticlesV1()
    {
        return $this->articleRepository->getPublishedArticles(['writer:id,first_name,last_name,email','comments:id,user_id,body,created_at'],[]);
    }

   public function getPublishedArticlesV2()
    {
        return $this->articleRepository->getPublishedArticles(['writer:id,first_name,last_name,email','comments:id,user_id,body,created_at','tags:is,name,slug'],['comments']);
    }


  public function getOldDraftArticles(int $days)
    {
        return $this->articleRepository->getOldDraftArticles($days);
    }

    public function archiveArticles($articles): int
    {
        foreach ($articles as $article) {

            $article->update([
                'status' => 'archived',
            ]);
        }

        return $articles->count();
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

            PublishArticle::dispatch($article);

        // User::where('role', 'admin')->get()->each(function ($admin) use ($article) {
        //     $this->adminService->notify($admin->id, ['title' => $article->title]);
        // });

        // $this->writerService->notify($article->writer_id, ['title' => $article->title]);

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

            DB::afterCommit(function()  {
Cache::tags(['Tags'])->flush();            });
            }

            $article->comments()->delete();
            $article->attachments()->delete();


            return $this->articleRepository->delete($article);
        });
    }

    //  public function show($article)
    // {
    //     return $this->articleRepository->findWithDetails($article);
    // }
}
