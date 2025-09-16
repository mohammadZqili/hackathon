<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Services\TrackService;
use App\Services\EditionContext;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrackController extends Controller
{
    protected TrackService $trackService;
    protected EditionContext $editionContext;

    public function __construct(TrackService $trackService, EditionContext $editionContext)
    {
        $this->trackService = $trackService;
        $this->editionContext = $editionContext;
    }

    /**
     * Display the single track assigned to this supervisor
     */
    public function index(Request $request): Response
    {
        $edition = $this->editionContext->current();
        $user = auth()->user();

        // Get the single track assigned to this supervisor
        $assignedTrack = $user->getAssignedTrack($edition->id);

        if (!$assignedTrack) {
            // Return empty data if no track assigned
            return Inertia::render('TrackSupervisor/Tracks/Index', [
                'tracks' => ['data' => [], 'total' => 0],
                'statistics' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'with_supervisor' => 0,
                    'total_teams' => 0,
                    'total_ideas' => 0
                ],
                'editions' => [$edition],
                'filters' => $request->only(['search', 'status', 'edition_id']),
                'message' => 'You are not assigned to any track in the current edition.'
            ]);
        }

        // Load relationships and counts for the track
        $assignedTrack->loadCount(['teams', 'ideas']);
        $assignedTrack->load(['edition', 'supervisors']);

        // Calculate statistics for the single track
        $statistics = [
            'total' => 1,
            'active' => $assignedTrack->is_active ? 1 : 0,
            'inactive' => !$assignedTrack->is_active ? 1 : 0,
            'with_supervisor' => 1, // Track supervisor is viewing their own track
            'total_teams' => $assignedTrack->teams_count ?? 0,
            'total_ideas' => $assignedTrack->ideas_count ?? 0
        ];

        // Return single track as paginated result for consistency
        $data = [
            'tracks' => [
                'data' => [$assignedTrack],
                'total' => 1,
                'current_page' => 1,
                'per_page' => 1,
                'links' => [] // Empty links since there's only one track
            ],
            'statistics' => $statistics,
            'editions' => [$edition], // Only show current edition
            'filters' => $request->only(['search', 'status', 'edition_id']),
            'current_edition' => $edition,
            'assigned_track' => $assignedTrack
        ];

        return Inertia::render('TrackSupervisor/Tracks/Index', $data);
    }

    /**
     * Show the form for creating a new track
     */
    public function create(): Response
    {
        // Track supervisors cannot create tracks
        abort(403, 'Track supervisors are not authorized to create tracks.');
    }

    /**
     * Store a newly created track
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'edition_id' => 'nullable|exists:hackathon_editions,id',
            'hackathon_id' => 'required|exists:hackathons,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'max_teams' => 'nullable|integer|min:1',
            'evaluation_criteria' => 'nullable|array',
            'is_active' => 'required|boolean',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        try {
            $result = $this->trackService->createTrack($validated, auth()->user());

            return redirect()->route('system-admin.tracks.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified track
     */
    public function show(Track $track): Response
    {
        $data = $this->trackService->getTrackDetails($track->id, auth()->user());

        if (!$data) {
            abort(404, 'Track not found or access denied.');
        }

        return Inertia::render('TrackSupervisor/Tracks/Show', $data);
    }

    /**
     * Show the form for editing the specified track
     */
    public function edit(Track $track): Response
    {
        $data = $this->trackService->getFormData(auth()->user(), $track->id);

        if (!isset($data['track'])) {
            abort(404, 'Track not found or access denied.');
        }

        return Inertia::render('TrackSupervisor/Tracks/Edit', $data);
    }

    /**
     * Update the specified track
     */
    public function update(Request $request, Track $track)
    {
        $validated = $request->validate([
            'edition_id' => 'nullable|exists:hackathon_editions,id',
            'hackathon_id' => 'required|exists:hackathons,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'max_teams' => 'nullable|integer|min:1',
            'evaluation_criteria' => 'nullable|array',
            'is_active' => 'required|boolean',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        try {
            $result = $this->trackService->updateTrack($track->id, $validated, auth()->user());

            return redirect()->route('system-admin.tracks.show', $track)
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified track
     */
    public function destroy(Track $track)
    {
        try {
            $result = $this->trackService->deleteTrack($track->id, auth()->user());

            return redirect()->route('system-admin.tracks.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Export tracks to CSV
     */
    public function export(Request $request)
    {
        $filters = $request->only(['edition_id', 'status', 'search']);

        try {
            $result = $this->trackService->exportTracks(auth()->user(), $filters);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $result['filename'] . '"',
            ];

            $callback = function() use ($result) {
                $file = fopen('php://output', 'w');
                foreach ($result['data'] as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
