<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Organization;
use App\Models\Speaker;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::paginate(15);

        return Inertia::render('SystemAdmin/Organizations/Index', [
            'organizations' => $organizations
        ]);
    }

    public function create()
    {
        $speakers = Speaker::orderBy('name')->get();
        
        return Inertia::render('SystemAdmin/Organizations/Create', [
            'speakers' => $speakers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'type' => 'nullable|in:government,private,ngo,educational,other',
            'is_active' => 'boolean',
            'social_media' => 'nullable|array',
            'logo_path' => 'nullable|string',
        ]);

        $organization = Organization::create($validated);

        return redirect()->route('system-admin.organizations.index')
            ->with('success', 'Organization created successfully.');
    }

    public function show(Organization $organization)
    {
        return Inertia::render('SystemAdmin/Organizations/Show', [
            'organization' => $organization
        ]);
    }

    public function edit(Organization $organization)
    {
        $speakers = Speaker::orderBy('name')->get();
        $organization->load('speakers');
        
        return Inertia::render('SystemAdmin/Organizations/Edit', [
            'organization' => $organization,
            'speakers' => $speakers
        ]);
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'type' => 'nullable|in:government,private,ngo,educational,other',
            'is_active' => 'boolean',
            'social_media' => 'nullable|array',
            'logo_path' => 'nullable|string',
        ]);

        $organization->update($validated);

        return redirect()->route('system-admin.organizations.index')
            ->with('success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('system-admin.organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }

    public function activate(Organization $organization)
    {
        $organization->update(['is_active' => true]);
        return response()->json(['message' => 'Organization activated successfully']);
    }

    public function deactivate(Organization $organization)
    {
        $organization->update(['is_active' => false]);
        return response()->json(['message' => 'Organization deactivated successfully']);
    }
}
