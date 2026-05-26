<?php

namespace App\Repositories\Contracts;

use App\Models\Article;
use App\Repositories\Contracts\BaseRepositoryInterface;

interface ArticleRepositoryInterface extends BaseRepositoryInterface
{

    public function getPublishedArticles(array $relations=[],array $counts=[]);
    public function publishArticle(Article $article);
    public function getOldDraftArticles(int $days);
    public function findWithDetails($id);
}
