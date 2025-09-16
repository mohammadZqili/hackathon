<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Services\HackathonEditionService;
use App\Services\EditionContext;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Team;
use App\Models\User;

class TeamController extends Controller
{
    protected TeamService $teamService;
    protected HackathonEditionService $editionService;
    protected EditionContext $editionContext;

    public function __construct(
        TeamService $teamService,
        HackathonEditionService $editionService,
        EditionContext $editionContext
    ) {
        $this->teamService = $teamService;
        $this->editionService = $editionService;
        $this->editionContext = $editionContext;
    }
    public function index(Request $request)
    {
        $edition = $this->editionContext->current();
        $user = auth()->user();

        // Get track IDs assigned to this supervisor in current edition
        $trackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();

        // Add filters for edition and tracks
        $filters = array_merge($request->only(['search', 'status']), [
            'edition_id' => $edition->id,
            'track_ids' => $trackIds
        ]);

        $data = $this->teamService->getPaginatedTeams(
            $user,
            $filters,
            15
        );

        $data['current_edition'] = $edition;
        $data['assigned_tracks'] = $trackIds;

        return Inertia::render('TrackSupervisor/Teams/Index', $data);
    }

    public function create()
    {
        $edition = $this->editionContext->current();
        $user = auth()->user();

        // Get all tracks assigned to this supervisor
        $assignedTracks = $user->tracksInEdition($edition->id)->get();

        if ($assignedTracks->isEmpty()) {
            abort(403, 'You are not assigned to any track in the current edition.');
        }

        return Inertia::render('TrackSupervisor/Teams/Create', [
            'edition' => $edition,
            'assigned_tracks' => $assignedTracks,
            'tracks' => $assignedTracks // For compatibility
        ]);
    }

    public function store(Request $request)
    {
        $edition = $this->editionContext->current();
        $user = auth()->user();

        // Get all track IDs assigned to this supervisor
        $assignedTrackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();

        if (empty($assignedTrackIds)) {
            abort(403, 'You are not assigned to any track in the current edition.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'track_id' => ['required', 'exists:tracks,id', function($attribute, $value, $fail) use ($assignedTrackIds) {
                if (!in_array($value, $assignedTrackIds)) {
                    $fail('You can only create teams in your assigned tracks.');
                }
            }],
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'nullable|integer|min:1|max:10',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        // Force the team to be in current edition
        $validated['edition_id'] = $edition->id;

        $result = $this->teamService->createTeam($validated, $user);

        return redirect()->route('track-supervisor.teams.index')
            ->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        // Check policy
        $this->authorize('view', $team);

        $data = $this->teamService->getTeamDetails($team->id, auth()->user());

        return Inertia::render('TrackSupervisor/Teams/Show', $data);
    }

    public function edit(Team $team)
    {
        // Check policy
        $this->authorize('update', $team);

        $edition = $this->editionContext->current();
        $data = $this->teamService->getTeamDetails($team->id, auth()->user());
        $assignedTracks = auth()->user()->tracksInEdition($edition->id)->get();

        return Inertia::render('TrackSupervisor/Teams/Edit', array_merge($data, [
            'edition' => $edition,
            'editions' => [$edition], // Only current edition for track supervisors
            'assigned_tracks' => $assignedTracks,
            'tracks' => $assignedTracks // For compatibility
        ]));
    }

    public function update(Request $request, Team $team)
    {
        // Check policy
        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'max_members' => 'nullable|integer|min:1|max:10'
        ]);

        $result = $this->teamService->updateTeam($team->id, $validated, auth()->user());

        return redirect()->route('track-supervisor.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        // Check policy - track supervisors can delete teams in their track
        $this->authorize('delete', $team);

        $this->teamService->deleteTeam($team->id, auth()->user());

        return redirect()->route('track-supervisor.teams.index')
            ->with('success', 'Team deleted successfully.');
    }

    public function addMember(Request $request, Team $team)
    {
        // Check policy - track supervisors CAN add members to teams in their tracks
        $this->authorize('addMember', $team);

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

    public function removeMember(Team $team, $userId)
    {
        // Check policy - track supervisors CAN remove members from teams in their tracks
        $this->authorize('removeMember', $team);

        $result = $this->teamService->removeMember($team->id, $userId, auth()->user());

        return back()->with('success', 'Member removed successfully.');
    }

    public function export()
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
