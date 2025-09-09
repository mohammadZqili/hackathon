<?php

namespace App\Repositories\Contracts;

use App\Models\HackathonEdition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface HackathonEditionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithCreator(int $perPage = 15): LengthAwarePaginator;
    
    public function getWithRelations(int $id): ?HackathonEdition;
    
    public function getForEdit(int $id): ?HackathonEdition;
    
    public function getCurrentEdition(): ?HackathonEdition;
    
    public function setCurrent(int $id): bool;
    
    public function archive(int $id): bool;
    
    public function getByStatus(string $status): Collection;
    
    public function getByYear(int $year): Collection;
    
    public function slugExists(string $slug, ?int $excludeId = null): bool;
    
    public function generateUniqueSlug(string $name, int $year, ?int $excludeId = null): string;
    
    public function getActiveEditions(): Collection;
    
    public function updateStatus(int $id, string $status): bool;
    
    public function getWithWorkshopCount(int $perPage = 15): LengthAwarePaginator;
    
    public function getForSelect(): Collection;
    
    public function isRegistrationOpen(int $id): bool;
    
    public function isIdeaSubmissionOpen(int $id): bool;
}