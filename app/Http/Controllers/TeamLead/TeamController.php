<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Repositories\TeamRepository;
use App\Repositories\TrackRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected $teamService;
    protected $teamRepository;
    protected $trackRepository;

    public function __construct(
        TeamService $teamService,
        TeamRepository $teamRepository,
        TrackRepository $trackRepository
    ) {
        $this->teamService = $teamService;
        $this->teamRepository = $teamRepository;
        $this->trackRepository = $trackRepository;
    }

    public function index()
    {
        // Redirect to show method for consistency
        return $this->show();
    }

    public function show()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        if (!$team) {
            // If no team exists, redirect to idea creation
            return redirect()->route('team-lead.idea.create')
                ->with('info', 'Please create an idea first. A team will be created automatically.');
        }

        $team->load(['members', 'track', 'idea']);
        
        return Inertia::render('TeamLead/Team/Show', [
            'team' => $team,
            'members' => $this->teamService->getTeamMembers($team)
        ]);
    }

    public function edit()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You don\'t have a team to edit');
        }

        $tracks = $this->trackRepository->getActive();
        
        return Inertia::render('TeamLead/Team/Edit', [
            'team' => $team,
            'tracks' => $tracks
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You don\'t have a team to update');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'track_id' => 'required|exists:tracks,id',
            'description' => 'nullable|string'
        ]);

        $team = $this->teamService->updateTeamDetails($team, $validated);

        return redirect()->route('team-leader.team.show')
            ->with('success', 'Team updated successfully');
    }

    public function showAddMember()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first');
        }

        return Inertia::render('TeamLead/Team/AddMember', [
            'team' => $team
        ]);
    }

    public function addMember(Request $request)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return back()->with('error', 'You don\'t have a team');
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        // Always use 'member' role for team members (leader role is set separately)
        $result = $this->teamService->addTeamMember($team, $validated['email'], 'member');

        if ($result['success']) {
            return redirect()->route('team-leader.team.index')
                ->with('success', $result['message']);
        } else {
            return back()->with('error', $result['message']);
        }
    }

    public function inviteMember(Request $request)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return back()->with('error', 'You don\'t have a team');
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $result = $this->teamService->addTeamMember($team, $validated['email']);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->with('error', $result['message']);
        }
    }

    public function removeMember($memberId)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return back()->with('error', 'You don\'t have a team');
        }

        $success = $this->teamService->removeTeamMember($team, $memberId);
        
        if ($success) {
            return back()->with('success', 'Team member removed successfully');
        } else {
            return back()->with('error', 'Failed to remove team member');
        }
    }

    public function disbandTeam()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return back()->with('error', 'You don\'t have a team to disband');
        }

        // Check if team has submitted idea
        if ($team->idea && $team->idea->status !== 'draft') {
            return back()->with('error', 'Cannot disband team with submitted idea');
        }

        try {
            $this->teamRepository->delete($team->id);
            return redirect()->route('team-leader.dashboard')
                ->with('success', 'Team disbanded successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to disband team: ' . $e->getMessage());
        }
    }
}