<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessApiQueryParameters
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Proses parameter filter
        if ($request->has('filter') && is_string($request->filter)) {
            $request->merge(['filter' => json_decode($request->filter, true)]);
        }
        
        return $next($request);
    }
}