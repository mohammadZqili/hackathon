<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'status',
        'role',
        'joined_at',
        'invited_at',
        'invited_by',
        'invitation_message',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'invited_at' => 'datetime',
    ];

    /**
     * Get the team this membership belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user who is a member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who sent the invitation.
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Accept the membership.
     */
    public function accept(): bool
    {
        return $this->update([
            'status' => 'accepted',
            'joined_at' => now(),
        ]);
    }

    /**
     * Reject the membership.
     */
    public function reject(): bool
    {
        return $this->update(['status' => 'rejected']);
    }

    /**
     * Remove the member from the team.
     */
    public function remove(): bool
    {
        return $this->update(['status' => 'removed']);
    }

    /**
     * Check if membership is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if membership is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if user is the team leader.
     */
    public function isLeader(): bool
    {
        return $this->role === 'leader';
    }

    /**
     * Scope to get accepted members.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope to get pending members.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
