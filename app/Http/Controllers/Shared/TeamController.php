<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function __construct(
        private TeamService $teamService
    ) {}
    
    /**
     * Display teams list based on user role (using user_type field)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $teams = $this->teamService->getTeamsForUser($request, $user);
        $permissions = $this->teamService->getTeamPermissions(null, $user);
        
        // For team members/leaders who have a team, redirect to show page
        if (in_array($user->user_type, ['team_leader', 'team_member']) && $user->team_id) {
            return redirect()->route('teams.show', $user->team_id);
        }
        
        return Inertia::render('Shared/Teams/Index', [
            'teams' => $teams,
            'permissions' => $permissions,
            'userRole' => $user->user_type,  // Pass user_type as userRole for consistency
            'filters' => $request->all(),
            'editions' => $user->user_type === 'system_admin' ? $this->teamService->getAvailableEditions($user) : []
        ]);
    }
    
    /**
     * Show team creation form
     */
    public function create()
    {
        $user = auth()->user();
        $permissions = $this->teamService->getTeamPermissions(null, $user);
        
        if (!$permissions['canCreate']) {
            abort(403, 'You cannot create a team');
        }
        
        return Inertia::render('Shared/Teams/Create', [
            'editions' => $this->teamService->getAvailableEditions($user),
            'userRole' => $user->user_type,
            'permissions' => $permissions
        ]);
    }
    
    /**
     * Store new team
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Validation rules based on role
        $rules = [
            'name' => 'required|string|max:255|unique:teams,name',
            'description' => 'nullable|string|max:1000',
        ];
        
        if ($user->user_type === 'system_admin') {
            $rules['edition_id'] = 'required|exists:editions,id';
            $rules['leader_id'] = 'nullable|exists:users,id';
            $rules['track_id'] = 'nullable|exists:tracks,id';
            $rules['max_members'] = 'nullable|integer|min:1|max:10';
        }
        
        $validated = $request->validate($rules);
        
        try {
            $team = $this->teamService->createTeam($validated, $user);
            
            return redirect()->route('teams.show', $team->id)
                ->with('success', 'Team created successfully');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Display team details
     */
    public function show($id)
    {
        $user = auth()->user();
        
        try {
            $team = $this->teamService->getTeamForUser($id, $user);
            
            return Inertia::render('Shared/Teams/Show', [
                'team' => $team,
                'permissions' => $team->permissions,
                'userRole' => $user->user_type
            ]);
            
        } catch (\Exception $e) {
            abort(404, 'Team not found or you do not have access');
        }
    }
    
    /**
     * Show team edit form
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        try {
            $team = $this->teamService->getTeamForUser($id, $user);
            
            if (!$team->permissions['canEdit']) {
                abort(403, 'You cannot edit this team');
            }
            
            return Inertia::render('Shared/Teams/Edit', [
                'team' => $team,
                'editions' => $this->teamService->getAvailableEditions($user),
                'permissions' => $team->permissions,
                'userRole' => $user->user_type
            ]);
            
        } catch (\Exception $e) {
            abort(404, 'Team not found');
        }
    }
    
    /**
     * Update team
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $team = Team::findOrFail($id);
        
        // Validation based on role
        $rules = [
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'description' => 'nullable|string|max:1000',
        ];
        
        if ($user->user_type === 'system_admin') {
            $rules['edition_id'] = 'required|exists:editions,id';
            $rules['leader_id'] = 'nullable|exists:users,id';
            $rules['status'] = 'required|in:active,inactive,disqualified';
        }
        
        $validated = $request->validate($rules);
        
        try {
            $team = $this->teamService->updateTeam($team, $validated, $user);
            
            return redirect()->route('teams.show', $team->id)
                ->with('success', 'Team updated successfully');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Delete team (System Admin only)
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        if ($user->user_type !== 'system_admin') {
            abort(403);
        }
        
        $team = Team::findOrFail($id);
        
        // Check if team has submitted idea
        if ($team->idea) {
            return back()->withErrors(['error' => 'Cannot delete team with submitted idea']);
        }
        
        $team->delete();
        
        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully');
    }
    
    /**
     * Add member to team
     */
    public function addMember(Request $request, Team $team)
    {
        $user = auth()->user();
        
        // Check permission based on user_type
        if (!in_array($user->user_type, ['system_admin', 'hackathon_admin']) && 
            ($user->user_type !== 'team_leader' || $team->leader_id !== $user->id)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,co-leader'
        ]);
        
        // Check if user is already in a team
        $newMember = User::find($validated['user_id']);
        if ($newMember->team_id) {
            return back()->withErrors(['user_id' => 'User is already in a team']);
        }
        
        // Check team capacity
        if ($team->members->count() >= $team->max_members) {
            return back()->withErrors(['user_id' => 'Team is at maximum capacity']);
        }
        
        // Add member
        $team->members()->attach($validated['user_id'], [
            'role' => $validated['role'],
            'joined_at' => now()
        ]);
        
        // Update user's team_id
        $newMember->update(['team_id' => $team->id]);
        
        return back()->with('success', 'Member added successfully');
    }
    
    /**
     * Remove member from team
     */
    public function removeMember(Team $team, User $member)
    {
        $user = auth()->user();
        
        // Check permission based on user_type
        if (!in_array($user->user_type, ['system_admin', 'hackathon_admin']) && 
            ($user->user_type !== 'team_leader' || $team->leader_id !== $user->id)) {
            abort(403);
        }
        
        // Cannot remove team leader
        if ($member->id === $team->leader_id) {
            return back()->withErrors(['error' => 'Cannot remove team leader']);
        }
        
        // Remove member
        $team->members()->detach($member->id);
        
        // Update user's team_id
        $member->update(['team_id' => null]);
        
        return back()->with('success', 'Member removed successfully');
    }
}
