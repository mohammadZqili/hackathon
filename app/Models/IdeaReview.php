<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdeaReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'idea_reviews';

    protected $fillable = [
        'idea_id',
        'reviewer_id',
        'status',
        'feedback',
        'score',
        'criteria_scores',
        'reviewed_at'
    ];

    protected $casts = [
        'criteria_scores' => 'array',
        'reviewed_at' => 'datetime',
        'score' => 'integer'
    ];

    /**
     * Get the idea that this review belongs to.
     */
    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }

    /**
     * Get the reviewer (user) who created this review.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}