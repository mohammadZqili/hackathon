<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
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
        $team = $this->teamService->getTeamByLeader(auth()->id());
        
        return Inertia::render('TeamLead/Team/Index', [
            'team' => $team,
            'canCreateTeam' => !$team
        ]);
    }

    public function create()
    {
        if ($this->teamService->getTeamByLeader(auth()->id())) {
            return redirect()->route('team-lead.team.index')
                ->with('error', 'You already have a team');
        }

        $tracks = $this->teamService->getAvailableTracks();
        
        return Inertia::render('TeamLead/Team/Create', [
            'tracks' => $tracks
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'track_id' => 'required|exists:tracks,id',
            'description' => 'nullable|string'
        ]);

        $team = $this->teamService->createTeam(auth()->id(), $validated);

        return redirect()->route('team-lead.team.index')
            ->with('success', 'Team created successfully');
    }

    public function addMember(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'nullable|string|in:developer,designer,marketer,other'
        ]);

        try {
            $member = $this->teamService->addTeamMember(
                auth()->id(),
                $validated['email'],
                $validated['role'] ?? 'member'
            );

            return back()->with('success', 'Team member added successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeMember($memberId)
    {
        try {
            $this->teamService->removeTeamMember(auth()->id(), $memberId);
            return back()->with('success', 'Team member removed successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
