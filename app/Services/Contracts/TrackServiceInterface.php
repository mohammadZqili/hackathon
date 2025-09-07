<?php

namespace App\Services\Contracts;

use App\Models\Track;
use Illuminate\Support\Collection;

interface TrackServiceInterface
{
    /**
     * Get tracks for a hackathon with statistics.
     */
    public function getHackathonTracks(int $hackathonId): Collection;

    /**
     * Get track details with comprehensive statistics.
     */
    public function getTrackDetails(int $trackId): array;

    /**
     * Get track statistics.
     */
    public function getTrackStatistics(int $trackId): array;

    /**
     * Check if user can register for track.
     */
    public function canUserRegisterForTrack(Track $track, $user): bool;

    /**
     * Get track with ideas and teams.
     */
    public function getTrackWithRelations(int $trackId): Track;
}