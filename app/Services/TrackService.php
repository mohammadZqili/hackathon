<?php

namespace App\Services;

use App\Repositories\TrackRepository;

class TrackService extends BaseService
{
    protected $trackRepo;

    public function __construct(TrackRepository $trackRepo)
    {
        $this->trackRepo = $trackRepo;
    }

    public function getAllTracks()
    {
        return $this->trackRepo->getAll();
    }

    public function getActiveTracksWithTeams()
    {
        return $this->trackRepo->getActiveWithTeams();
    }

    public function getTrackDetails($trackId)
    {
        return $this->trackRepo->findWithDetails($trackId);
    }

    public function getTrackSupervisors($trackId)
    {
        return $this->trackRepo->getSupervisors($trackId);
    }
}
