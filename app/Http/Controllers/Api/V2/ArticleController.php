<?php
namespace App\Http\Controllers\Api\V2;
use App\Http\Controllers\Controller;
use App\Http\Requests\Articles\CreateArticle;
use App\Http\Requests\Articles\UpdateArticle;
use App\Http\Resources\Api\V2\ArticleResource;
use App\Models\Article;
use App\Services\Api\V1\ArticleService;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{

    public function __construct(protected ArticleService $articleService)
    {
    }


    public function index()
    {
        $articles = $this->articleService->getPublishedArticlesV2();
        //ArticleResource v2
        return ArticleResource::collection($articles);
    }


}
