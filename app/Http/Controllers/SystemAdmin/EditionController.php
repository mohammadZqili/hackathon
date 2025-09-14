<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EditionController extends Controller
{
    public function index()
    {
        $editions = Edition::with('admin')
            ->withCount('teams')
            ->orderBy('year', 'desc')
            ->paginate(10);

        return Inertia::render('SystemAdmin/Editions/Index', [
            'editions' => $editions
        ]);
    }

    public function create()
    {
        // Get users with Hackathon Admin role OR user_type
        $admins = User::select('id', 'name', 'email', 'user_type')
            ->where(function($query) {
                // Check by role using Spatie permissions
                $query->whereHas('roles', function($q) {
                    $q->where('name', UserType::HACKATHON_ADMIN->value);
                })
                // OR check by user_type field
                ->orWhere('user_type', UserType::HACKATHON_ADMIN->value);
            })
            ->orderBy('name')
            ->get();

        return Inertia::render('SystemAdmin/Editions/Create', [
            'admins' => $admins
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'registration_start_date' => 'required|date',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'hackathon_start_date' => 'required|date|after_or_equal:registration_end_date',
            'hackathon_end_date' => 'required|date|after:hackathon_start_date',
            'admin_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'max_teams' => 'required|integer|min:1',
            'max_team_members' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        Edition::create($validated);

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Edition created successfully.');
    }

    public function edit(Edition $edition)
    {
        // Get users with Hackathon Admin role OR user_type
        $admins = User::select('id', 'name', 'email', 'user_type')
            ->where(function($query) {
                // Check by role using Spatie permissions
                $query->whereHas('roles', function($q) {
                    $q->where('name', UserType::HACKATHON_ADMIN->value);
                })
                // OR check by user_type field
                ->orWhere('user_type', UserType::HACKATHON_ADMIN->value);
            })
            ->orderBy('name')
            ->get();

        $edition->load('admin');

        return Inertia::render('SystemAdmin/Editions/Edit', [
            'edition' => $edition,
            'admins' => $admins
        ]);
    }

    public function update(Request $request, Edition $edition)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'registration_start_date' => 'required|date',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'hackathon_start_date' => 'required|date|after_or_equal:registration_end_date',
            'hackathon_end_date' => 'required|date|after:hackathon_start_date',
            'admin_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'max_teams' => 'required|integer|min:1',
            'max_team_members' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $edition->update($validated);

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Edition updated successfully.');
    }

    public function destroy(Edition $edition)
    {
        // Check if edition has any associated data
        if ($edition->teams()->exists() || $edition->ideas()->exists() || $edition->workshops()->exists()) {
            return back()->with('error', 'Cannot delete edition with existing data.');
        }

        $edition->delete();

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Edition deleted successfully.');
    }

    public function activate(Edition $edition)
    {
        // Deactivate all other editions
        Edition::where('id', '!=', $edition->id)->update(['is_active' => false]);
        
        // Activate this edition
        $edition->update(['is_active' => true]);

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Edition activated successfully.');
    }
}
