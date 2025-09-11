<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckVisitorRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('visitor')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
