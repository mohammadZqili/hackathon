<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Workshop;
use App\Models\Speaker;
use App\Models\Organization;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = Workshop::with(['speakers', 'organizations'])
            ->latest()
            ->paginate(15);

        $speakers = Speaker::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        return Inertia::render('SystemAdmin/Workshops/Index', [
            'workshops' => $workshops,
            'speakers' => $speakers,
            'organizations' => $organizations
        ]);
    }

    public function create()
    {
        $speakers = Speaker::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        \Log::info('Workshop Create Data', [
            'speakers_count' => $speakers->count(),
            'organizations_count' => $organizations->count()
        ]);

        return Inertia::render('SystemAdmin/Workshops/Create', [
            'speakers' => $speakers,
            'organizations' => $organizations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'format' => 'nullable|in:online,offline,hybrid',
            'location' => 'nullable|string|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'prerequisites' => 'nullable|string',
            'materials' => 'nullable|array',
            'is_active' => 'boolean',
            'requires_registration' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_time',
            'speaker_ids' => 'nullable|array',
            'speaker_ids.*' => 'exists:speakers,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id',
        ]);

        // Remove relation fields from validated data
        $workshopData = collect($validated)->except(['speaker_ids', 'organization_ids'])->toArray();
        
        // Create the workshop
        $workshop = Workshop::create($workshopData);

        // Attach speakers if provided
        if (!empty($validated['speaker_ids'])) {
            $speakerData = [];
            foreach ($validated['speaker_ids'] as $index => $speakerId) {
                $speakerData[$speakerId] = ['role' => 'main_speaker', 'order' => $index + 1];
            }
            $workshop->speakers()->attach($speakerData);
        }

        // Attach organizations if provided
        if (!empty($validated['organization_ids'])) {
            $orgData = [];
            foreach ($validated['organization_ids'] as $orgId) {
                $orgData[$orgId] = ['role' => 'organizer'];
            }
            $workshop->organizations()->attach($orgData);
        }

        return redirect()->route('system-admin.workshops.index')
            ->with('success', 'Workshop created successfully.');
    }

    public function show(Workshop $workshop)
    {
        $workshop->load(['hackathon', 'speakers', 'organizations', 'registrations']);

        return Inertia::render('SystemAdmin/Workshops/Show', [
            'workshop' => $workshop
        ]);
    }

    public function edit(Workshop $workshop)
    {
        $speakers = Speaker::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $workshop->load(['speakers', 'organizations']);

        return Inertia::render('SystemAdmin/Workshops/Edit', [
            'workshop' => $workshop,
            'speakers' => $speakers,
            'organizations' => $organizations
        ]);
    }

    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'format' => 'nullable|in:online,offline,hybrid',
            'location' => 'nullable|string|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'prerequisites' => 'nullable|string',
            'materials' => 'nullable|array',
            'is_active' => 'boolean',
            'requires_registration' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_time',
            'speaker_ids' => 'nullable|array',
            'speaker_ids.*' => 'exists:speakers,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id',
        ]);

        // Remove relation fields from validated data
        $workshopData = collect($validated)->except(['speaker_ids', 'organization_ids'])->toArray();
        
        // Update the workshop
        $workshop->update($workshopData);

        // Sync speakers if provided
        if (isset($validated['speaker_ids'])) {
            $speakerData = [];
            foreach ($validated['speaker_ids'] as $index => $speakerId) {
                $speakerData[$speakerId] = ['role' => 'main_speaker', 'order' => $index + 1];
            }
            $workshop->speakers()->sync($speakerData);
        }

        // Sync organizations if provided
        if (isset($validated['organization_ids'])) {
            $orgData = [];
            foreach ($validated['organization_ids'] as $orgId) {
                $orgData[$orgId] = ['role' => 'organizer'];
            }
            $workshop->organizations()->sync($orgData);
        }

        return redirect()->route('system-admin.workshops.index')
            ->with('success', 'Workshop updated successfully.');
    }

    public function destroy(Workshop $workshop)
    {
        $workshop->delete();

        return redirect()->route('system-admin.workshops.index')
            ->with('success', 'Workshop deleted successfully.');
    }

    public function attendance(Workshop $workshop)
    {
        $workshop->load('attendances.user');

        return Inertia::render('SystemAdmin/Workshops/Attendance', [
            'workshop' => $workshop
        ]);
    }

    public function generateQR(Workshop $workshop)
    {
        // TODO: Implement QR generation
        return response()->json(['message' => 'QR generation to be implemented']);
    }

    public function export()
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
