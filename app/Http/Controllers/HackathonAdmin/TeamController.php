<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HackathonAdmin\CreateTeamRequest;
use App\Http\Requests\HackathonAdmin\UpdateTeamRequest;
use App\Http\Requests\HackathonAdmin\ApproveTeamRequest;
use App\Services\TeamService;
use App\Models\Team;
use App\Models\HackathonEdition;
use App\Models\Hackathon;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index(Request $request): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        if (!$currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        $teams = Team::where('hackathon_id', $currentEdition->id)
            ->with(['leader', 'track', 'members', 'idea'])
            ->when($request->get('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->get('track_id'), function ($query, $trackId) {
                $query->where('track_id', $trackId);
            })
            ->when($request->get('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15);

        // Get the current hackathon to find tracks
        $currentHackathon = Hackathon::where('is_current', true)->first();
        $tracks = $currentHackathon ? Track::where('hackathon_id', $currentHackathon->id)->get() : collect();

        return Inertia::render('HackathonAdmin/Teams/Index', [
            'teams' => $teams,
            'tracks' => $tracks,
            'filters' => $request->only(['status', 'track_id', 'search']),
            'statistics' => $this->teamService->getTeamStatistics($currentEdition->id),
        ]);
    }

    public function create(): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        if (!$currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        // Get the current hackathon to find tracks
        $currentHackathon = Hackathon::where('is_current', true)->first();
        $tracks = $currentHackathon ? Track::where('hackathon_id', $currentHackathon->id)
            ->where('is_active', true)
            ->get() : collect();
        
        $users = User::whereDoesntHave('teams', function ($query) use ($currentEdition) {
            $query->where('hackathon_id', $currentEdition->id);
        })->get();

        return Inertia::render('HackathonAdmin/Teams/Create', [
            'tracks' => $tracks,
            'users' => $users,
            'edition' => $currentEdition,
        ]);
    }

    public function store(CreateTeamRequest $request)
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        $data = $request->validated();
        $data['hackathon_id'] = $currentEdition->id;
        
        $team = $this->teamService->createTeam($data);

        return redirect()->route('hackathon-admin.teams.index')
            ->with('success', 'Team created successfully');
    }

    public function show(Team $team): Response
    {
        $team->load(['leader', 'track', 'members', 'idea', 'hackathon']);

        return Inertia::render('HackathonAdmin/Teams/Show', [
            'team' => $team,
        ]);
    }

    public function edit(Team $team): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        // Get the current hackathon to find tracks
        $currentHackathon = Hackathon::where('is_current', true)->first();
        $tracks = $currentHackathon ? Track::where('hackathon_id', $currentHackathon->id)
            ->where('is_active', true)
            ->get() : collect();

        return Inertia::render('HackathonAdmin/Teams/Edit', [
            'team' => $team,
            'tracks' => $tracks,
        ]);
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $team = $this->teamService->updateTeam($team->id, $request->validated());

        return redirect()->route('hackathon-admin.teams.show', $team)
            ->with('success', 'Team updated successfully');
    }

    public function approve(ApproveTeamRequest $request, Team $team)
    {
        $team = $this->teamService->approveTeam($team->id);

        return back()->with('success', 'Team approved successfully');
    }

    public function reject(Request $request, Team $team)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $team = $this->teamService->rejectTeam($team->id, $request->reason);

        return back()->with('success', 'Team rejected');
    }

    public function destroy(Team $team)
    {
        $this->teamService->deleteTeam($team->id);

        return redirect()->route('hackathon-admin.teams.index')
            ->with('success', 'Team deleted successfully');
    }
}
