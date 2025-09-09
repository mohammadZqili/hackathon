<?php

namespace App\Services;

use App\Models\Idea;
use App\Models\Team;
use App\Models\User;
use App\Models\Track;
use App\Models\IdeaFile;
use App\Models\IdeaAuditLog;
use App\Repositories\IdeaRepository;
use App\Repositories\TrackRepository;
use App\Repositories\HackathonRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\IdeaServiceInterface;
use App\Enums\IdeaStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class IdeaService implements IdeaServiceInterface
{
    public function __construct(
        private IdeaRepository $ideaRepo,
        private TrackRepository $trackRepo,
        private HackathonRepository $hackathonRepo,
        private UserRepository $userRepo
    ) {}

    // =====================================================
    // Team/Member Operations (Existing Functionality)
    // =====================================================

    /**
     * Create a new idea for a team.
     */
    public function createIdea(Team $team, array $data): Idea
    {
        return DB::transaction(function () use ($team, $data) {
            // Check if team can create idea
            if (!$team->hackathon->isIdeaSubmissionOpen()) {
                throw new \Exception('مهلة تقديم الأفكار منتهية');
            }

            // Check if team already has an idea
            if ($team->idea) {
                throw new \Exception('الفريق لديه فكرة بالفعل');
            }

            // Create idea
            $data['team_id'] = $team->id;
            $data['hackathon_id'] = $team->hackathon_id;
            $data['track_id'] = $team->track_id;
            $data['status'] = 'draft';
            $data['created_by'] = auth()->id();

            $idea = $this->ideaRepo->create($data);

            // Log creation
            $this->createAuditLog($idea, 'created', 'تم إنشاء الفكرة', auth()->user());

            Log::info('Idea created', [
                'idea_id' => $idea->id,
                'team_id' => $team->id,
                'created_by' => auth()->id(),
            ]);

            return $idea;
        });
    }

    /**
     * Update an existing idea.
     */
    public function updateIdea(Idea $idea, array $data, User $user): bool
    {
        // Check permissions
        if (!$this->canUserEditIdea($idea, $user)) {
            throw new \Exception('غير مصرح لك بتعديل هذه الفكرة');
        }

        // Check if idea can be edited
        if (!in_array($idea->status, ['draft', 'needs_revision'])) {
            throw new \Exception('لا يمكن تعديل الفكرة في هذه الحالة');
        }

        // Update idea
        $result = $this->ideaRepo->update($idea->id, $data);

        if ($result) {
            // Log update
            $this->createAuditLog($idea, 'updated', 'تم تعديل الفكرة', $user);

            Log::info('Idea updated', [
                'idea_id' => $idea->id,
                'updated_by' => $user->id,
            ]);
        }

        return $result;
    }

    /**
     * Submit an idea for review.
     */
    public function submitIdea(Idea $idea): bool
    {
        // Check if idea can be submitted
        if ($idea->status !== 'draft' && $idea->status !== 'needs_revision') {
            throw new \Exception('لا يمكن تقديم الفكرة في هذه الحالة');
        }

        // Validate required fields
        $this->validateIdeaForSubmission($idea);

        // Check submission deadline
        if (!$idea->hackathon->isIdeaSubmissionOpen()) {
            throw new \Exception('مهلة تقديم الأفكار منتهية');
        }

        return DB::transaction(function () use ($idea) {
            // Update status
            $result = $this->ideaRepo->update($idea->id, [
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            if ($result) {
                // Log submission
                $this->createAuditLog($idea, 'submitted', 'تم تقديم الفكرة للمراجعة', auth()->user());

                // Update team status
                $idea->team->update(['status' => 'submitted']);

                Log::info('Idea submitted', [
                    'idea_id' => $idea->id,
                    'team_id' => $idea->team_id,
                ]);
            }

            return $result;
        });
    }

    /**
     * Upload files for an idea.
     */
    public function uploadFiles(Idea $idea, array $files, User $uploader): array
    {
        // Check permissions
        if (!$this->canUserEditIdea($idea, $uploader)) {
            throw new \Exception('غير مصرح لك برفع ملفات لهذه الفكرة');
        }

        // Check file limits
        $currentFileCount = $idea->files()->count();
        $maxFiles = config('hackathon.max_files_per_idea', 8);

        if ($currentFileCount + count($files) > $maxFiles) {
            throw new \Exception("يمكن رفع {$maxFiles} ملفات كحد أقصى");
        }

        $uploadedFiles = [];
        $errors = [];

        foreach ($files as $file) {
            try {
                $uploadedFile = $this->uploadSingleFile($idea, $file, $uploader);
                $uploadedFiles[] = $uploadedFile;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Log file uploads
        if (!empty($uploadedFiles)) {
            $this->createAuditLog(
                $idea,
                'files_uploaded',
                'تم رفع ' . count($uploadedFiles) . ' ملف(ات)',
                $uploader
            );
        }

        return [
            'uploaded' => $uploadedFiles,
            'errors' => $errors,
            'success_count' => count($uploadedFiles),
            'error_count' => count($errors),
        ];
    }

    /**
     * Delete a file from an idea.
     */
    public function deleteFile(int $fileId, User $user): bool
    {
        $ideaFile = IdeaFile::findOrFail($fileId);
        $idea = $ideaFile->idea;

        // Check permissions
        if (!$this->canUserEditIdea($idea, $user)) {
            throw new \Exception('غير مصرح لك بحذف هذا الملف');
        }

        return DB::transaction(function () use ($ideaFile, $idea, $user) {
            // Delete physical file
            if (Storage::exists($ideaFile->file_path)) {
                Storage::delete($ideaFile->file_path);
            }

            // Delete record
            $result = $ideaFile->delete();

            if ($result) {
                // Log deletion
                $this->createAuditLog(
                    $idea,
                    'file_deleted',
                    "تم حذف الملف: {$ideaFile->original_name}",
                    $user
                );

                Log::info('Idea file deleted', [
                    'file_id' => $ideaFile->id,
                    'idea_id' => $idea->id,
                    'deleted_by' => $user->id,
                ]);
            }

            return $result;
        });
    }

    /**
     * Review an idea (supervisor action).
     */
    public function reviewIdea(Idea $idea, string $status, array $reviewData, User $reviewer): bool
    {
        // Check permissions
        if (!$this->canUserReviewIdea($idea, $reviewer)) {
            throw new \Exception('غير مصرح لك بمراجعة هذه الفكرة');
        }

        // Validate status
        $validStatuses = ['under_review', 'needs_revision', 'accepted', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            throw new \Exception('حالة غير صحيحة');
        }

        return DB::transaction(function () use ($idea, $status, $reviewData, $reviewer) {
            // Update idea
            $updateData = [
                'status' => $status,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_feedback' => $reviewData['feedback'] ?? null,
                'review_score' => $reviewData['score'] ?? null,
            ];

            $result = $this->ideaRepo->update($idea->id, $updateData);

            if ($result) {
                // Log review
                $statusText = $this->getStatusText($status);
                $this->createAuditLog(
                    $idea,
                    'reviewed',
                    "تم تقييم الفكرة: {$statusText}",
                    $reviewer
                );

                // Update team status if needed
                if ($status === 'accepted') {
                    $idea->team->update(['status' => 'accepted']);
                } elseif ($status === 'rejected') {
                    $idea->team->update(['status' => 'rejected']);
                }

                Log::info('Idea reviewed', [
                    'idea_id' => $idea->id,
                    'status' => $status,
                    'reviewed_by' => $reviewer->id,
                ]);
            }

            return $result;
        });
    }

    /**
     * Get ideas for review by supervisor.
     */
    public function getIdeasForReview(User $supervisor): Collection
    {
        // Get supervisor's assigned tracks
        $trackIds = $supervisor->supervisedTracks()->pluck('id');

        return $this->ideaRepo->findByQuery([
            'status' => ['submitted', 'under_review'],
            'track_id' => $trackIds->toArray(),
        ]);
    }

    /**
     * Get idea statistics.
     */
    public function getIdeaStatistics(int $ideaId): array
    {
        $idea = $this->ideaRepo->findOrFail($ideaId);

        return [
            'status' => $idea->status,
            'files' => [
                'count' => $idea->files()->count(),
                'total_size' => $idea->files()->sum('file_size'),
                'types' => $idea->files()->selectRaw('file_type, COUNT(*) as count')
                    ->groupBy('file_type')->get()->pluck('count', 'file_type'),
            ],
            'timeline' => [
                'created_at' => $idea->created_at,
                'submitted_at' => $idea->submitted_at,
                'reviewed_at' => $idea->reviewed_at,
            ],
            'team' => [
                'name' => $idea->team->name,
                'members_count' => $idea->team->acceptedMembers()->count(),
            ],
            'track' => $idea->track->name,
            'audit_count' => $idea->auditLogs()->count(),
        ];
    }

    // =====================================================
    // SystemAdmin Operations (New Functionality)
    // =====================================================

    /**
     * Get paginated ideas with filters for system admin.
     */
    public function getPaginatedIdeas(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->ideaRepo->getModel()->with(['team.leader', 'team.hackathon', 'track', 'reviewer', 'files']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('team', function ($teamQuery) use ($search) {
                      $teamQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('team.leader', function ($leaderQuery) use ($search) {
                      $leaderQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply track filter
        if (!empty($filters['track'])) {
            $query->where('track_id', $filters['track']);
        }

        // Apply edition filter
        if (!empty($filters['edition'])) {
            $query->whereHas('team.hackathon', function ($q) use ($filters) {
                $q->where('id', $filters['edition']);
            });
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for ideas list.
     */
    public function getFilterOptions(): array
    {
        return [
            'tracks' => $this->trackRepo->getModel()
                ->select('id', 'name', 'hackathon_id')
                ->with('hackathon:id,name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'editions' => $this->hackathonRepo->getModel()
                ->select('id', 'name', 'year')
                ->orderBy('year', 'desc')
                ->get()
        ];
    }

    /**
     * Get general ideas statistics.
     */
    public function getGeneralIdeaStatistics(): array
    {
        return [
            'total' => $this->ideaRepo->count(),
            'pending' => $this->ideaRepo->getModel()->whereIn('status', ['submitted', 'under_review'])->count(),
            'accepted' => $this->ideaRepo->getModel()->where('status', 'accepted')->count(),
            'rejected' => $this->ideaRepo->getModel()->where('status', 'rejected')->count(),
            'needs_revision' => $this->ideaRepo->getModel()->where('status', 'needs_revision')->count(),
        ];
    }

    /**
     * Get idea with all relations for admin view.
     */
    public function getIdeaWithRelations(int $ideaId): ?Idea
    {
        return $this->ideaRepo->getModel()
            ->with([
                'team.leader', 
                'team.hackathon', 
                'team.members',
                'track', 
                'reviewer', 
                'files', 
                'auditLogs.user'
            ])
            ->find($ideaId);
    }

    /**
     * Get available supervisors for track.
     */
    public function getAvailableSupervisors(?int $trackId = null): Collection
    {
        if (!$trackId) {
            return collect();
        }

        return $this->userRepo->getModel()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'track_supervisor');
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get user permissions for idea management.
     */
    public function getUserPermissions(): array
    {
        $user = Auth::user();
        
        return [
            'review_ideas' => $user->can('review_ideas') || $user->hasRole('system_admin'),
            'score_ideas' => $user->can('score_ideas') || $user->hasRole('system_admin'),
            'assign_supervisors' => $user->can('assign_supervisors') || $user->hasRole('system_admin'),
        ];
    }

    /**
     * Accept an idea (SystemAdmin).
     */
    public function acceptIdea(Idea $idea, array $data): bool
    {
        return DB::transaction(function () use ($idea, $data) {
            $previousStatus = $idea->status;

            $updated = $this->ideaRepo->update($idea->id, [
                'status' => IdeaStatus::ACCEPTED->value,
                'feedback' => $data['feedback'] ?? null,
                'score' => $data['score'] ?? null,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);

            if ($updated) {
                $this->createAuditLog($idea, 'accepted', $data['feedback'] ?: 'Idea accepted without feedback', Auth::user(), [
                    'score' => $data['score'] ?? null,
                    'previous_status' => $previousStatus,
                    'reviewer_name' => Auth::user()->name
                ]);
            }

            return $updated;
        });
    }

    /**
     * Reject an idea (SystemAdmin).
     */
    public function rejectIdea(Idea $idea, array $data): bool
    {
        return DB::transaction(function () use ($idea, $data) {
            $previousStatus = $idea->status;

            $updated = $this->ideaRepo->update($idea->id, [
                'status' => IdeaStatus::REJECTED->value,
                'feedback' => $data['feedback'],
                'score' => $data['score'] ?? null,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);

            if ($updated) {
                $this->createAuditLog($idea, 'rejected', $data['feedback'], Auth::user(), [
                    'score' => $data['score'] ?? null,
                    'previous_status' => $previousStatus,
                    'reviewer_name' => Auth::user()->name
                ]);
            }

            return $updated;
        });
    }

    /**
     * Mark idea as needing revision (SystemAdmin).
     */
    public function markForRevision(Idea $idea, array $data): bool
    {
        return DB::transaction(function () use ($idea, $data) {
            $previousStatus = $idea->status;

            $updated = $this->ideaRepo->update($idea->id, [
                'status' => IdeaStatus::NEEDS_REVISION->value,
                'feedback' => $data['feedback'],
                'score' => $data['score'] ?? null,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);

            if ($updated) {
                $this->createAuditLog($idea, 'needs_revision', $data['feedback'], Auth::user(), [
                    'score' => $data['score'] ?? null,
                    'previous_status' => $previousStatus,
                    'reviewer_name' => Auth::user()->name
                ]);
            }

            return $updated;
        });
    }

    /**
     * Assign supervisor to an idea (SystemAdmin).
     */
    public function assignSupervisor(Idea $idea, int $supervisorId): bool
    {
        $supervisor = $this->userRepo->find($supervisorId);
        
        if (!$supervisor || !$supervisor->hasRole('track_supervisor')) {
            throw new \Exception('Selected user is not a track supervisor.');
        }

        return DB::transaction(function () use ($idea, $supervisor) {
            $previousSupervisor = $idea->reviewer;

            $updated = $this->ideaRepo->update($idea->id, [
                'reviewed_by' => $supervisor->id,
                'status' => $idea->status === 'submitted' ? 'under_review' : $idea->status
            ]);

            if ($updated) {
                $this->createAuditLog($idea, 'supervisor_assigned', "Supervisor assigned: {$supervisor->name}", Auth::user(), [
                    'new_supervisor_id' => $supervisor->id,
                    'new_supervisor_name' => $supervisor->name,
                    'previous_supervisor_id' => $previousSupervisor?->id,
                    'previous_supervisor_name' => $previousSupervisor?->name,
                    'assigned_by' => Auth::user()->name
                ]);
            }

            return $updated;
        });
    }

    /**
     * Update score for an idea (SystemAdmin).
     */
    public function updateScore(Idea $idea, float $score): bool
    {
        return DB::transaction(function () use ($idea, $score) {
            $previousScore = $idea->score;

            $updated = $this->ideaRepo->update($idea->id, [
                'score' => $score,
                'reviewed_by' => $idea->reviewed_by ?: Auth::id(),
                'reviewed_at' => $idea->reviewed_at ?: now()
            ]);

            if ($updated) {
                $this->createAuditLog($idea, 'score_updated', "Score updated from {$previousScore} to {$score}", Auth::user(), [
                    'new_score' => $score,
                    'previous_score' => $previousScore,
                    'updated_by' => Auth::user()->name
                ]);
            }

            return $updated;
        });
    }

    /**
     * Delete an idea (SystemAdmin).
     */
    public function deleteIdea(Idea $idea): bool
    {
        return DB::transaction(function () use ($idea) {
            // Create audit log before deletion
            $this->createAuditLog($idea, 'deleted', 'Idea deleted by system administrator', Auth::user(), [
                'idea_title' => $idea->title,
                'team_name' => $idea->team->name ?? 'Unknown',
                'deleted_at' => now()->toISOString()
            ]);

            return $this->ideaRepo->delete($idea->id);
        });
    }

    /**
     * Get export data for ideas.
     */
    public function getExportData(array $filters = []): Collection
    {
        $query = $this->ideaRepo->getModel()->with(['team.leader', 'team.hackathon', 'track', 'reviewer']);

        // Apply same filters as index
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('team', function ($teamQuery) use ($search) {
                      $teamQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['track'])) {
            $query->where('track_id', $filters['track']);
        }

        if (!empty($filters['edition'])) {
            $query->whereHas('team.hackathon', function ($q) use ($filters) {
                $q->where('id', $filters['edition']);
            });
        }

        return $query->get()->map(function ($idea) {
            return [
                'id' => $idea->id,
                'title' => $idea->title,
                'description' => $idea->description,
                'team_name' => $idea->team->name ?? 'N/A',
                'team_leader' => $idea->team->leader->name ?? 'N/A',
                'track' => $idea->track->name ?? 'N/A',
                'edition' => $idea->team->hackathon->name ?? 'N/A',
                'status' => $idea->status,
                'score' => $idea->score,
                'reviewer' => $idea->reviewer->name ?? 'N/A',
                'submitted_at' => $idea->submitted_at?->format('Y-m-d H:i:s'),
                'reviewed_at' => $idea->reviewed_at?->format('Y-m-d H:i:s'),
                'created_at' => $idea->created_at->format('Y-m-d H:i:s')
            ];
        });
    }

    /**
     * Get detailed statistics for system admin.
     */
    public function getDetailedStatistics(): array
    {
        return [
            'total' => $this->ideaRepo->count(),
            'by_status' => $this->ideaRepo->getModel()
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_track' => $this->ideaRepo->getModel()
                ->with('track:id,name')
                ->select('track_id', DB::raw('count(*) as count'))
                ->groupBy('track_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'track' => $item->track->name ?? 'Unknown',
                        'count' => $item->count
                    ];
                }),
            'average_score' => $this->ideaRepo->getModel()->whereNotNull('score')->avg('score'),
            'recent_submissions' => $this->ideaRepo->getModel()->where('created_at', '>=', now()->subDays(7))->count(),
            'pending_review' => $this->ideaRepo->getModel()->whereIn('status', ['submitted', 'under_review'])->count()
        ];
    }

    /**
     * Get evaluation criteria for review page.
     */
    public function getEvaluationCriteria(?Track $track = null): array
    {
        $defaultCriteria = [
            ['name' => 'Innovation', 'weight' => 25, 'description' => 'How innovative and creative is the solution?'],
            ['name' => 'Technical Feasibility', 'weight' => 25, 'description' => 'Is the technical approach sound and achievable?'],
            ['name' => 'Impact', 'weight' => 25, 'description' => 'What is the potential impact of this solution?'],
            ['name' => 'Presentation', 'weight' => 25, 'description' => 'How well is the idea presented and documented?']
        ];

        if ($track && $track->evaluation_criteria) {
            return $track->evaluation_criteria;
        }

        return $defaultCriteria;
    }

    // =====================================================
    // Private Helper Methods
    // =====================================================

    /**
     * Upload a single file for an idea.
     */
    private function uploadSingleFile(Idea $idea, UploadedFile $file, User $uploader): IdeaFile
    {
        // Validate file
        $this->validateFile($file);

        // Store file
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('ideas/' . $idea->id, $fileName, 'public');

        // Create file record
        return IdeaFile::create([
            'idea_id' => $idea->id,
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $uploader->id,
        ]);
    }

    /**
     * Validate file for upload.
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check file size
        $maxSize = config('hackathon.max_file_size', 15728640); // 15MB
        if ($file->getSize() > $maxSize) {
            throw new \Exception('حجم الملف كبير جداً (الحد الأقصى: 15MB)');
        }

        // Check file type
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('نوع الملف غير مسموح (PDF, DOC, DOCX, PPT, PPTX فقط)');
        }
    }

    /**
     * Validate idea for submission.
     */
    private function validateIdeaForSubmission(Idea $idea): void
    {
        $errors = [];

        if (empty($idea->title)) {
            $errors[] = 'عنوان الفكرة مطلوب';
        }

        if (empty($idea->problem_statement)) {
            $errors[] = 'بيان المشكلة مطلوب';
        }

        if (empty($idea->solution_approach)) {
            $errors[] = 'نهج الحل مطلوب';
        }

        if (empty($idea->expected_impact)) {
            $errors[] = 'الأثر المتوقع مطلوب';
        }

        if (!empty($errors)) {
            throw new \Exception('يرجى إكمال الحقول المطلوبة: ' . implode(', ', $errors));
        }
    }

    /**
     * Check if user can edit an idea.
     */
    private function canUserEditIdea(Idea $idea, User $user): bool
    {
        // Team leader and members can edit
        return $idea->team->leader_id === $user->id ||
               $idea->team->acceptedMembers()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user can review an idea.
     */
    private function canUserReviewIdea(Idea $idea, User $reviewer): bool
    {
        // Track supervisors can review ideas in their tracks
        return $reviewer->supervisedTracks()->where('id', $idea->track_id)->exists() ||
               $reviewer->hasRole(['hackathon_admin', 'system_admin']);
    }

    /**
     * Create audit log entry.
     */
    private function createAuditLog(Idea $idea, string $action, string $description, User $user, array $metadata = []): void
    {
        IdeaAuditLog::create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'metadata' => array_merge([
                'user_name' => $user->name,
                'user_role' => $user->user_type,
            ], $metadata),
        ]);
    }

    /**
     * Get status text in Arabic.
     */
    private function getStatusText(string $status): string
    {
        $statusMap = [
            'draft' => 'مسودة',
            'submitted' => 'مقدمة',
            'under_review' => 'قيد المراجعة',
            'needs_revision' => 'تحتاج مراجعة',
            'accepted' => 'مقبولة',
            'rejected' => 'مرفوضة',
        ];

        return $statusMap[$status] ?? $status;
    }
}
