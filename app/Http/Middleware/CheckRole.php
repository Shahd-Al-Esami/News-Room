<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user=Auth::user();
        $writer=$user->role ==='writer';
        if($user && $writer && $user->id === $request->route('article')->writer_id){
            return $next($request);
        }
        return response()->json(['message'=>'Unauthorized'],403);

    }
}
