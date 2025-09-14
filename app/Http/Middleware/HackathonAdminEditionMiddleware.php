<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HackathonAdminEditionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('hackathon_admin')) {
            abort(403, 'Unauthorized. Hackathon Admin access required.');
        }

        $user = auth()->user();

        // Get edition from route parameter if present
        $editionId = $request->route('edition') 
                     ?? $request->route('edition_id') 
                     ?? $request->input('edition_id')
                     ?? session('current_edition_id');

        // If specific edition is requested, check access
        if ($editionId) {
            if (!$user->canAccessEdition($editionId)) {
                abort(403, 'You do not have access to this edition.');
            }
            // Store current edition in session
            session(['current_edition_id' => $editionId]);
        } else {
            // Get user's accessible editions
            $accessibleEditions = $user->getAccessibleEditionIds();
            
            if (empty($accessibleEditions)) {
                abort(403, 'You are not assigned to any editions. Please contact the system administrator.');
            }

            // Set the first accessible edition as current if not set
            if (!session('current_edition_id') || !in_array(session('current_edition_id'), $accessibleEditions)) {
                session(['current_edition_id' => $accessibleEditions[0]]);
            }
        }

        // Add edition filter to request for easy access in controllers
        $request->merge(['edition_filter' => session('current_edition_id')]);

        return $next($request);
    }
}
