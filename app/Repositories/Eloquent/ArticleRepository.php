<?php
namespace App\Repositories\Eloquent;

use App\Enums\ArticleCategory;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleRepository extends BaseRepository implements ArticleRepositoryInterface
{
    public function __construct( Article $model)
    {
        parent::__construct($model);
    }

public function getPublishedArticles(array $relations=[],array $counts=[])
    {
       // use DB::enableQueryLog(); in the logApiRequest middleware to check the number of queries executed

        return $this->model->query()
            ->where('status', 'published')->with($relations)->withCount($counts)
           ->latest()
            ->paginate(15);
    }


 public function getOldDraftArticles(int $days)
    {
        return Article::query()
            ->where('status', 'draft')
            ->whereDate(
                'created_at',
                '<=',
                now()->subDays($days)
            )
            ->get();
    }



    public function publishArticle($article) {

        $article->update([
        'status' => ArticleStatus::Published,
        'published_at' => now(),
    ]);

        return $article;
    }


    public function create($data){
            $article = $this->model->create([
                'title' => $data['title'],
                'slug' => $data['slug'] ?? Str::slug($data['title']),
                'category' => $data['category'] ?? ArticleCategory::General,
                'content' => $data['content'],
                'writer_id' => Auth::user()->id,
                'status' => $data['status'] ?? ArticleStatus::Draft,

            ]);
             if($article->status === ArticleStatus::Published){
            $article->published_at = now();
            $article->save();
        }

return $article;
}



        public function update($article, array $data)
        {


                $article->update([
                    'title' => $data['title'] ?? $article->title,
                    'slug' => $data['slug'] ?? Str::slug($data['title'] ?? $article->title),
                    'category' => $data['category'] ?? $article->category,
                    'content' => $data['content'] ?? $article->content,
                    'status' => $data['status'] ?? $article->status,
                ]);


            return $article;
        }



        public function delete($id)
        {

                $this->model->delete($id);

        }
public function findWithDetails($id){
return $this->model->query()
          ->with(['writer:id,first_name,last_name,email', 'comments:id,user_id,body,created_at', 'tags:id,name,slug'])
          ->withCount(['comments'])
          ->where('id', $id)
          ->firstOrFail();}

}
