<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Speaker;

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
        return Inertia::render('SystemAdmin/Speakers/Create');
    }

    public function store(Request $request)
    {
        // TODO: Implement store functionality
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
        return Inertia::render('SystemAdmin/Speakers/Edit', [
            'speaker' => $speaker
        ]);
    }

    public function update(Request $request, Speaker $speaker)
    {
        // TODO: Implement update functionality
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
        // TODO: Implement activate functionality
        return response()->json(['message' => 'Activate functionality to be implemented']);
    }

    public function deactivate(Speaker $speaker)
    {
        // TODO: Implement deactivate functionality
        return response()->json(['message' => 'Deactivate functionality to be implemented']);
    }
}
