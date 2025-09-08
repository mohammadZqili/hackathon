<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\HackathonEdition;

class HackathonEditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $editions = HackathonEdition::with(['creator'])
            ->orderBy('year', 'desc')
            ->paginate(15);

        return Inertia::render('SystemAdmin/Editions/Index', [
            'editions' => $editions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('SystemAdmin/Editions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2020|max:2030',
            'slug' => 'nullable|string|unique:hackathon_editions,slug',
            'description' => 'nullable|string',
            'theme' => 'nullable|string|max:255',
            'registration_start_date' => 'required|date',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'idea_submission_start_date' => 'required|date',
            'idea_submission_end_date' => 'required|date|after:idea_submission_start_date',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after:event_start_date',
            'location' => 'nullable|string|max:255',
            'is_current' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';
        
        if (!$validated['slug']) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name'] . '-' . $validated['year']);
        }

        $edition = HackathonEdition::create($validated);

        if ($validated['is_current'] ?? false) {
            HackathonEdition::where('id', '!=', $edition->id)
                ->update(['is_current' => false]);
        }

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Hackathon edition created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HackathonEdition $edition)
    {
        $edition->load(['creator', 'teams', 'workshops', 'news']);

        return Inertia::render('SystemAdmin/Editions/Show', [
            'edition' => $edition
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HackathonEdition $edition)
    {
        return Inertia::render('SystemAdmin/Editions/Edit', [
            'edition' => $edition
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HackathonEdition $edition)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2020|max:2030',
            'slug' => 'nullable|string|unique:hackathon_editions,slug,' . $edition->id,
            'description' => 'nullable|string',
            'theme' => 'nullable|string|max:255',
            'registration_start_date' => 'required|date',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'idea_submission_start_date' => 'required|date',
            'idea_submission_end_date' => 'required|date|after:idea_submission_start_date',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after:event_start_date',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:draft,active,completed,archived',
            'is_current' => 'boolean',
        ]);

        if (!$validated['slug']) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name'] . '-' . $validated['year']);
        }

        $edition->update($validated);

        if ($validated['is_current'] ?? false) {
            HackathonEdition::where('id', '!=', $edition->id)
                ->update(['is_current' => false]);
        }

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Hackathon edition updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HackathonEdition $edition)
    {
        $edition->delete();

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Hackathon edition deleted successfully.');
    }

    /**
     * Set an edition as current.
     */
    public function setCurrent(HackathonEdition $edition)
    {
        HackathonEdition::where('id', '!=', $edition->id)
            ->update(['is_current' => false]);

        $edition->update(['is_current' => true]);

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Edition set as current successfully.');
    }

    /**
     * Archive an edition.
     */
    public function archive(HackathonEdition $edition)
    {
        $edition->update([
            'status' => 'archived',
            'is_current' => false,
        ]);

        return redirect()->route('system-admin.editions.index')
            ->with('success', 'Edition archived successfully.');
    }

    /**
     * Export edition data.
     */
    public function export(HackathonEdition $edition)
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
