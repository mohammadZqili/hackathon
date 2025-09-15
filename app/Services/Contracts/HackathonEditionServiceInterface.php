<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface HackathonEditionServiceInterface
{
    public function getPaginatedEditions(int $perPage = 15): LengthAwarePaginator;

    public function getEditionForView(int $id): ?object;

    public function getEditionForEdit(int $id): array;

    public function getDataForCreate(): array;

    public function createEdition(array $data): ?object;
    
    public function updateEdition(int $id, array $data): bool;
    
    public function deleteEdition(int $id): bool;
    
    public function setCurrentEdition(int $id): bool;
    
    public function archiveEdition(int $id): bool;
    
    public function getCurrentEdition(): ?object;
    
    public function getEditionsByStatus(string $status): Collection;
    
    public function getEditionsByYear(int $year): Collection;
    
    public function getActiveEditions(): Collection;
    
    public function getEditionsForSelect(): Collection;
    
    public function isRegistrationOpen(int $id): bool;
    
    public function isIdeaSubmissionOpen(int $id): bool;
    
    public function exportEdition(int $id): array;
    
    public function getEditionStatistics(int $id): array;
    
    public function validateDates(array $data): array;
}