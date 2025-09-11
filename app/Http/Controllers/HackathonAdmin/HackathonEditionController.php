<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Controller;
use App\Models\HackathonEdition;
use App\Services\Contracts\HackathonEditionServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class HackathonEditionController extends Controller
{
    public function __construct(
        protected HackathonEditionServiceInterface $editionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $editions = $this->editionService->getPaginatedEditions(15);

        return Inertia::render('HackathonAdmin/Editions/Index', [
            'editions' => $editions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('HackathonAdmin/Editions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
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

        try {
            $this->editionService->createEdition($validated);

            return redirect()->route('hackathon-admin.editions.index')
                ->with('success', 'Hackathon edition created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create hackathon edition: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HackathonEdition $edition): Response
    {
        $editionWithRelations = $this->editionService->getEditionForView($edition->id);
        $statistics = $this->editionService->getEditionStatistics($edition->id);

        return Inertia::render('HackathonAdmin/Editions/Show', [
            'edition' => $editionWithRelations,
            'statistics' => $statistics
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HackathonEdition $edition): Response
    {
        $data = $this->editionService->getEditionForEdit($edition->id);

        return Inertia::render('HackathonAdmin/Editions/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HackathonEdition $edition): RedirectResponse
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

        try {
            $this->editionService->updateEdition($edition->id, $validated);

            return redirect()->route('hackathon-admin.editions.index')
                ->with('success', 'Hackathon edition updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update hackathon edition: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HackathonEdition $edition): RedirectResponse
    {
        try {
            $this->editionService->deleteEdition($edition->id);

            return redirect()->route('hackathon-admin.editions.index')
                ->with('success', 'Hackathon edition deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete hackathon edition: ' . $e->getMessage());
        }
    }

    /**
     * Set an edition as current.
     */
    public function setCurrent(HackathonEdition $edition): RedirectResponse
    {
        try {
            $this->editionService->setCurrentEdition($edition->id);

            return redirect()->route('hackathon-admin.editions.index')
                ->with('success', 'Edition set as current successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to set current edition: ' . $e->getMessage());
        }
    }

    /**
     * Archive an edition.
     */
    public function archive(HackathonEdition $edition): RedirectResponse
    {
        try {
            $this->editionService->archiveEdition($edition->id);

            return redirect()->route('hackathon-admin.editions.index')
                ->with('success', 'Edition archived successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to archive edition: ' . $e->getMessage());
        }
    }

    /**
     * Export edition data.
     */
    public function export(HackathonEdition $edition)
    {
        try {
            $data = $this->editionService->exportEdition($edition->id);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to export edition: ' . $e->getMessage()
            ], 500);
        }
    }
}
