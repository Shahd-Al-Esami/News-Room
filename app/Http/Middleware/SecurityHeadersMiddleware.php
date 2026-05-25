<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | Force JSON Accept Header
        |--------------------------------------------------------------------------
        */
        if (
            !$request->headers->has('Accept') ||
            $request->headers->get('Accept') === '*/*'
        ) {
            $request->headers->set(
                'Accept',
                'application/json'
            );
        }

        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | Remove Technology Fingerprints
        |--------------------------------------------------------------------------
        */
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        /*
        |--------------------------------------------------------------------------
        | Prevent Clickjacking
        |--------------------------------------------------------------------------
        */
        $response->headers->set(
            'X-Frame-Options',
            'DENY'
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent MIME Type Sniffing
        |--------------------------------------------------------------------------
        */
        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        /*
        |--------------------------------------------------------------------------
        | Adobe Cross Domain Policies
        |--------------------------------------------------------------------------
        */
        $response->headers->set(
            'X-Permitted-Cross-Domain-Policies',
            'none'
        );

        /*
        |--------------------------------------------------------------------------
        | Referrer Policy
        |--------------------------------------------------------------------------
        */
        $response->headers->set(
            'Referrer-Policy',
            'no-referrer'
        );

        /*
        |--------------------------------------------------------------------------
        | Strict Transport Security (Production Only)
        |--------------------------------------------------------------------------
        */
        if (app()->environment('production')) {

            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
