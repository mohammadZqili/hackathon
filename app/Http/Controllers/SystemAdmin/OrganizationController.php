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
        // TODO: Implement store functionality
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
        // TODO: Implement update functionality
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
        // TODO: Implement activate functionality
        return response()->json(['message' => 'Activate functionality to be implemented']);
    }

    public function deactivate(Organization $organization)
    {
        // TODO: Implement deactivate functionality
        return response()->json(['message' => 'Deactivate functionality to be implemented']);
    }
}
