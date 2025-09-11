<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Speaker;
use App\Models\Organization;
use App\Models\Workshop;

class SpeakerController extends Controller
{
    public function index()
    {
        $speakers = Speaker::paginate(15);

        return Inertia::render('SystemAdmin/Speakers/Index', [
            'speakers' => $speakers
        ]);
    }

    public function create()
    {
        $organizations = Organization::orderBy('name')->get();
        $workshops = Workshop::orderBy('title')->get();
        
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

        $speaker = Speaker::create($validated);

        return redirect()->route('system-admin.speakers.index')
            ->with('success', 'Speaker created successfully.');
    }

    public function show(Speaker $speaker)
    {
        return Inertia::render('SystemAdmin/Speakers/Show', [
            'speaker' => $speaker
        ]);
    }

    public function edit(Speaker $speaker)
    {
        $organizations = Organization::orderBy('name')->get();
        $workshops = Workshop::orderBy('title')->get();
        $speaker->load('organization');
        
        return Inertia::render('SystemAdmin/Speakers/Edit', [
            'speaker' => $speaker,
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

        $speaker->update($validated);

        return redirect()->route('system-admin.speakers.index')
            ->with('success', 'Speaker updated successfully.');
    }

    public function destroy(Speaker $speaker)
    {
        $speaker->delete();

        return redirect()->route('system-admin.speakers.index')
            ->with('success', 'Speaker deleted successfully.');
    }

    public function activate(Speaker $speaker)
    {
        $speaker->update(['is_active' => true]);
        return response()->json(['message' => 'Speaker activated successfully']);
    }

    public function deactivate(Speaker $speaker)
    {
        $speaker->update(['is_active' => false]);
        return response()->json(['message' => 'Speaker deactivated successfully']);
    }
}
