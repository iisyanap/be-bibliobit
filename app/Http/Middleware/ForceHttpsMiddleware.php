<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ForceHttpsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Log::info("Request Scheme: " . $request->getScheme());
        Log::info("Request URL: " . $request->fullUrl());
        if (!$request->secure()) {
            Log::warning("Redirecting to HTTPS: " . $request->getRequestUri());
            return redirect()->secure($request->getRequestUri());
        }
        return $next($request);
    }
}