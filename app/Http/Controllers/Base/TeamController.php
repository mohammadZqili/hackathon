<?php

namespace App\Http\Controllers\Base;

use App\Models\Team;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    protected function applyRoleScope($query, Request $request)
    {
        $user = $request->user();
        
        if ($user->hasRole('system-admin')) {
            return $query;
        }
        
        if ($user->hasRole('hackathon-admin')) {
            $editionId = $user->edition_id ?? session('current_edition_id');
            return $query->where('edition_id', $editionId);
        }
        
        if ($user->hasRole('track-supervisor')) {
            $trackIds = $user->supervisedTracks->pluck('id')->toArray();
            return $query->whereIn('track_id', $trackIds);
        }
        
        return $query;
    }
    
    protected function getViewPrefix(Request $request): string
    {
        $user = $request->user();
        
        if ($user->hasRole('system-admin')) return 'SystemAdmin';
        if ($user->hasRole('hackathon-admin')) return 'HackathonAdmin';
        if ($user->hasRole('track-supervisor')) return 'TrackSupervisor';
        
        return 'Visitor';
    }
    
    public function index(Request $request)
    {
        $query = Team::with(['track', 'leader', 'members']);
        $query = $this->applyRoleScope($query, $request);
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->track_id) {
            $query->where('track_id', $request->track_id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $teams = $query->paginate(10)->withQueryString();
        
        $tracks = $this->applyRoleScope(Track::query(), $request)->get();
        
        $statistics = [
            'total' => $this->applyRoleScope(Team::query(), $request)->count(),
            'active' => $this->applyRoleScope(Team::query(), $request)->where('status', 'active')->count(),
            'pending' => $this->applyRoleScope(Team::query(), $request)->where('status', 'pending')->count(),
            'inactive' => $this->applyRoleScope(Team::query(), $request)->where('status', 'inactive')->count(),
        ];
        
        return Inertia::render("Shared/Teams/Index", [
            'teams' => $teams,
            'tracks' => $tracks,
            'statistics' => $statistics,
            'filters' => $request->only(['search', 'track_id', 'status']),
            'viewPrefix' => $this->getViewPrefix($request),
        ]);
    }
    
    public function create(Request $request)
    {
        $tracks = $this->applyRoleScope(Track::query(), $request)->get();
        $users = User::all(); // You might want to filter this too
        
        return Inertia::render("Shared/Teams/Create", [
            'tracks' => $tracks,
            'users' => $users,
            'viewPrefix' => $this->getViewPrefix($request),
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
        
        $track = Track::findOrFail($validated['track_id']);
        $validated['edition_id'] = $track->edition_id;
        
        $team = Team::create($validated);
        
        if (!empty($validated['member_ids'])) {
            $team->members()->sync($validated['member_ids']);
        }
        
        return redirect()->back()->with('success', 'Team created successfully');
    }
    
    public function edit(Request $request, Team $team)
    {
        $team->load(['track', 'leader', 'members']);
        $tracks = $this->applyRoleScope(Track::query(), $request)->get();
        $users = User::all();
        
        return Inertia::render("Shared/Teams/Edit", [
            'team' => $team,
            'tracks' => $tracks,
            'users' => $users,
            'viewPrefix' => $this->getViewPrefix($request),
        ]);
    }
    
    public function update(Request $request, Team $team)
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
        
        $team->update($validated);
        
        if (isset($validated['member_ids'])) {
            $team->members()->sync($validated['member_ids']);
        }
        
        return redirect()->back()->with('success', 'Team updated successfully');
    }
    
    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->back()->with('success', 'Team deleted successfully');
    }
}