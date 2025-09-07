<?php

namespace App\Services\Contracts;

use App\Models\Hackathon;
use Illuminate\Support\Collection;

interface HackathonServiceInterface
{
    /**
     * Get the current active hackathon.
     */
    public function getCurrentHackathon(): ?Hackathon;

    /**
     * Create a new hackathon.
     */
    public function createHackathon(array $data): Hackathon;

    /**
     * Update hackathon details.
     */
    public function updateHackathon(int $id, array $data): bool;

    /**
     * Activate a hackathon and deactivate others.
     */
    public function activateHackathon(int $id): bool;

    /**
     * Get comprehensive statistics for a hackathon.
     */
    public function getHackathonStatistics(int $id): array;

    /**
     * Check if registration is currently open.
     */
    public function isRegistrationOpen(int $id): bool;

    /**
     * Check if idea submission is currently open.
     */
    public function isIdeaSubmissionOpen(int $id): bool;

    /**
     * Get all hackathons with pagination.
     */
    public function getAllHackathons(int $perPage = 15): mixed;

    /**
     * Archive a completed hackathon.
     */
    public function archiveHackathon(int $id): bool;

    /**
     * Get registration timeline data.
     */
    public function getRegistrationTimeline(int $id): array;
}
