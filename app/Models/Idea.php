<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Idea extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'track_id',
        'title',
        'description',
        'problem_statement',
        'solution_approach',
        'expected_impact',
        'technologies',
        'status',
        'score',
        'feedback',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'evaluation_scores',
    ];

    protected $casts = [
        'technologies' => 'array',
        'score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'evaluation_scores' => 'array',
    ];

    /**
     * Get the team that owns this idea.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the edition this idea belongs to.
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class, 'edition_id');
    }

    /**
     * Get the track this idea belongs to.
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Get the supervisor who reviewed this idea.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the supervisor who reviewed this idea (alias for reviewer).
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the files associated with this idea.
     */
    public function files(): HasMany
    {
        return $this->hasMany(IdeaFile::class);
    }

    /**
     * Get the audit logs for this idea.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(IdeaAuditLog::class);
    }

    /**
     * Submit the idea for review.
     */
    public function submit(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Log the submission
        $this->logAction('submitted', null, 'submitted', 'Idea submitted for review');

        return true;
    }

    /**
     * Accept the idea.
     */
    public function accept(User $reviewer, ?string $feedback = null, ?float $score = null, ?array $evaluationScores = null): bool
    {
        $this->update([
            'status' => 'accepted',
            'feedback' => $feedback,
            'score' => $score,
            'evaluation_scores' => $evaluationScores,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        // Log the acceptance
        $this->logAction('status_changed', 'accepted', 'accepted', $feedback, $reviewer);

        return true;
    }

    /**
     * Reject the idea.
     */
    public function reject(User $reviewer, ?string $feedback = null, ?float $score = null, ?array $evaluationScores = null): bool
    {
        $this->update([
            'status' => 'rejected',
            'feedback' => $feedback,
            'score' => $score,
            'evaluation_scores' => $evaluationScores,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        // Log the rejection
        $this->logAction('status_changed', 'rejected', 'rejected', $feedback, $reviewer);

        return true;
    }

    /**
     * Request revision for the idea.
     */
    public function requestRevision(User $reviewer, string $feedback): bool
    {
        $this->update([
            'status' => 'needs_revision',
            'feedback' => $feedback,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        // Log the revision request
        $this->logAction('status_changed', 'needs_revision', 'needs_revision', $feedback, $reviewer);

        return true;
    }

    /**
     * Log an action on this idea.
     */
    public function logAction(string $action, ?string $fieldName = null, $newValue = null, ?string $notes = null, ?User $user = null): void
    {
        $this->auditLogs()->create([
            'user_id' => $user?->id ?? auth()?->id(),
            'action' => $action,
            'field_name' => $fieldName,
            'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
            'notes' => $notes,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * Check if idea can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'needs_revision']);
    }

    /**
     * Check if idea can be submitted.
     */
    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft' && 
               !empty($this->title) && 
               !empty($this->description) &&
               $this->team->hackathon->isIdeaSubmissionOpen();
    }

    /**
     * Get the idea's status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'under_review' => 'yellow',
            'needs_revision' => 'orange',
            'accepted' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the idea's status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'needs_revision' => 'Needs Revision',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Scope to get ideas by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get ideas for a specific track.
     */
    public function scopeForTrack($query, int $trackId)
    {
        return $query->where('track_id', $trackId);
    }

    /**
     * Scope to get ideas that need review.
     */
    public function scopeNeedsReview($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review']);
    }
}
