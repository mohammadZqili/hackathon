<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Services\HackathonEditionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Team;
use App\Models\User;

class TeamController extends Controller
{
    protected TeamService $teamService;
    protected HackathonEditionService $editionService;

    public function __construct(
        TeamService $teamService,
        HackathonEditionService $editionService
    ) {
        $this->teamService = $teamService;
        $this->editionService = $editionService;
    }
    public function index(Request $request)
    {
        $data = $this->teamService->getPaginatedTeams(
            auth()->user(),
            $request->only(['search', 'edition_id', 'status']),
            15
        );

        return Inertia::render('TrackSupervisor/Teams/Index', $data);
    }

    public function create()
    {
        $editions = $this->editionService->getAllEditions(auth()->user());

        return Inertia::render('TrackSupervisor/Teams/Create', [
            'editions' => $editions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'edition_id' => 'required|exists:hackathon_editions,id',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'nullable|integer|min:1|max:10',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        $result = $this->teamService->createTeam($validated, auth()->user());

        return redirect()->route('system-admin.teams.index')
            ->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $data = $this->teamService->getTeamDetails($team->id, auth()->user());

        return Inertia::render('TrackSupervisor/Teams/Show', $data);
    }

    public function edit(Team $team)
    {
        $data = $this->teamService->getTeamDetails($team->id, auth()->user());
        $editions = $this->editionService->getAllEditions(auth()->user());

        return Inertia::render('TrackSupervisor/Teams/Edit', array_merge($data, [
            'editions' => $editions
        ]));
    }

    public function update(Request $request, Team $team)
    {
        $result = $this->teamService->updateTeam($team->id, $request->all(), auth()->user());

        return redirect()->route('system-admin.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $this->teamService->deleteTeam($team->id, auth()->user());

        return redirect()->route('system-admin.teams.index')
            ->with('success', 'Team deleted successfully.');
    }

    public function addMember(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'send_invitation' => 'nullable|boolean'
        ]);

        $result = $this->teamService->addMemberWithInvitation($team, $validated);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['email' => $result['message']]);
    }

    public function removeMember(Team $team, $user)
    {
        // Handle both User model and string ID (ULIDs are strings)
        $userId = $user instanceof User ? $user->id : (string)$user;

        $success = $this->teamService->removeTeamMember($team, $userId);

        if ($success) {
            return back()->with('success', 'Member removed successfully.');
        }

        return back()->withErrors(['error' => 'Failed to remove member from team.']);
    }

    public function export()
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
