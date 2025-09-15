<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\EditionContext;
use Symfony\Component\HttpFoundation\Response;

class ResolveEditionMiddleware
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
        // Get edition from route parameter if present
        $editionId = $request->route('edition')
                     ?? $request->route('edition_id')
                     ?? $request->input('edition_id');

        if ($editionId) {
            // Try to set this as current edition
            $edition = $this->editionContext->setCurrentEdition($editionId);
            if (!$edition) {
                abort(404, 'Edition not found.');
            }
        }

        // Ensure we have a current edition
        $currentEdition = $this->editionContext->current();
        if (!$currentEdition) {
            abort(503, 'No active edition available. Please contact administrator.');
        }

        // Add current edition to request for easy access in controllers
        $request->merge(['current_edition' => $currentEdition]);

        // Share current edition with all views via Inertia
        if (class_exists(\Inertia\Inertia::class)) {
            \Inertia\Inertia::share('currentEdition', $currentEdition);
        }

        return $next($request);
    }
}