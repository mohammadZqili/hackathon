<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdeaInstruction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'idea_id',
        'user_id',
        'user_role',
        'instruction_text',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the idea that owns the instruction.
     */
    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }

    /**
     * Get the user who created the instruction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active instructions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include supervisor instructions.
     */
    public function scopeSupervisor($query)
    {
        return $query->where('user_role', 'supervisor');
    }

    /**
     * Scope a query to only include team leader instructions.
     */
    public function scopeTeamLeader($query)
    {
        return $query->where('user_role', 'team_leader');
    }
}