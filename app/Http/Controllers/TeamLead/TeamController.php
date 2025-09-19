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
            return redirect()->route('team-lead.idea.show')
                ->with('info', 'Please create an idea first. A team will be created automatically.');
        }

        $team->load(['members', 'track', 'idea']);

        return Inertia::render('TeamLead/Team/Show', [
            'team' => $team,
            'members' => $this->teamService->getTeamMembers($team)
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        // Get available editions
        $editions = \App\Models\HackathonEdition::where('is_current', true)
            ->orWhere('status', 'upcoming')
            ->get();

        $tracks = $this->trackRepository->getActive();

        return Inertia::render('TeamLead/Team/Create', [
            'editions' => $editions,
            'tracks' => $tracks
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Check if user already has a team
        $existingTeam = $this->teamService->getMyTeam($user);
        if ($existingTeam) {
            return redirect()->route('team-lead.team.show')
                ->with('error', 'You already have a team.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'edition_id' => 'required|exists:hackathon_editions,id',
            'member_emails' => 'nullable|array',
            'member_emails.*' => 'email'
        ]);

        // Create the team
        $teamData = [
            'name' => $validated['name'],
            'leader_id' => $user->id,
            'user_id' => $user->id, // For Jetstream compatibility
            'edition_id' => $validated['edition_id'],
            'status' => 'active',
            'personal_team' => false
        ];

        $team = $this->teamService->createTeam($teamData, $user);

        // Send invitations to members
        if (!empty($validated['member_emails'])) {
            foreach ($validated['member_emails'] as $email) {
                $this->sendTeamInvitation($team, $email);
            }
        }

        return redirect()->route('team-lead.team.show')
            ->with('success', 'Team created successfully! Invitations have been sent to the members.');
    }

    protected function sendTeamInvitation($team, $email)
    {
        // Generate unique token
        $token = \Illuminate\Support\Str::random(32);

        // Create invitation record
        // Note: role should be 'member' to match the enum in team_members table
        $invitation = \App\Models\TeamInvitation::create([
            'team_id' => $team->id,
            'email' => $email,
            'token' => $token,
            'role' => 'member', // Changed from 'team_member' to 'member'
            'status' => 'pending',
            'expires_at' => now()->addDays(7)
        ]);

        // Send invitation email
        \Illuminate\Support\Facades\Mail::to($email)->send(
            new \App\Mail\TeamInvitationMail($team, $invitation)
        );

        return $invitation;
    }

    public function edit()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        if (!$team) {
            return redirect()->route('team-lead.dashboard')
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
            return redirect()->route('team-lead.dashboard')
                ->with('error', 'You don\'t have a team to update');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'track_id' => 'required|exists:tracks,id',
            'description' => 'nullable|string'
        ]);

        $team = $this->teamService->updateTeamDetails($team, $validated);

        return redirect()->route('team-lead.team.show')
            ->with('success', 'Team updated successfully');
    }

    public function showAddMember()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        if (!$team) {
            return redirect()->route('team-lead.dashboard')
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
            return redirect()->route('team-lead.team.index')
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
            return redirect()->route('team-lead.dashboard')
                ->with('success', 'Team disbanded successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to disband team: ' . $e->getMessage());
        }
    }
}
