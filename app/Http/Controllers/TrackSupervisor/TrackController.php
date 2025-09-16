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
     * Display the tracks assigned to this supervisor
     */
    public function index(Request $request): Response
    {
        $edition = $this->editionContext->current();
        $user = auth()->user();

        // Get all tracks assigned to this supervisor in the current edition
        $assignedTracks = $user->tracksInEdition($edition->id)->get();

        if ($assignedTracks->isEmpty()) {
            // Return empty data if no tracks assigned
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
                'message' => 'You are not assigned to any tracks in the current edition.'
            ]);
        }

        // Apply filters
        $query = $assignedTracks;

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query = $query->filter(function ($track) use ($search) {
                return stripos($track->name, $search) !== false ||
                       stripos($track->description, $search) !== false;
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query = $query->filter(function ($track) use ($isActive) {
                return $track->is_active === $isActive;
            });
        }

        // Load relationships and counts for all tracks
        $query->each(function($track) {
            $track->loadCount(['teams', 'ideas']);
            $track->load(['edition', 'supervisors']);
        });

        // Calculate statistics for all assigned tracks
        $statistics = [
            'total' => $assignedTracks->count(),
            'active' => $assignedTracks->where('is_active', true)->count(),
            'inactive' => $assignedTracks->where('is_active', false)->count(),
            'with_supervisor' => $assignedTracks->count(), // All have supervisor (current user)
            'total_teams' => $assignedTracks->sum('teams_count'),
            'total_ideas' => $assignedTracks->sum('ideas_count')
        ];

        // Return tracks as paginated result for consistency
        $data = [
            'tracks' => [
                'data' => $query->values()->all(),
                'total' => $query->count(),
                'current_page' => 1,
                'per_page' => $query->count(),
                'links' => [] // No pagination needed for limited tracks
            ],
            'statistics' => $statistics,
            'editions' => [$edition], // Only show current edition
            'filters' => $request->only(['search', 'status', 'edition_id']),
            'current_edition' => $edition,
            'assigned_tracks' => $assignedTracks
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
        // Track supervisors shouldn't be able to create tracks normally
        abort(403, 'Track supervisors are not authorized to create tracks.');
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
            'hackathon_id' => 'required|exists:hackathons,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'max_teams' => 'nullable|integer|min:1',
            'evaluation_criteria' => 'nullable|array',
            'is_active' => 'required|boolean',
        ]);

        // Track supervisors cannot change edition_id or supervisor_id
        // Keep the existing values
        $validated['edition_id'] = $track->edition_id;

        try {
            $result = $this->trackService->updateTrack($track->id, $validated, auth()->user());

            return redirect()->route('track-supervisor.tracks.show', $track)
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

            return redirect()->route('track-supervisor.tracks.index')
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
