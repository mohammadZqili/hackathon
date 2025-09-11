<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        $team = $this->teamService->getMemberTeam(auth()->id());
        
        return Inertia::render('TeamMember/Team/Index', [
            'team' => $team
        ]);
    }

    public function show()
    {
        $user = auth()->user();
        $team = $this->teamService->getMemberTeam($user->id);
        
        if (!$team) {
            return redirect()->route('team-member.dashboard')
                ->with('info', 'You are not part of any team');
        }

        $team->load(['members', 'track', 'idea', 'leader']);
        
        return Inertia::render('TeamMember/Team/Show', [
            'team' => $team,
            'members' => $this->teamService->getTeamMembers($team),
            'isLeader' => $team->leader_id === $user->id
        ]);
    }
}
