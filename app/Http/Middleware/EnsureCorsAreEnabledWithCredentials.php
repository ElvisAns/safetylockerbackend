<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCorsAreEnabledWithCredentials
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Credentials', 'true');
        if (env('APP_ENV') === 'production') {
            $response->header('Access-Control-Allow-Origin', env('FRONTEND_PROD'));
        } else {
            $response->header('Access-Control-Allow-Origin', env('FRONTEND_LOCAL'));
        }
        return $response;
    }
}
