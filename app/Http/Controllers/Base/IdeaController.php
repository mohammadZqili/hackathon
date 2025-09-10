<?php

namespace App\Http\Controllers\Base;

use App\Models\Idea;
use App\Models\Team;
use App\Models\Track;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class IdeaController extends Controller
{
    protected function applyRoleScope($query, Request $request, $model = 'Idea')
    {
        $user = $request->user();
        
        if ($user->hasRole('system-admin')) {
            return $query;
        }
        
        if ($user->hasRole('hackathon-admin')) {
            $editionId = $user->edition_id ?? session('current_edition_id');
            
            if ($model === 'Idea') {
                return $query->whereHas('team', function($q) use ($editionId) {
                    $q->where('edition_id', $editionId);
                });
            }
            
            if ($model === 'Team') {
                return $query->where('edition_id', $editionId);
            }
            
            if ($model === 'Track') {
                return $query->where('edition_id', $editionId);
            }
        }
        
        if ($user->hasRole('track-supervisor')) {
            $trackIds = $user->supervisedTracks->pluck('id')->toArray();
            
            if ($model === 'Idea') {
                return $query->whereHas('team', function($q) use ($trackIds) {
                    $q->whereIn('track_id', $trackIds);
                });
            }
            
            if ($model === 'Team') {
                return $query->whereIn('track_id', $trackIds);
            }
            
            if ($model === 'Track') {
                return $query->whereIn('id', $trackIds);
            }
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
        $query = Idea::with(['team', 'team.track', 'reviews']);
        $query = $this->applyRoleScope($query, $request);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->track_id) {
            $query->whereHas('team', function($q) use ($request) {
                $q->where('track_id', $request->track_id);
            });
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $ideas = $query->latest()->paginate(10)->withQueryString();
        
        $tracks = $this->applyRoleScope(Track::query(), $request, 'Track')->get();
        
        $baseQuery = $this->applyRoleScope(Idea::query(), $request);
        $statistics = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'reviewed' => (clone $baseQuery)->where('status', 'reviewed')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];
        
        return Inertia::render("Shared/Ideas/Index", [
            'ideas' => $ideas,
            'tracks' => $tracks,
            'statistics' => $statistics,
            'filters' => $request->only(['search', 'track_id', 'status']),
            'viewPrefix' => $this->getViewPrefix($request),
        ]);
    }
    
    public function show(Idea $idea, Request $request)
    {
        $idea->load(['team', 'team.track', 'team.members', 'reviews.reviewer']);
        
        return Inertia::render("Shared/Ideas/Show", [
            'idea' => $idea,
            'viewPrefix' => $this->getViewPrefix($request),
        ]);
    }
    
    public function review(Idea $idea, Request $request)
    {
        $idea->load(['team', 'team.track', 'reviews']);
        
        return Inertia::render("Shared/Ideas/Review", [
            'idea' => $idea,
            'viewPrefix' => $this->getViewPrefix($request),
        ]);
    }
    
    public function submitReview(Request $request, Idea $idea)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:10',
            'feedback' => 'required|string',
            'status' => 'required|in:approved,rejected,needs_revision',
        ]);
        
        $idea->reviews()->create([
            'reviewer_id' => $request->user()->id,
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'reviewed_at' => now(),
        ]);
        
        $idea->update([
            'status' => $validated['status'] === 'approved' ? 'approved' : 
                      ($validated['status'] === 'rejected' ? 'rejected' : 'needs_revision'),
            'average_score' => $idea->reviews()->avg('score'),
        ]);
        
        return redirect()->back()->with('success', 'Review submitted successfully');
    }
    
    public function create(Request $request)
    {
        $teams = $this->applyRoleScope(Team::query(), $request, 'Team')->get();
        
        return Inertia::render("Shared/Ideas/Create", [
            'teams' => $teams,
            'viewPrefix' => $this->getViewPrefix($request),
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
        
        $validated['status'] = 'pending';
        $validated['submitted_at'] = now();
        
        Idea::create($validated);
        
        return redirect()->back()->with('success', 'Idea submitted successfully');
    }
    
    public function update(Request $request, Idea $idea)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tech_stack' => 'nullable|string',
            'github_url' => 'nullable|url',
            'demo_url' => 'nullable|url',
            'status' => 'nullable|in:pending,reviewed,approved,rejected,needs_revision',
        ]);
        
        $idea->update($validated);
        
        return redirect()->back()->with('success', 'Idea updated successfully');
    }
    
    public function destroy(Idea $idea)
    {
        $idea->delete();
        return redirect()->back()->with('success', 'Idea deleted successfully');
    }
}