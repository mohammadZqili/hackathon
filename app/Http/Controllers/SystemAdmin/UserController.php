<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\HackathonEditionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class UserController extends Controller
{
    protected UserService $userService;
    protected HackathonEditionService $editionService;
    
    public function __construct(UserService $userService, HackathonEditionService $editionService)
    {
        $this->userService = $userService;
        $this->editionService = $editionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->userService->getPaginatedUsers(
            auth()->user(),
            $request->only(['search', 'user_type', 'edition_id', 'status']),
            $request->get('per_page', 15)
        );
        
        // Get user types for filter dropdown
        $userTypes = [
            ['value' => 'system_admin', 'label' => 'System Admin'],
            ['value' => 'hackathon_admin', 'label' => 'Hackathon Admin'],
            ['value' => 'track_supervisor', 'label' => 'Track Supervisor'],
            ['value' => 'team_leader', 'label' => 'Team Leader'],
            ['value' => 'team_member', 'label' => 'Team Member'],
            ['value' => 'visitor', 'label' => 'Visitor'],
        ];
        
        return Inertia::render('SystemAdmin/Users/Index', array_merge($data, [
            'userTypes' => $userTypes
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $editions = $this->editionService->getAllEditions(auth()->user());
        
        $userTypes = [
            ['value' => 'system_admin', 'label' => 'System Admin'],
            ['value' => 'hackathon_admin', 'label' => 'Hackathon Admin'],
            ['value' => 'track_supervisor', 'label' => 'Track Supervisor'],
            ['value' => 'team_leader', 'label' => 'Team Leader'],
            ['value' => 'team_member', 'label' => 'Team Member'],
            ['value' => 'visitor', 'label' => 'Visitor'],
        ];
        
        // Filter user types based on current user role
        if (auth()->user()->user_type === 'hackathon_admin') {
            $userTypes = array_filter($userTypes, function($type) {
                return $type['value'] !== 'system_admin';
            });
        }
        
        return Inertia::render('SystemAdmin/Users/Create', [
            'editions' => $editions,
            'userTypes' => array_values($userTypes)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:system_admin,hackathon_admin,track_supervisor,team_leader,team_member,visitor',
            'edition_id' => 'nullable|exists:hackathon_editions,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);
        
        try {
            $result = $this->userService->createUser($validated, auth()->user());
            return redirect()->route('system-admin.users.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $data = $this->userService->getUserDetails($user->id, auth()->user());
        
        if (!$data) {
            abort(404, 'User not found or access denied.');
        }
        
        return Inertia::render('SystemAdmin/Users/Show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $data = $this->userService->getUserDetails($user->id, auth()->user());
        
        if (!$data) {
            abort(404, 'User not found or access denied.');
        }
        
        $editions = $this->editionService->getAllEditions(auth()->user());
        
        $userTypes = [
            ['value' => 'system_admin', 'label' => 'System Admin'],
            ['value' => 'hackathon_admin', 'label' => 'Hackathon Admin'],
            ['value' => 'track_supervisor', 'label' => 'Track Supervisor'],
            ['value' => 'team_leader', 'label' => 'Team Leader'],
            ['value' => 'team_member', 'label' => 'Team Member'],
            ['value' => 'visitor', 'label' => 'Visitor'],
        ];
        
        // Filter user types based on current user role
        if (auth()->user()->user_type === 'hackathon_admin') {
            $userTypes = array_filter($userTypes, function($type) {
                return $type['value'] !== 'system_admin';
            });
        }
        
        return Inertia::render('SystemAdmin/Users/Edit', array_merge($data, [
            'editions' => $editions,
            'userTypes' => array_values($userTypes)
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_type' => 'required|in:system_admin,hackathon_admin,track_supervisor,team_leader,team_member,visitor',
            'edition_id' => 'nullable|exists:hackathon_editions,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);
        
        try {
            $result = $this->userService->updateUser($user->id, $validated, auth()->user());
            return redirect()->route('system-admin.users.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $result = $this->userService->deleteUser($user->id, auth()->user());
            return redirect()->route('system-admin.users.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Search users for autocomplete
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $users = $this->userService->searchUsers(auth()->user(), $query, 10);
        
        return response()->json(['users' => $users]);
    }
    
    /**
     * Export users data
     */
    public function export(Request $request)
    {
        try {
            $result = $this->userService->exportUsers(
                auth()->user(),
                $request->only(['search', 'user_type', 'edition_id', 'status'])
            );
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $result['filename'] . '"',
            ];
            
            $callback = function() use ($result) {
                $file = fopen('php://output', 'w');
                foreach ($result['data'] as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
