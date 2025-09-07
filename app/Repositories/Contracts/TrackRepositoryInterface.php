<?php

namespace App\Repositories\Contracts;

interface TrackRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get tracks by hackathon ID
     */
    public function getByHackathon(int $hackathonId, array $filters = []);

    /**
     * Get track with supervisor
     */
    public function getWithSupervisor(int $trackId);

    /**
     * Get track statistics (ideas count, etc.)
     */
    public function getStatistics(int $trackId);

    /**
     * Assign supervisor to track
     */
    public function assignSupervisor(int $trackId, int $supervisorId);

    /**
     * Remove supervisor from track
     */
    public function removeSupervisor(int $trackId, int $supervisorId);

    /**
     * Get tracks assigned to supervisor
     */
    public function getBySupervisor(int $supervisorId);

    /**
     * Get track ideas with filters
     */
    public function getIdeasByTrack(int $trackId, array $filters = []);
}
