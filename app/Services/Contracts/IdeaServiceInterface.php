<?php
namespace App\Services\Contracts;

use App\Models\Idea;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface IdeaServiceInterface
{
    public function createIdea(Team $team, array $data): Idea;
    public function updateIdea(Idea $idea, array $data, User $user): bool;
    public function submitIdea(Idea $idea): bool;
    public function uploadFiles(Idea $idea, array $files, User $uploader): array;
    public function deleteFile(int $fileId, User $user): bool;
    public function reviewIdea(Idea $idea, string $status, array $reviewData, User $reviewer): bool;
    public function getIdeasForReview(User $supervisor): Collection;
}
