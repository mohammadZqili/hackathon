<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTrackSupervisorRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        $user = auth()->user();
        
        // Check if user has any of the track supervisor roles
        if ($user->hasRole('track-supervisor') || 
            $user->hasRole('track_supervisor') || 
            $user->hasRole('system-admin') ||
            $user->hasRole('system_admin')) {
            return $next($request);
        }
        
        abort(403, 'Unauthorized. You need TrackSupervisor role to access this area.');
    }
}