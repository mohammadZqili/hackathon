<?php

namespace App\Services;

use App\Models\Idea;
use App\Models\Team;
use App\Models\User;
use App\Models\IdeaFile;
use App\Models\IdeaAuditLog;
use App\Repositories\IdeaRepository;
use App\Services\Contracts\IdeaServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class IdeaService implements IdeaServiceInterface
{
    public function __construct(
        private IdeaRepository $ideaRepo
    ) {}

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
    private function createAuditLog(Idea $idea, string $action, string $description, User $user): void
    {
        IdeaAuditLog::create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'metadata' => [
                'user_name' => $user->name,
                'user_role' => $user->user_type,
            ],
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