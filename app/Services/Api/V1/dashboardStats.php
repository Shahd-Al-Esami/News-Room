<?php
namespace App\Services\Api\V1;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStats{


public function getStats()
    {
        return [
            'count_articles' => $this->countArticles(),
            'top_writers' => $this->topWriters(),
            'comments_count' => $this->commentsCount(),
            'top_tags' => $this->topTags(),
        ];
    }

    public function countArticles()
    {
        //invalidation cache in the observer
        $statsArticles=Cache::tags(['dashboard','Articles'])->remember('totalArticles', now()->addMinutes(10), function () {


        return Cache::lock('lock:totalArticles', 10)->block(5,function () {
//double check to avoid cache stampede
        $cached = Cache::tags(['dashboard', 'Articles'])->get('totalArticles');
                if ($cached !== null) {
                    return $cached;
                }
                   return
                           $totalArticles = DB::table('articles')->count('id');


                 });
    });

    return $statsArticles;

    }
    public function topWriters()
    {

    $topWriters=Cache::tags(['dashboard','Users'])->remember('topWriters', now()->addMinutes(10), function () {
        return Cache::lock('lock:topWriters', 10)->block(5,function () {
$cached = Cache::tags(['dashboard', 'Users'])->get('topWriters');
                if ($cached !== null) {
                    return $cached;
                }
        
                          $topWriters=DB::table('users')->join('articles', 'users.id', '=', 'articles.writer_id')
                            ->select('users.id','users.first_name', DB::raw('COUNT(articles.id) as articles_count'))
                             ->groupBy('users.id','users.first_name')
                           ->orderByDesc('articles_count')->limit(5) ->get()->toArray();


                 });
    });
    return $topWriters;
    }
     public function commentsCount()
    {
    $commentsCount=Cache::tags(['dashboard','Comments'])->remember('commentsCount', now()->addMinutes(10), function () {
        return Cache::lock('lock:commentsCount', 10)->block(5,function () {
            //double check to avoid cache stampede
$cached = Cache::tags(['dashboard', 'Comments'])->get('commentsCount');
                if ($cached !== null) {
                    return $cached;
                }

        return  $commentsCount = DB::table('comments')->count('id');


                 });

    });

    return $commentsCount;
    }





    public function topTags(){
        //invalidation in article service
        return Cache::tags(['dashboard','Tags'])->remember('topTags', now()->addMinutes(10), function () {
            return Cache::lock('lock:topTags', 10)->block(5,function () {
                //double check to avoid cache stampede
                $cached = Cache::tags(['dashboard', 'Tags'])->get('topTags');
                if ($cached !== null) {
                    return $cached;
                }
                       return
                              $topTags = Tag::withCount('articles')
                                ->orderByDesc('articles_count')
                                ->take(10)
                                ->get(['id', 'name','articles_count'])->toArray();

                     });
        });
    }




}
