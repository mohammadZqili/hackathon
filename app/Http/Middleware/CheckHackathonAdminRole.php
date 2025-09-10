<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckHackathonAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        $user = auth()->user();
        
        // Check if user has any of the hackathon admin roles
        if ($user->hasRole('hackathon-admin') || 
            $user->hasRole('hackathon_admin') || 
            $user->hasRole('system-admin') ||
            $user->hasRole('system_admin')) {
            return $next($request);
        }
        
        abort(403, 'Unauthorized. You need HackathonAdmin role to access this area.');
    }
}