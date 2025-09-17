<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TeamService;
use App\Services\IdeaService;

class EnsureTeamLeaderHasIdea
{
    protected $teamService;
    protected $ideaService;

    public function __construct(TeamService $teamService, IdeaService $ideaService)
    {
        $this->teamService = $teamService;
        $this->ideaService = $ideaService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Only apply to team leaders
        if (!$user || !$user->hasRole('team_leader')) {
            return $next($request);
        }

        // Skip for idea creation routes
        $skipRoutes = [
            'team-lead.idea.create',
            'team-lead.idea.store',
            'team-lead.team.create',
            'team-lead.team.store'
        ];

        if (in_array($request->route()->getName(), $skipRoutes)) {
            return $next($request);
        }

        // Check if team leader has a team
        $team = $this->teamService->getMyTeam($user);

        if (!$team) {
            // Redirect to team creation with a message
            if ($request->route()->getName() !== 'team-lead.dashboard') {
                return redirect()->route('team-lead.idea.create')
                    ->with('info', 'You need to create an idea first. A team will be created automatically.');
            }
        } else {
            // Check if team has an idea
            $idea = $this->ideaService->getTeamIdea($user->id);

            if (!$idea) {
                // Redirect to idea creation
                if (!in_array($request->route()->getName(), ['team-lead.dashboard', 'team-lead.idea.index'])) {
                    return redirect()->route('team-lead.idea.create')
                        ->with('info', 'You need to create an idea for your team first.');
                }
            }
        }

        return $next($request);
    }
}
