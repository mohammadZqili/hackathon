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
        // Track supervisors cannot create teams
        abort(403, 'Track supervisors are not authorized to create teams.');
    }

    public function store(Request $request)
    {
        // Track supervisors cannot create teams
        abort(403, 'Track supervisors are not authorized to create teams.');
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
        // Track supervisors cannot edit teams
        abort(403, 'Track supervisors are not authorized to edit teams.');
    }

    public function update(Request $request, Team $team)
    {
        // Track supervisors cannot update teams
        abort(403, 'Track supervisors are not authorized to update teams.');
    }

    public function destroy(Team $team)
    {
        // Track supervisors cannot delete teams
        abort(403, 'Track supervisors are not authorized to delete teams.');
    }

    public function addMember(Request $request, Team $team)
    {
        // Track supervisors cannot add members
        abort(403, 'Track supervisors are not authorized to manage team members.');
    }

    public function removeMember(Team $team, $user)
    {
        // Track supervisors cannot remove members
        abort(403, 'Track supervisors are not authorized to manage team members.');
    }

    public function export()
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
