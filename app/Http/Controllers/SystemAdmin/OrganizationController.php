<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\OrganizationService;
use App\Services\SpeakerService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Organization;

class OrganizationController extends Controller
{
    protected OrganizationService $organizationService;
    protected SpeakerService $speakerService;

    public function __construct(
        OrganizationService $organizationService,
        SpeakerService $speakerService
    ) {
        $this->organizationService = $organizationService;
        $this->speakerService = $speakerService;
    }
    public function index()
    {
        $organizations = $this->organizationService->getPaginatedOrganizations(15);

        return Inertia::render('SystemAdmin/Organizations/Index', [
            'organizations' => $organizations
        ]);
    }

    public function create()
    {
        $speakers = $this->speakerService->getAllSpeakers();

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

        $organization = $this->organizationService->createOrganization($validated);

        return redirect()->route('system-admin.organizations.index')
            ->with('success', 'Organization created successfully.');
    }

    public function show(Organization $organization)
    {
        $organizationData = $this->organizationService->getOrganizationDetails($organization->id);

        return Inertia::render('SystemAdmin/Organizations/Show', [
            'organization' => $organizationData
        ]);
    }

    public function edit(Organization $organization)
    {
        $speakers = $this->speakerService->getAllSpeakers();
        $organizationData = $this->organizationService->getOrganizationWithSpeakers($organization->id);

        return Inertia::render('SystemAdmin/Organizations/Edit', [
            'organization' => $organizationData,
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

        $this->organizationService->updateOrganization($organization->id, $validated);

        return redirect()->route('system-admin.organizations.index')
            ->with('success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization)
    {
        $this->organizationService->deleteOrganization($organization->id);

        return redirect()->route('system-admin.organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }

    public function activate(Organization $organization)
    {
        $this->organizationService->activateOrganization($organization->id);
        return response()->json(['message' => 'Organization activated successfully']);
    }

    public function deactivate(Organization $organization)
    {
        $this->organizationService->deactivateOrganization($organization->id);
        return response()->json(['message' => 'Organization deactivated successfully']);
    }
}
