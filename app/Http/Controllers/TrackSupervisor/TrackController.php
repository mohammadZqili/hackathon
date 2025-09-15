<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Services\TrackService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrackController extends Controller
{
    protected TrackService $trackService;

    public function __construct(TrackService $trackService)
    {
        $this->trackService = $trackService;
    }

    /**
     * Display a listing of tracks
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['edition_id', 'status', 'search']);
        $perPage = $request->get('per_page', 15);

        $data = $this->trackService->getPaginatedTracks(
            auth()->user(),
            $filters,
            $perPage
        );

        return Inertia::render('TrackSupervisor/Tracks/Index', $data);
    }

    /**
     * Show the form for creating a new track
     */
    public function create(): Response
    {
        $data = $this->trackService->getFormData(auth()->user());

        return Inertia::render('TrackSupervisor/Tracks/Create', $data);
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
