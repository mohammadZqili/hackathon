<?php

namespace App\Http\Controllers\Shared;

use App\Models\Idea;
use App\Models\Team;
use App\Models\Track;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SharedIdeaController extends BaseController
{
    public function index(Request $request)
    {
        $query = Idea::with(['team', 'team.track', 'reviews']);
        $query = $this->applyRoleScope($query, $request, 'Idea');
        
        // Apply search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Apply track filter
        if ($request->track_id) {
            $query->whereHas('team', function($q) use ($request) {
                $q->where('track_id', $request->track_id);
            });
        }
        
        // Apply status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $ideas = $query->paginate(10)->withQueryString();
        
        // Get available tracks for filters
        $tracksQuery = Track::query();
        $tracksQuery = $this->applyRoleScope($tracksQuery, $request, 'Track');
        $tracks = $tracksQuery->get();
        
        // Statistics
        $baseQuery = $this->applyRoleScope(Idea::query(), $request, 'Idea');
        $statistics = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'reviewed' => (clone $baseQuery)->where('status', 'reviewed')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];
        
        return Inertia::render("{$this->getViewPath($request)}/Ideas/Index", [
            'ideas' => $ideas,
            'tracks' => $tracks,
            'statistics' => $statistics,
            'filters' => $request->only(['search', 'track_id', 'status']),
            'currentEdition' => $this->getCurrentEdition($request),
        ]);
    }
    
    public function show(Request $request, Idea $idea)
    {
        if (!$this->canAccess($idea, $request)) {
            abort(403);
        }
        
        $idea->load(['team', 'team.track', 'team.members', 'reviews.reviewer']);
        
        return Inertia::render("{$this->getViewPath($request)}/Ideas/Show", [
            'idea' => $idea,
            'currentEdition' => $this->getCurrentEdition($request),
        ]);
    }
    
    public function review(Request $request, Idea $idea)
    {
        if (!$this->canAccess($idea, $request)) {
            abort(403);
        }
        
        $idea->load(['team', 'team.track', 'reviews']);
        
        return Inertia::render("{$this->getViewPath($request)}/Ideas/Review", [
            'idea' => $idea,
            'currentEdition' => $this->getCurrentEdition($request),
        ]);
    }
    
    public function submitReview(Request $request, Idea $idea)
    {
        if (!$this->canAccess($idea, $request)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:10',
            'feedback' => 'required|string',
            'status' => 'required|in:approved,rejected,needs_revision',
        ]);
        
        // Create review record
        $idea->reviews()->create([
            'reviewer_id' => $request->user()->id,
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'reviewed_at' => now(),
        ]);
        
        // Update idea status
        $idea->update([
            'status' => $validated['status'] === 'approved' ? 'approved' : 
                      ($validated['status'] === 'rejected' ? 'rejected' : 'needs_revision'),
            'average_score' => $idea->reviews()->avg('score'),
        ]);
        
        $routePrefix = $this->getRoutePrefix($request);
        
        return redirect()->route("{$routePrefix}.ideas.index")
            ->with('success', 'Review submitted successfully');
    }
    
    public function create(Request $request)
    {
        $teamsQuery = Team::query();
        $teamsQuery = $this->applyRoleScope($teamsQuery, $request, 'Team');
        $teams = $teamsQuery->get();
        
        return Inertia::render("{$this->getViewPath($request)}/Ideas/Create", [
            'teams' => $teams,
            'currentEdition' => $this->getCurrentEdition($request),
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'team_id' => 'required|exists:teams,id',
            'tech_stack' => 'nullable|string',
            'github_url' => 'nullable|url',
            'demo_url' => 'nullable|url',
        ]);
        
        // Ensure team is accessible
        $team = Team::findOrFail($validated['team_id']);
        if (!$this->canAccess($team, $request)) {
            abort(403, 'Unauthorized access to this team');
        }
        
        $validated['status'] = 'pending';
        $validated['submitted_at'] = now();
        
        $idea = Idea::create($validated);
        
        $routePrefix = $this->getRoutePrefix($request);
        
        return redirect()->route("{$routePrefix}.ideas.index")
            ->with('success', 'Idea submitted successfully');
    }
    
    public function update(Request $request, Idea $idea)
    {
        if (!$this->canAccess($idea, $request)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tech_stack' => 'nullable|string',
            'github_url' => 'nullable|url',
            'demo_url' => 'nullable|url',
            'status' => 'nullable|in:pending,reviewed,approved,rejected,needs_revision',
        ]);
        
        $idea->update($validated);
        
        $routePrefix = $this->getRoutePrefix($request);
        
        return redirect()->route("{$routePrefix}.ideas.show", $idea)
            ->with('success', 'Idea updated successfully');
    }
    
    public function destroy(Request $request, Idea $idea)
    {
        if (!$this->canAccess($idea, $request)) {
            abort(403);
        }
        
        $idea->delete();
        
        $routePrefix = $this->getRoutePrefix($request);
        
        return redirect()->route("{$routePrefix}.ideas.index")
            ->with('success', 'Idea deleted successfully');
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