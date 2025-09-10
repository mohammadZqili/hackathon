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
        // TODO: Implement update functionality
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
