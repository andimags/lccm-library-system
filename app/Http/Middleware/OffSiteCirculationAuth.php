<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OffSiteCirculationAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->temp_role == 'librarian') {
            return $next($request);
        }

        $circulationId = $request->route('id');
        $circulation = \App\Models\OffSiteCirculation::find($circulationId);

        if ($circulation && $circulation->borrower_id == auth()->user()->id) {
            return $next($request);
        }

        return route('dashboard.index');
    }
}
