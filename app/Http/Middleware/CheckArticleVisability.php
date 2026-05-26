<?php

namespace App\Http\Middleware;

use App\Models\Article;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckArticleVisability
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next): Response
    {
        $article = $request->route('article');

        if (!$article instanceof Article) {
            return $next($request);
        }

        if ($article->status === 'published') {
            return $next($request);
        }

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'This article is not published. Authentication required.'
            ], 401);
        }

        $isAdmin = $user->role === 'admin';
        $isOwner = $user->role === 'writer' && $user->id === $article->writer_id;

        if ($isAdmin) {
            return $next($request);
        }

        if ($isOwner && in_array($article->status, ['draft', 'archived'])) {
            return $next($request);
        }

        return response()->json([
            'message' => 'You do not have permission to view this unpublished article.'
        ], 403);
    }
}
