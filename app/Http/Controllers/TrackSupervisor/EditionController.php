<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Services\HackathonEditionService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EditionController extends Controller
{
    protected HackathonEditionService $editionService;
    protected UserService $userService;

    public function __construct(
        HackathonEditionService $editionService,
        UserService $userService
    ) {
        $this->editionService = $editionService;
        $this->userService = $userService;
    }
    public function index()
    {
        $editions = $this->editionService->getPaginatedEditions(10);

        return Inertia::render('TrackSupervisor/Editions/Index', [
            'editions' => $editions
        ]);
    }

    public function create()
    {
        $admins = $this->userService->getUsersByType('hackathon_admin');

        return Inertia::render('TrackSupervisor/Editions/Create', [
            'admins' => $admins
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'registration_start_date' => 'required|date',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'hackathon_start_date' => 'required|date|after_or_equal:registration_end_date',
            'hackathon_end_date' => 'required|date|after:hackathon_start_date',
            'admin_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'max_teams' => 'required|integer|min:1',
            'max_team_members' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $this->editionService->createEdition($validated);

        return redirect()->route('track-supervisor.editions.index')
            ->with('success', 'Edition created successfully.');
    }

    public function show(Edition $edition)
    {
        $data = $this->editionService->getEditionWithStatistics($edition->id);

        return Inertia::render('TrackSupervisor/Editions/Show', $data);
    }

    public function edit(Edition $edition)
    {
        $admins = $this->userService->getUsersByType('hackathon_admin');
        $editionData = $this->editionService->getEditionDetails($edition->id);

        return Inertia::render('TrackSupervisor/Editions/Edit', [
            'edition' => $editionData,
            'admins' => $admins
        ]);
    }

    public function update(Request $request, Edition $edition)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'registration_start_date' => 'required|date',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'hackathon_start_date' => 'required|date|after_or_equal:registration_end_date',
            'hackathon_end_date' => 'required|date|after:hackathon_start_date',
            'admin_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'max_teams' => 'required|integer|min:1',
            'max_team_members' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $this->editionService->updateEdition($edition->id, $validated);

        return redirect()->route('track-supervisor.editions.index')
            ->with('success', 'Edition updated successfully.');
    }

    public function destroy(Edition $edition)
    {
        try {
            $this->editionService->deleteEdition($edition->id);
            return redirect()->route('track-supervisor.editions.index')
                ->with('success', 'Edition deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function activate(Edition $edition)
    {
        $this->editionService->activateEdition($edition->id);

        return redirect()->route('track-supervisor.editions.index')
            ->with('success', 'Edition activated successfully.');
    }

    public function export()
    {
        $csv = $this->editionService->exportToCsv();

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="editions-' . now()->format('Y-m-d') . '.csv"');
    }
}
