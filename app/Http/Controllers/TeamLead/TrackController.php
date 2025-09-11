<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\TrackService;
use Inertia\Inertia;

class TrackController extends Controller
{
    protected $trackService;

    public function __construct(TrackService $trackService)
    {
        $this->trackService = $trackService;
    }

    public function index()
    {
        $tracks = $this->trackService->getActiveTracksWithTeams();
        
        return Inertia::render('TeamLead/Tracks/Index', [
            'tracks' => $tracks
        ]);
    }

    public function show($id)
    {
        $track = $this->trackService->getTrackDetails($id);
        
        return Inertia::render('TeamLead/Tracks/Show', [
            'track' => $track
        ]);
    }
}
