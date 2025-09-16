<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdeaComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'idea_id',
        'user_id',
        'comment',
        'is_supervisor',
        'parent_id',
    ];

    protected $casts = [
        'is_supervisor' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the idea this comment belongs to.
     */
    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

    /**
     * Get the user who made this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment if this is a reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(IdeaComment::class, 'parent_id');
    }

    /**
     * Get replies to this comment.
     */
    public function replies()
    {
        return $this->hasMany(IdeaComment::class, 'parent_id');
    }

    /**
     * Scope to get top-level comments (not replies).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get formatted time for display.
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}