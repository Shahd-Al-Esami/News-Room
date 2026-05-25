<?php

namespace App\Observers;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{

  private function clearCache(): void
    {
        Cache::tags(['Articles'])->flush();
        Cache::tags(['Users'])->flush();
    }
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
            $this->clearCache();

    }

    /**
     * Handle the Article "updated" event.
     */


    public function updated(Article $article): void
    {
        if ($article->wasChanged('status')) {
            $this->clearCache();
        }
    }

    public function deleted(Article $article): void
    {
            $this->clearCache();

    }

    public function restored(Article $article): void
    {
            $this->clearCache();

    }

    public function forceDeleted(Article $article): void
    {
            $this->clearCache();

    }






}
