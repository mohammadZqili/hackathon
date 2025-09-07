<?php

namespace App\Repositories\Contracts;

use App\Models\Hackathon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface HackathonRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get current active hackathon
     */
    public function getCurrentHackathon(): ?Hackathon;

    /**
     * Get all active hackathons
     */
    public function getActiveHackathons(): Collection;

    /**
     * Get hackathons by year
     */
    public function getHackathonsByYear(int $year): Collection;

    /**
     * Set hackathon as current
     */
    public function setAsCurrent(int $hackathonId): bool;

    /**
     * Get hackathon with stats
     */
    public function getWithStats(int $hackathonId): ?Hackathon;

    /**
     * Get paginated hackathons with filters
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Check if registration is open
     */
    public function isRegistrationOpen(int $hackathonId): bool;

    /**
     * Check if idea submission is open
     */
    public function isIdeaSubmissionOpen(int $hackathonId): bool;

    /**
     * Get upcoming hackathons
     */
    public function getUpcomingHackathons(): Collection;

    /**
     * Get past hackathons
     */
    public function getPastHackathons(): Collection;
}
