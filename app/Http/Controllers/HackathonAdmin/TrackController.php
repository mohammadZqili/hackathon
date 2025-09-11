<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\User;
use App\Models\HackathonEdition;
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

    public function index(Request $request): Response
    {
        $query = Track::with(['teams', 'ideas', 'edition', 'hackathon'])
            ->withCount(['teams', 'ideas']);

        // Apply filters
        if ($request->filled('edition_id')) {
            $query->where('edition_id', $request->get('edition_id'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tracks = $query->latest()->paginate(15)->withQueryString();

        // Get all editions for filter dropdown
        $editions = HackathonEdition::orderBy('created_at', 'desc')->get();

        // Get statistics
        $statistics = [
            'total' => Track::count(),
            'active' => Track::where('is_active', true)->count(),
            'inactive' => Track::where('is_active', false)->count(),
            'with_teams' => Track::has('teams')->count(),
            'total_teams' => Track::withCount('teams')->get()->sum('teams_count'),
            'total_ideas' => Track::withCount('ideas')->get()->sum('ideas_count'),
        ];

        return Inertia::render('HackathonAdmin/Tracks/Index', [
            'tracks' => $tracks,
            'editions' => $editions,
            'statistics' => $statistics,
            'filters' => $request->only(['edition_id', 'status', 'search']),
        ]);
    }

    public function create(): Response
    {
        // Get all editions
        $editions = HackathonEdition::orderBy('created_at', 'desc')->get();

        // Get potential track supervisors
        $supervisors = User::whereIn('role', ['track_supervisor'])
            ->orWhereHas('roles', function($q) {
                $q->where('name', 'track_supervisor');
            })
            ->get();

        return Inertia::render('HackathonAdmin/Tracks/Create', [
            'editions' => $editions,
            'supervisors' => $supervisors,
        ]);
    }

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
        ]);

        try {
            $track = $this->trackService->createTrack($validated);

            // Track created successfully

            return redirect()->route('hackathon-admin.tracks.index')
                ->with('success', 'Track created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(Track $track): Response
    {
        $track->load(['teams.members', 'ideas.team', 'edition', 'hackathon']);

        return Inertia::render('HackathonAdmin/Tracks/Show', [
            'track' => $track,
        ]);
    }

    public function edit(Track $track): Response
    {
        // Get all editions
        $editions = HackathonEdition::orderBy('created_at', 'desc')->get();

        return Inertia::render('HackathonAdmin/Tracks/Edit', [
            'track' => $track,
            'editions' => $editions,
        ]);
    }

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
        ]);

        try {
            $track = $this->trackService->updateTrack($track->id, $validated);

            // Track updated successfully

            return redirect()->route('hackathon-admin.tracks.show', $track)
                ->with('success', 'Track updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Track $track)
    {
        // Check if track has teams or ideas
        if ($track->teams()->exists() || $track->ideas()->exists()) {
            return back()->with('error', 'Cannot delete track with associated teams or ideas.');
        }

        try {
            $this->trackService->deleteTrack($track->id);
            return redirect()->route('hackathon-admin.tracks.index')
                ->with('success', 'Track deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    /**
     * Export tracks to CSV
     */
    public function export(Request $request)
    {
        $query = Track::with(['edition', 'hackathon'])
            ->withCount(['teams', 'ideas']);

        if ($request->filled('edition_id')) {
            $query->where('edition_id', $request->get('edition_id'));
        }

        $tracks = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tracks-export-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($tracks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Edition', 'Description', 'Teams Count', 'Ideas Count', 'Status', 'Created At']);

            foreach ($tracks as $track) {
                fputcsv($file, [
                    $track->id,
                    $track->name,
                    $track->edition->name ?? 'N/A',
                    $track->description,
                    $track->teams_count,
                    $track->ideas_count,
                    $track->is_active ? 'Active' : 'Inactive',
                    $track->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
