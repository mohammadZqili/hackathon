<?php

namespace App\Http\Controllers\HackathonAdmin;
use App\Http\Requests\HackathonAdmin\CreateTeamRequest;
use App\Http\Requests\HackathonAdmin\UpdateTeamRequest;
use App\Http\Requests\HackathonAdmin\ApproveTeamRequest;
use App\Services\TeamService;
use App\Models\Team;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends BaseController
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        parent::__construct();
        $this->teamService = $teamService;
    }

    public function index(Request $request): Response
    {
        if (!$this->currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        $query = Team::where('edition_id', $this->currentEdition->id)
            ->with(['leader', 'track', 'members', 'idea']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('track_id')) {
            $query->where('track_id', $request->get('track_id'));
        }
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $teams = $query->latest()->paginate(15);

        // Get tracks for current edition
        $tracks = Track::where('hackathon_edition_id', $this->currentEdition->id)->get();

        return Inertia::render('HackathonAdmin/Teams/Index', [
            'teams' => $teams,
            'tracks' => $tracks,
            'filters' => $request->only(['status', 'track_id', 'search']),
            'statistics' => $this->teamService->getTeamStatistics($this->currentEdition->id),
        ]);
    }

    public function create(): Response
    {
        if (!$this->currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        // Get tracks for current edition
        $tracks = Track::where('hackathon_edition_id', $this->currentEdition->id)
            ->where('status', 'active')
            ->get();

        $users = User::whereDoesntHave('teams', function ($query) {
            $query->where('edition_id', $this->currentEdition->id);
        })->get();

        return Inertia::render('HackathonAdmin/Teams/Create', [
            'tracks' => $tracks,
            'users' => $users,
            'edition' => $this->currentEdition,
        ]);
    }

    public function store(CreateTeamRequest $request)
    {
        $data = $request->validated();
        $data['edition_id'] = $this->currentEdition->id;

        try {
            $team = $this->teamService->createTeam($data);
            return redirect()->route('hackathon-admin.teams.index')
                ->with('success', 'Team created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Team $team): Response
    {
        $team->load(['leader', 'track', 'members', 'idea', 'edition']);

        return Inertia::render('HackathonAdmin/Teams/Show', [
            'team' => $team,
        ]);
    }

    public function edit(Team $team): Response
    {
        // Ensure team belongs to current edition
        if (!$this->checkEditionOwnership($team)) {
            abort(403);
        }

        // Get tracks for current edition
        $tracks = Track::where('hackathon_edition_id', $this->currentEdition->id)
            ->where('status', 'active')
            ->get();

        return Inertia::render('HackathonAdmin/Teams/Edit', [
            'team' => $team,
            'tracks' => $tracks,
        ]);
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        if (!$this->checkEditionOwnership($team)) {
            abort(403);
        }

        try {
            $team = $this->teamService->updateTeam($team->id, $request->validated());
            return redirect()->route('hackathon-admin.teams.show', $team)
                ->with('success', 'Team updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function approve(ApproveTeamRequest $request, Team $team)
    {
        if (!$this->checkEditionOwnership($team)) {
            abort(403);
        }

        try {
            $team = $this->teamService->approveTeam($team->id);
            return back()->with('success', 'Team approved successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, Team $team)
    {
        if (!$this->checkEditionOwnership($team)) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $team = $this->teamService->rejectTeam($team->id, $request->reason);
            return back()->with('success', 'Team rejected');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Team $team)
    {
        if (!$this->checkEditionOwnership($team)) {
            abort(403);
        }

        try {
            $this->teamService->deleteTeam($team->id);
            return redirect()->route('hackathon-admin.teams.index')
                ->with('success', 'Team deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
