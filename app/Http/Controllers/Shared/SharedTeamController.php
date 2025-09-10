<?php

namespace App\Http\Controllers\Shared;

use App\Models\Team;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SharedTeamController extends BaseController
{
    public function index(Request $request)
    {
        $query = Team::with(['track', 'leader', 'members']);
        $query = $this->applyRoleScope($query, $request, 'Team');
        
        // Apply search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Apply track filter
        if ($request->track_id) {
            $query->where('track_id', $request->track_id);
        }
        
        // Apply status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $teams = $query->paginate(10)->withQueryString();
        
        // Get available tracks for filters
        $tracksQuery = Track::query();
        $tracksQuery = $this->applyRoleScope($tracksQuery, $request, 'Track');
        $tracks = $tracksQuery->get();
        
        // Statistics
        $statistics = [
            'total' => $this->applyRoleScope(Team::query(), $request, 'Team')->count(),
            'active' => $this->applyRoleScope(Team::query(), $request, 'Team')->where('status', 'active')->count(),
            'pending' => $this->applyRoleScope(Team::query(), $request, 'Team')->where('status', 'pending')->count(),
            'inactive' => $this->applyRoleScope(Team::query(), $request, 'Team')->where('status', 'inactive')->count(),
        ];
        
        $routePrefix = $this->getRoutePrefix($request);
        
        return Inertia::render("{$this->getViewPath($request)}/Teams/Index", [
            'teams' => $teams,
            'tracks' => $tracks,
            'statistics' => $statistics,
            'filters' => $request->only(['search', 'track_id', 'status']),
            'currentEdition' => $this->getCurrentEdition($request),
        ]);
    }
    
    public function create(Request $request)
    {
        $tracksQuery = Track::query();
        $tracksQuery = $this->applyRoleScope($tracksQuery, $request, 'Track');
        $tracks = $tracksQuery->get();
        
        $usersQuery = User::query();
        $usersQuery = $this->applyRoleScope($usersQuery, $request, 'User');
        $users = $usersQuery->get();
        
        return Inertia::render("{$this->getViewPath($request)}/Teams/Create", [
            'tracks' => $tracks,
            'users' => $users,
            'currentEdition' => $this->getCurrentEdition($request),
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'track_id' => 'required|exists:tracks,id',
            'leader_id' => 'required|exists:users,id',
            'member_ids' => 'array',
            'member_ids.*' => 'exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,pending,inactive',
        ]);
        
        // Ensure track is accessible
        $track = Track::findOrFail($validated['track_id']);
        if (!$this->canAccess($track, $request)) {
            abort(403, 'Unauthorized access to this track');
        }
        
        $validated['edition_id'] = $track->edition_id;
        
        $team = Team::create($validated);
        
        // Add members
        if (!empty($validated['member_ids'])) {
            $team->members()->sync($validated['member_ids']);
        }
        
        $routePrefix = $this->getRoutePrefix($request);
        
        return redirect()->route("{$routePrefix}.teams.index")
            ->with('success', 'Team created successfully');
    }
    
    public function edit(Request $request, Team $team)
    {
        if (!$this->canAccess($team, $request)) {
            abort(403);
        }
        
        $team->load(['track', 'leader', 'members']);
        
        $tracksQuery = Track::query();
        $tracksQuery = $this->applyRoleScope($tracksQuery, $request, 'Track');
        $tracks = $tracksQuery->get();
        
        $usersQuery = User::query();
        $usersQuery = $this->applyRoleScope($usersQuery, $request, 'User');
        $users = $usersQuery->get();
        
        return Inertia::render("{$this->getViewPath($request)}/Teams/Edit", [
            'team' => $team,
            'tracks' => $tracks,
            'users' => $users,
            'currentEdition' => $this->getCurrentEdition($request),
        ]);
    }
    
    public function update(Request $request, Team $team)
    {
        if (!$this->canAccess($team, $request)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'track_id' => 'required|exists:tracks,id',
            'leader_id' => 'required|exists:users,id',
            'member_ids' => 'array',
            'member_ids.*' => 'exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,pending,inactive',
        ]);
        
        // Ensure new track is accessible
        if ($validated['track_id'] != $team->track_id) {
            $track = Track::findOrFail($validated['track_id']);
            if (!$this->canAccess($track, $request)) {
                abort(403, 'Unauthorized access to this track');
            }
        }
        
        $team->update($validated);
        
        // Update members
        if (isset($validated['member_ids'])) {
            $team->members()->sync($validated['member_ids']);
        }
        
        $routePrefix = $this->getRoutePrefix($request);
        
        return redirect()->route("{$routePrefix}.teams.index")
            ->with('success', 'Team updated successfully');
    }
    
    public function destroy(Request $request, Team $team)
    {
        if (!$this->canAccess($team, $request)) {
            abort(403);
        }
        
        $team->delete();
        
        $routePrefix = $this->getRoutePrefix($request);
        
        return redirect()->route("{$routePrefix}.teams.index")
            ->with('success', 'Team deleted successfully');
    }
    
    protected function getViewPath(Request $request): string
    {
        $user = $request->user();
        
        if ($user->hasRole('system-admin')) {
            return 'SystemAdmin';
        }
        
        if ($user->hasRole('hackathon-admin')) {
            return 'HackathonAdmin';
        }
        
        if ($user->hasRole('track-supervisor')) {
            return 'TrackSupervisor';
        }
        
        return 'Visitor';
    }
}