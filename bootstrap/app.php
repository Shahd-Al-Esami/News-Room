<?php

use App\Http\Middleware\CheckArticleVisability;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\isAdmin;
use App\Http\Middleware\LogApiRequest;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
        'check.article.visibility' => CheckArticleVisability::class,
        'api.logger' => LogApiRequest::class,
        'is.admin' => isAdmin::class,
        'check.role' => CheckRole::class,

    ]);
    //global middelware
    $middleware->append([
      SecurityHeadersMiddleware::class,
    ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
