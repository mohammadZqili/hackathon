<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\EditionContext;
use Symfony\Component\HttpFoundation\Response;

class TrackSupervisorScopeMiddleware
{
    protected EditionContext $editionContext;

    public function __construct(EditionContext $editionContext)
    {
        $this->editionContext = $editionContext;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Only apply to track supervisors
        if (!$user || !$user->hasRole('track_supervisor')) {
            return $next($request);
        }

        // Get current edition
        $currentEdition = $this->editionContext->current();
        if (!$currentEdition) {
            abort(503, 'No active edition available.');
        }

        // Get tracks assigned to this supervisor in the current edition
        $assignedTracks = $user->tracksInEdition($currentEdition->id)->get();
        $trackIds = $assignedTracks->pluck('id')->toArray();

        if (empty($trackIds)) {
            abort(403, 'You are not assigned to any track in the current edition.');
        }

        // Add track IDs to request for easy access in controllers
        $request->merge([
            'supervisor_track_ids' => $trackIds,
            'supervisor_edition_id' => $currentEdition->id
        ]);

        return $next($request);
    }
}
