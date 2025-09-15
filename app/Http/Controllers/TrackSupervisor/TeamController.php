<?php

namespace App\Http\Controllers\TrackSupervisor;

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
        $query = Team::with(['leader', 'members', 'idea', 'edition']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $teams = $query->latest()->paginate(15);

        // Add members count to each team
        $teams->getCollection()->transform(function ($team) {
            $team->members_count = $team->members->count();
            return $team;
        });

        return Inertia::render('TrackSupervisor/Teams/Index', [
            'teams' => $teams
        ]);
    }

    public function create()
    {
        $editions = Edition::orderBy('year', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('TrackSupervisor/Teams/Create', [
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

        return redirect()->route('track-supervisor.teams.index')
            ->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $team->load(['leader', 'members', 'idea', 'edition']);

        return Inertia::render('TrackSupervisor/Teams/Show', [
            'team' => $team
        ]);
    }

    public function edit(Team $team)
    {
        $team->load(['leader', 'members', 'idea', 'edition']);

        $editions = Edition::orderBy('year', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('TrackSupervisor/Teams/Edit', [
            'team' => $team,
            'editions' => $editions
        ]);
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'edition_id' => 'required|exists:editions,id',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'nullable|integer|min:1|max:10',
            'status' => 'required|in:active,inactive,disqualified'
        ]);

        $team->update($validated);

        return redirect()->route('track-supervisor.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->route('track-supervisor.teams.index')
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

        // Check if user is in the team
        if (!$team->members()->where('user_id', $userId)->exists()) {
            return back()->withErrors(['error' => 'User is not a member of this team.']);
        }

        // Remove member from team
        $team->members()->detach($userId);

        // If this was the leader, clear the leader_id
        if ($team->leader_id == $userId) {
            $team->update(['leader_id' => null]);
        }

        return back()->with('success', 'Member removed successfully.');
    }

    public function export()
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
