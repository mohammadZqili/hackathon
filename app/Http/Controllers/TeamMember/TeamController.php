<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Repositories\TeamRepository;
use App\Repositories\IdeaRepository;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected $teamService;
    protected $teamRepository;
    protected $ideaRepository;

    public function __construct(
        TeamService $teamService,
        TeamRepository $teamRepository,
        IdeaRepository $ideaRepository
    ) {
        $this->teamService = $teamService;
        $this->teamRepository = $teamRepository;
        $this->ideaRepository = $ideaRepository;
    }

    public function index()
    {
        $user = auth()->user();

        // Use the same method as dashboard service for consistency
        $member = $this->teamRepository->findMemberTeam($user->id);
        $team = $member ? $member->team : null;

        if (!$team) {
            return Inertia::render('TeamMember/Team/Index', [
                'team' => null,
                'message' => 'You are not part of any team yet.'
            ]);
        }

        // Load team with all necessary relationships
        $team->load(['members', 'track', 'leader', 'idea']);

        return Inertia::render('TeamMember/Team/Index', [
            'team' => $team,
            'isLeader' => $team->leader_id === $user->id,
            'memberRole' => $member ? $member->role : null
        ]);
    }

    public function show()
    {
        $user = auth()->user();

        // Use the same method as dashboard service for consistency
        $member = $this->teamRepository->findMemberTeam($user->id);
        $team = $member ? $member->team : null;

        if (!$team) {
            return redirect()->route('team-member.dashboard')
                ->with('info', 'You are not part of any team');
        }

        // Load team with all necessary relationships
        $team->load(['members', 'track', 'idea', 'leader']);

        // Get idea for the team
        $idea = $this->ideaRepository->findByTeamId($team->id);

        return Inertia::render('TeamMember/Team/Show', [
            'team' => $team,
            'idea' => $idea,
            'members' => $team->members,
            'isLeader' => $team->leader_id === $user->id,
            'memberRole' => $member ? $member->role : null
        ]);
    }
}
