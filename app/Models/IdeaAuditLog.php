<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdeaAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'idea_id',
        'user_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'notes',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the idea this log belongs to.
     */
    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable action description.
     */
    public function getActionDescriptionAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Idea was created',
            'updated' => 'Idea was updated',
            'submitted' => 'Idea was submitted for review',
            'status_changed' => "Status changed to {$this->new_value}",
            'file_added' => 'File was added',
            'file_removed' => 'File was removed',
            'comment_added' => 'Comment was added',
            'score_updated' => 'Score was updated',
            default => 'Unknown action',
        };
    }

    /**
     * Get the change summary.
     */
    public function getChangeSummaryAttribute(): string
    {
        if ($this->field_name && $this->old_value !== null && $this->new_value !== null) {
            return "Changed {$this->field_name} from '{$this->old_value}' to '{$this->new_value}'";
        }

        if ($this->field_name && $this->new_value !== null) {
            return "Set {$this->field_name} to '{$this->new_value}'";
        }

        return $this->action_description;
    }

    /**
     * Scope to get logs by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get recent logs.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
