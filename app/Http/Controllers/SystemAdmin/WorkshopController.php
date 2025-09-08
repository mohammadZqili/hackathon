<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Workshop;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = Workshop::with(['hackathon', 'speakers', 'organizations', 'registrations'])
            ->latest()
            ->paginate(15);

        return Inertia::render('SystemAdmin/Workshops/Index', [
            'workshops' => $workshops
        ]);
    }

    public function create()
    {
        return Inertia::render('SystemAdmin/Workshops/Create');
    }

    public function store(Request $request)
    {
        // TODO: Implement store functionality
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
        return Inertia::render('SystemAdmin/Workshops/Edit', [
            'workshop' => $workshop
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
