<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrackController extends BaseController
{
    public function index()
    {
        $tracks = Track::where('hackathon_edition_id', $this->currentEdition->id)
            ->with(['supervisor', 'teams', 'ideas'])
            ->withCount(['teams', 'ideas'])
            ->latest()
            ->paginate(10);
        
        return Inertia::render('HackathonAdmin/Tracks/Index', [
            'tracks' => $tracks,
            'edition' => $this->currentEdition,
        ]);
    }
    
    public function create()
    {
        // Get potential track supervisors
        $supervisors = User::whereIn('role', ['track_supervisor'])
            ->orWhereHas('roles', function($q) {
                $q->where('name', 'track_supervisor');
            })
            ->get();
        
        return Inertia::render('HackathonAdmin/Tracks/Create', [
            'supervisors' => $supervisors,
            'edition' => $this->currentEdition,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'max_teams' => 'nullable|integer|min:1',
            'supervisor_id' => 'nullable|exists:users,id',
            'evaluation_criteria' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);
        
        $validated['hackathon_edition_id'] = $this->currentEdition->id;
        
        $track = Track::create($validated);
        
        // Assign supervisor role if selected
        if ($request->supervisor_id) {
            $supervisor = User::find($request->supervisor_id);
            if (!$supervisor->hasRole('track_supervisor')) {
                $supervisor->assignRole('track_supervisor');
            }
            
            // Create track-supervisor relationship
            \DB::table('track_supervisors')->insert([
                'track_id' => $track->id,
                'user_id' => $supervisor->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return redirect()->route('hackathon-admin.tracks.index')
            ->with('success', 'Track created successfully.');
    }
    
    public function show(Track $track)
    {
        if (!$this->checkEditionOwnership($track)) {
            abort(403);
        }
        
        $track->load(['supervisor', 'teams.members', 'ideas.team']);
        
        return Inertia::render('HackathonAdmin/Tracks/Show', [
            'track' => $track,
            'edition' => $this->currentEdition,
        ]);
    }
    
    public function edit(Track $track)
    {
        if (!$this->checkEditionOwnership($track)) {
            abort(403);
        }
        
        $supervisors = User::whereIn('role', ['track_supervisor'])
            ->orWhereHas('roles', function($q) {
                $q->where('name', 'track_supervisor');
            })
            ->get();
        
        return Inertia::render('HackathonAdmin/Tracks/Edit', [
            'track' => $track,
            'supervisors' => $supervisors,
            'edition' => $this->currentEdition,
        ]);
    }
    
    public function update(Request $request, Track $track)
    {
        if (!$this->checkEditionOwnership($track)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'max_teams' => 'nullable|integer|min:1',
            'supervisor_id' => 'nullable|exists:users,id',
            'evaluation_criteria' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);
        
        $track->update($validated);
        
        // Update supervisor assignment
        if ($request->supervisor_id) {
            $supervisor = User::find($request->supervisor_id);
            if (!$supervisor->hasRole('track_supervisor')) {
                $supervisor->assignRole('track_supervisor');
            }
            
            // Update track-supervisor relationship
            \DB::table('track_supervisors')
                ->where('track_id', $track->id)
                ->delete();
                
            \DB::table('track_supervisors')->insert([
                'track_id' => $track->id,
                'user_id' => $supervisor->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return redirect()->route('hackathon-admin.tracks.show', $track)
            ->with('success', 'Track updated successfully.');
    }
    
    public function destroy(Track $track)
    {
        if (!$this->checkEditionOwnership($track)) {
            abort(403);
        }
        
        // Check if track has teams or ideas
        if ($track->teams()->exists() || $track->ideas()->exists()) {
            return back()->with('error', 'Cannot delete track with associated teams or ideas.');
        }
        
        $track->delete();
        
        return redirect()->route('hackathon-admin.tracks.index')
            ->with('success', 'Track deleted successfully.');
    }
    
    /**
     * Assign supervisor to track
     */
    public function assignSupervisor(Request $request, Track $track)
    {
        if (!$this->checkEditionOwnership($track)) {
            abort(403);
        }
        
        $request->validate([
            'supervisor_id' => 'required|exists:users,id',
        ]);
        
        $supervisor = User::find($request->supervisor_id);
        
        // Assign track supervisor role if not already assigned
        if (!$supervisor->hasRole('track_supervisor')) {
            $supervisor->assignRole('track_supervisor');
        }
        
        // Create or update track-supervisor relationship
        \DB::table('track_supervisors')->updateOrInsert(
            ['track_id' => $track->id],
            [
                'user_id' => $supervisor->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        return back()->with('success', 'Supervisor assigned successfully.');
    }
}