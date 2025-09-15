<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\SpeakerService;
use App\Services\OrganizationService;
use App\Services\WorkshopService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Speaker;

class SpeakerController extends Controller
{
    protected SpeakerService $speakerService;
    protected OrganizationService $organizationService;
    protected WorkshopService $workshopService;

    public function __construct(
        SpeakerService $speakerService,
        OrganizationService $organizationService,
        WorkshopService $workshopService
    ) {
        $this->speakerService = $speakerService;
        $this->organizationService = $organizationService;
        $this->workshopService = $workshopService;
    }
    public function index()
    {
        $speakers = $this->speakerService->getPaginatedSpeakers(15);

        return Inertia::render('SystemAdmin/Speakers/Index', [
            'speakers' => $speakers
        ]);
    }

    public function create()
    {
        $organizations = $this->organizationService->getAllOrganizations();
        $workshops = $this->workshopService->getAllWorkshops();

        return Inertia::render('SystemAdmin/Speakers/Create', [
            'organizations' => $organizations,
            'workshops' => $workshops
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'organization_id' => 'nullable|exists:organizations,id',
            'expertise_areas' => 'nullable|array',
            'social_media' => 'nullable|array',
            'is_active' => 'boolean',
            'photo_path' => 'nullable|string',
        ]);

        $speaker = $this->speakerService->createSpeaker($validated);

        return redirect()->route('system-admin.speakers.index')
            ->with('success', 'Speaker created successfully.');
    }

    public function show(Speaker $speaker)
    {
        $speakerData = $this->speakerService->getSpeakerDetails($speaker->id);

        return Inertia::render('SystemAdmin/Speakers/Show', [
            'speaker' => $speakerData
        ]);
    }

    public function edit(Speaker $speaker)
    {
        $organizations = $this->organizationService->getAllOrganizations();
        $workshops = $this->workshopService->getAllWorkshops();
        $speakerData = $this->speakerService->getSpeakerWithOrganization($speaker->id);

        return Inertia::render('SystemAdmin/Speakers/Edit', [
            'speaker' => $speakerData,
            'organizations' => $organizations,
            'workshops' => $workshops
        ]);
    }

    public function update(Request $request, Speaker $speaker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'organization_id' => 'nullable|exists:organizations,id',
            'expertise_areas' => 'nullable|array',
            'social_media' => 'nullable|array',
            'is_active' => 'boolean',
            'photo_path' => 'nullable|string',
        ]);

        $this->speakerService->updateSpeaker($speaker->id, $validated);

        return redirect()->route('system-admin.speakers.index')
            ->with('success', 'Speaker updated successfully.');
    }

    public function destroy(Speaker $speaker)
    {
        $this->speakerService->deleteSpeaker($speaker->id);

        return redirect()->route('system-admin.speakers.index')
            ->with('success', 'Speaker deleted successfully.');
    }

    public function activate(Speaker $speaker)
    {
        $this->speakerService->activateSpeaker($speaker->id);
        return response()->json(['message' => 'Speaker activated successfully']);
    }

    public function deactivate(Speaker $speaker)
    {
        $this->speakerService->deactivateSpeaker($speaker->id);
        return response()->json(['message' => 'Speaker deactivated successfully']);
    }
}
