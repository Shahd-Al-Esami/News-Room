<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\Articles\CreateArticle;
use App\Http\Requests\Articles\UpdateArticle;
use App\Http\Resources\Api\V1\ArticleResource;
use App\Models\Article;
use App\Services\Api\V1\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{

    public function __construct(protected ArticleService $articleService)
    {
    }


    public function index()
    {
        $articles = $this->articleService->getPublishedArticlesV1();
        return ArticleResource::collection($articles);
    }


    public function publishArticle(Article $article): ArticleResource
    {
        return new ArticleResource(
            $this->articleService->publishArticle($article)
        );
    }

    public function store(CreateArticle $request): ArticleResource
    {
        $article = $this->articleService->createArticle($request->validated());
        return new ArticleResource($article);
    }

    public function update(UpdateArticle $request, Article $article): ArticleResource
    {
        $updatedArticle = $this->articleService->updateArticle($article, $request->validated());
        return new ArticleResource($updatedArticle);
    }

    public function destroy(Article $article): JsonResponse
    {
        $this->articleService->deleteArticle($article);
        return response()->json(['message' => 'Article deleted successfully'], 200);
    }

    public function show(Article $article): ArticleResource
    {
        $article=$this->articleService->show($article->id);
        return new ArticleResource($article);
    }
}
