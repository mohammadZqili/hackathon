<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Team;
use App\Models\User;
use App\Models\Edition;

class TeamController extends Controller
{
    protected TeamService $teamService;
    protected TeamRepository $teamRepository;

    public function __construct(TeamService $teamService, TeamRepository $teamRepository)
    {
        $this->teamService = $teamService;
        $this->teamRepository = $teamRepository;
    }
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'edition_id' => $request->input('edition_id'),
            'status' => $request->input('status')
        ];

        $teams = $this->teamRepository->getPaginatedWithFilters($filters, 15);

        // Add members count to each team
        $teams->getCollection()->transform(function ($team) {
            $team->members_count = $team->members->count();
            return $team;
        });

        return Inertia::render('SystemAdmin/Teams/Index', [
            'teams' => $teams,
            'filters' => $filters
        ]);
    }

    public function create()
    {
        $editions = Edition::orderBy('year', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('SystemAdmin/Teams/Create', [
            'editions' => $editions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'edition_id' => 'required|exists:editions,id',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'nullable|integer|min:1|max:10',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'edition_id' => $validated['edition_id'],
            'leader_id' => $validated['leader_id'] ?? null,
            'max_members' => $validated['max_members'] ?? 5,
            'status' => 'active'
        ]);

        // Add leader as a member if specified
        if (!empty($validated['leader_id'])) {
            $team->members()->attach($validated['leader_id'], ['role' => 'leader']);
        }

        // Add other members
        if (!empty($validated['member_ids'])) {
            foreach ($validated['member_ids'] as $memberId) {
                if ($memberId != $validated['leader_id']) {
                    $team->members()->attach($memberId, ['role' => 'member']);
                }
            }
        }

        return redirect()->route('system-admin.teams.index')
            ->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $team->load(['leader', 'members', 'idea', 'edition']);

        return Inertia::render('SystemAdmin/Teams/Show', [
            'team' => $team
        ]);
    }

    public function edit(Team $team)
    {
        $team->load(['leader', 'members', 'idea', 'edition']);

        $editions = Edition::orderBy('year', 'desc')
            ->where('is_current',1)
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('SystemAdmin/Teams/Edit', [
            'team' => $team,
            'editions' => $editions
        ]);
    }

    public function update(Request $request, Team $team)
    {
        $team->update($request->all());

        return redirect()->route('system-admin.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

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
