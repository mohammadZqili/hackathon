<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'user_id',  // For Jetstream compatibility
        'personal_team',  // For Jetstream compatibility
        'hackathon_id',
        'leader_id',
        'track_id',
        'invite_code',
        'max_members',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'max_members' => 'integer',
        'submitted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name);
            }
            if (empty($team->invite_code)) {
                $team->invite_code = Str::upper(Str::random(8));
            }
        });
    }

    /**
     * Get the hackathon this team belongs to.
     */
    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    /**
     * Get the team leader.
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get the track this team is participating in.
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Get all team members including the leader.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot(['status', 'role', 'joined_at', 'invited_at', 'invited_by'])
            ->withTimestamps();
    }

    /**
     * Get only accepted team members.
     */
    public function acceptedMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'accepted');
    }

    /**
     * Get pending member requests.
     */
    public function pendingMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'pending');
    }

    /**
     * Get the team's idea.
     */
    public function idea(): HasOne
    {
        return $this->hasOne(Idea::class);
    }

    /**
     * Get team member records.
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Add a member to the team.
     */
    public function addMember(User $user, string $status = 'pending', ?User $invitedBy = null): TeamMember
    {
        return $this->teamMembers()->create([
            'user_id' => $user->id,
            'status' => $status,
            'role' => $user->id === $this->leader_id ? 'leader' : 'member',
            'invited_by' => $invitedBy?->id,
            'invited_at' => now(),
            'joined_at' => $status === 'accepted' ? now() : null,
        ]);
    }

    /**
     * Remove a member from the team.
     */
    public function removeMember(User $user): bool
    {
        return $this->teamMembers()
            ->where('user_id', $user->id)
            ->update(['status' => 'removed']);
    }

    /**
     * Accept a member's request.
     */
    public function acceptMember(User $user): bool
    {
        return $this->teamMembers()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'accepted', 'joined_at' => now()]);
    }

    /**
     * Check if team is full.
     */
    public function isFull(): bool
    {
        return $this->acceptedMembers()->count() >= $this->max_members;
    }

    /**
     * Check if user is a member of this team.
     */
    public function hasMember(User $user): bool
    {
        return $this->acceptedMembers()->where('users.id', $user->id)->exists();
    }

    /**
     * Check if user is the leader of this team.
     */
    public function isLeader(User $user): bool
    {
        return $this->leader_id === $user->id;
    }

    /**
     * Get the number of accepted members.
     */
    public function getMembersCountAttribute(): int
    {
        return $this->acceptedMembers()->count();
    }

    /**
     * Get available spots in the team.
     */
    public function getAvailableSpotsAttribute(): int
    {
        return max(0, $this->max_members - $this->members_count);
    }

    /**
     * Check if team can submit idea.
     */
    public function canSubmitIdea(): bool
    {
        return $this->status === 'active' && 
               $this->hackathon->isIdeaSubmissionOpen() &&
               !$this->idea;
    }

    /**
     * Submit the team's idea.
     */
    public function submitIdea(): bool
    {
        if (!$this->idea || $this->idea->status !== 'draft') {
            return false;
        }

        $this->idea->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return true;
    }

    /**
     * Generate a new invite code.
     */
    public function regenerateInviteCode(): string
    {
        $newCode = Str::upper(Str::random(8));
        $this->update(['invite_code' => $newCode]);
        return $newCode;
    }

    /**
     * Scope to get teams by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get teams for a specific hackathon.
     */
    public function scopeForHackathon($query, int $hackathonId)
    {
        return $query->where('hackathon_id', $hackathonId);
    }
}
