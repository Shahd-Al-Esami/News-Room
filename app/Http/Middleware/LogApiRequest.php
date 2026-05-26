<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{


    public function handle(Request $request, Closure $next): Response
    {
// Before Middleware -> the middleware will log the api request details after $request execution

        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $durationInms = ($endTime - $startTime) * 1000;
        $queryCount = count(DB::getQueryLog());

        $request->attributes->set('log_duration', $durationInms);
        $request->attributes->set('log_query_count', $queryCount);

        $response->headers->set('X-Debug-Query-Count', $queryCount);
        $response->headers->set('X-Debug-Execution-Time', round($durationInms, 2) . 'ms');

        return $response;
    }


    public function terminate(Request $request, Response $response): void
    {
        $durationInms = $request->attributes->get('log_duration');
        $user = Auth::user();
// After Middleware -> the middleware will log the api request details after $response is generated

        try {
            DB::table('request_logs')->insert([
                'user_id'     => $user?->id ?? null,
                'method'      => $request->method(),
                'url'         => $request->url(),
                'duration'    => $durationInms,
                'status_code' => $response->getStatusCode(),
                'ip_address'  => $request->ip(),
                'created_at'  => now()
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to save request log: ' . $e->getMessage());
        }
    }
}
