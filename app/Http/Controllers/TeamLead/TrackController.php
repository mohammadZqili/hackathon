<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\TrackService;
use App\Services\EditionContext;
use Inertia\Inertia;

class TrackController extends Controller
{
    protected $trackService;
    protected $editionContext;

    public function __construct(TrackService $trackService, EditionContext $editionContext)
    {
        $this->trackService = $trackService;
        $this->editionContext = $editionContext;
    }

    public function index()
    {
        // Get current edition
        $currentEdition = $this->editionContext->current();

        // Get tracks for current edition only
        $tracks = $this->trackService->getActiveTracksWithTeams();

        return Inertia::render('TeamLead/Tracks/Index', [
            'tracks' => $tracks,
            'currentEdition' => $currentEdition
        ]);
    }

    public function show($id)
    {
        $track = $this->trackService->getTrackDetails($id, auth()->user());

        if (!$track) {
            return redirect()->route('team-lead.tracks.index')
                ->with('error', 'Track not found or not accessible.');
        }

        return Inertia::render('TeamLead/Tracks/Show', [
            'track' => $track['track']
        ]);
    }
}
