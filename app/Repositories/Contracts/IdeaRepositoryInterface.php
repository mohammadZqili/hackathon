<?php

namespace App\Repositories\Contracts;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface IdeaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get ideas by track
     */
    public function getIdeasByTrack(int $trackId): Collection;

    /**
     * Get ideas by status
     */
    public function getIdeasByStatus(string $status): Collection;

    /**
     * Get ideas for supervisor review
     */
    public function getIdeasForSupervisorReview(int $supervisorId): Collection;

    /**
     * Get paginated ideas with filters
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Submit idea for review
     */
    public function submitForReview(int $ideaId): bool;

    /**
     * Evaluate idea
     */
    public function evaluateIdea(int $ideaId, string $status, ?string $feedback = null, ?float $score = null, ?int $reviewerId = null): bool;

    /**
     * Get idea with all relationships
     */
    public function getIdeaWithRelations(int $ideaId): ?Idea;

    /**
     * Get ideas needing review
     */
    public function getIdeasNeedingReview(): Collection;

    /**
     * Get accepted ideas
     */
    public function getAcceptedIdeas(): Collection;

    /**
     * Get rejected ideas
     */
    public function getRejectedIdeas(): Collection;

    /**
     * Add file to idea
     */
    public function addFile(int $ideaId, array $fileData): bool;

    /**
     * Remove file from idea
     */
    public function removeFile(int $ideaId, int $fileId): bool;

    /**
     * Get idea statistics
     */
    public function getStatistics(): array;
}
