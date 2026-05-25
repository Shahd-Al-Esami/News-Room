<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogApiRequest
{
    public function handle(Request $request, Closure $next)
    {
// Before Middleware -> the middleware will log the api request details after $request execution
         DB::enableQueryLog();

       $startTime = microtime(true);
        $response = $next($request);
        
// After Middleware -> the middleware will log the api request details after $response is generated
        $endTime = microtime(true);
        $durationInms = ($endTime - $startTime) * 1000;
         $queryCount = count(DB::getQueryLog());
// Log the request details to the database
        $user = Auth::user();
        DB::table('request_logs')->insert([
            'user_id' => $user?->id ?? null,
            'method' => $request->method(),
            'url' => $request->url(),
            'duration' => $durationInms,
            'status_code' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'created_at' => now()
        ]);

 return $response->withHeaders([
            'X-Debug-Query-Count' => $queryCount,
            'X-Debug-Execution-Time' => "{$durationInms}ms",
        ]);
        }
}
