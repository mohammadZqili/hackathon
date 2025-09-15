<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasSuperAdminPrivileges;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;

    use HasUlids;

    use \OwenIt\Auditing\Auditable;

    use HasRoles, HasSuperAdminPrivileges {
        HasSuperAdminPrivileges::hasPermissionTo insteadof HasRoles;
        HasSuperAdminPrivileges::hasRole insteadof HasRoles;
        HasRoles::hasPermissionTo as hasBasePermissionTo;
        HasRoles::hasRole as hasBaseRole;
    }
    use Searchable;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'password_expiry_at',
        'password_changed_at',
        'force_password_change',
        'disable_account',
        'social_logins',
        'avatar',
        // Hackathon fields
        'date_of_birth',
        'phone',
        'national_id',
        'user_type',
        'occupation',
        'job_title',
        'is_active',
        'last_login_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'password_expiry_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'force_password_change' => 'boolean',
        'disable_account' => 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'social_logins' => 'array',
        // Hackathon casts
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    protected $appends = ['created_at_formatted'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->user_slug = 'user-' . Str::random(12);
            if (!$user->password) {
                $user->password = null;
            }
        });
    }


    /**
     * Format date with relative time for recent dates
     * - Within 24 hours: "2 hours ago", "Just now"
     * - Yesterday: "Yesterday"
     * - This year: "May 6"
     * - Other years: "May 6, 2020"
     */
    public function formatDateStyle(?Carbon $date = null): string
    {
        $date = $date ?? $this->created_at;

        // Ensure we have a Carbon instance
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if (!$date) {
            return '';
        }

        // Very recent (less than 5 minutes)
        if ($date->diffInMinutes() < 5) {
            return 'Just now';
        }

        // Within last 24 hours
        if ($date->isToday()) {
            return $date->diffForHumans(['short' => false, 'parts' => 1]);
        }

        // Yesterday
        if ($date->isYesterday()) {
            return 'Yesterday';
        }

        // This year (but not recent)
        if ($date->isCurrentYear()) {
            return $date->format('F j'); // "May 6"
        }

        // Different year - show full date
        return $date->format('F j, Y'); // "May 6, 2020"
    }


    public function getCreatedAtFormattedAttribute(): string
    {
        // Ensure created_at is a Carbon instance
        $createdAt = $this->created_at instanceof Carbon
            ? $this->created_at
            : ($this->created_at ? Carbon::parse($this->created_at) : null);

        return $this->formatDateStyle($createdAt);
    }


    public function isPasswordExpired(): bool
    {
        if (!$this->password_expiry_at) {
            return false;
        }

        return $this->password_expiry_at->isPast();
    }


    public function daysUntilPasswordExpiry(): int
    {
        if (!$this->password_expiry_at) {
            return 0;
        }

        $expiryDate = Carbon::createFromTimestamp($this->password_expiry_at);
        return max(0, now()->diffInDays($expiryDate));
    }


    public function loginHistory()
    {
        return $this->morphMany(LoginHistory::class, 'user');
    }


    public function latestLogin()
    {
        return $this->morphOne(LoginHistory::class, 'user')->latestOfMany('login_at');
    }


    public function isLoggedIn(): bool
    {
        return $this->latestLogin?->logout_at === null;
    }


    public function isSuperUser(): bool
    {
        // Now uses the trait method isSuperAdmin() internally
        return $this->isSuperAdmin();
    }


    public function canBeDeleted(): bool
    {
        return !$this->isSuperUser();
    }


    public function canChangeRole(): bool
    {
        return !$this->isSuperUser();
    }


    public function canChangeAccountStatus(): bool
    {
        return !$this->isSuperUser();
    }


    public function toSearchableArray(): array
    {
        return array_merge($this->toArray(), [
            'id' => (string) $this->id,
            'created_at' => $this->created_at->timestamp,
            'collection_name' => 'users',
        ]);
    }

    // =====================================================
    // Hackathon Relationships
    // =====================================================

    /**
     * Get teams led by this user.
     */
    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    /**
     * Get teams this user is a member of.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot(['status', 'role', 'joined_at', 'invited_at', 'invited_by'])
            ->withTimestamps();
    }

    /**
     * Get active teams (accepted membership).
     */
    public function activeTeams(): BelongsToMany
    {
        return $this->teams()->wherePivot('status', 'accepted');
    }

    /**
     * Get workshop registrations.
     */
    public function workshopRegistrations(): HasMany
    {
        return $this->hasMany(WorkshopRegistration::class);
    }

//    /**
//     * Get tracks this user supervises.
//     */
//    public function supervisedTracks(): BelongsToMany
//    {
//        return $this->belongsToMany(Track::class, 'track_supervisors')
//            ->withTimestamps();
//    }

    /**
     * Get ideas reviewed by this user.
     */
    public function reviewedIdeas(): HasMany
    {
        return $this->hasMany(Idea::class, 'reviewed_by');
    }

    /**
     * Get idea files uploaded by this user.
     */
    public function uploadedIdeaFiles(): HasMany
    {
        return $this->hasMany(IdeaFile::class, 'uploaded_by');
    }

    /**
     * Get audit logs created by this user.
     */
    public function ideaAuditLogs(): HasMany
    {
        return $this->hasMany(IdeaAuditLog::class);
    }

    // =====================================================
    // Hackathon Helper Methods
    // =====================================================

    /**
     * Check if user is a team leader.
     */
    public function isTeamLeader(): bool
    {
        return $this->user_type === 'team_leader' || $this->ledTeams()->exists();
    }

    /**
     * Check if user is a track supervisor.
     */
    public function isTrackSupervisor(): bool
    {
        return $this->user_type === 'track_supervisor' || $this->supervisedTracks()->exists();
    }

    /**
     * Check if user can create a team.
     */
    public function canCreateTeam(): bool
    {
        return in_array($this->user_type, ['team_leader', 'team_member', 'visitor']);
    }

    /**
     * Check if user can join teams.
     */
    public function canJoinTeam(): bool
    {
        return in_array($this->user_type, ['team_member', 'visitor']);
    }

    /**
     * Check if user is hackathon admin.
     */
    public function isHackathonAdmin(): bool
    {
        return $this->user_type === 'hackathon_admin' || $this->hasRole('hackathon_admin');
    }

    /**
     * Check if user is system admin.
     */
    public function isSystemAdmin(): bool
    {
        // Check if user is system admin using the enhanced trait
        return $this->user_type === 'admin' || $this->isSuperAdmin();
    }

    /**
     * Get current team for a hackathon.
     */
    public function getTeamForHackathon($hackathonId)
    {
        return $this->activeTeams()
            ->where('hackathon_id', $hackathonId)
            ->first();
    }

    /**
     * Check if user has a team in current hackathon.
     */
    public function hasTeamInCurrentHackathon(): bool
    {
        $currentHackathon = \App\Models\Hackathon::current();
        if (!$currentHackathon) {
            return false;
        }

        return $this->activeTeams()
            ->where('hackathon_id', $currentHackathon->id)
            ->exists();
    }

    /**
     * Get user's role label.
     */
    public function getUserTypeLabel(): string
    {
        return match($this->user_type) {
            'system_admin' => 'مدير النظام',
            'hackathon_admin' => 'مدير الهاكاثون',
            'track_supervisor' => 'مشرف المسار',
            'workshop_supervisor' => 'مشرف الورشة',
            'team_leader' => 'قائد الفريق',
            'team_member' => 'عضو الفريق',
            'visitor' => 'زائر',
            default => 'مستخدم',
        };
    }

    // =====================================================
    // Hackathon Role Relationships
    // =====================================================

    /**
     * Get supervised tracks for track supervisors
     */
    public function supervisedTracks()
    {
        return $this->belongsToMany(Track::class, 'track_supervisors', 'user_id', 'track_id')
                    ->withTimestamps();
    }

    /**
     * Get tracks supervised by this user in a specific edition
     */
    public function tracksInEdition($editionId)
    {
        return $this->supervisedTracks()
            ->where('tracks.edition_id', $editionId);
    }

    /**
     * Get the single track assigned to this supervisor in current edition
     * Track supervisors have only ONE track assignment
     */
    public function getAssignedTrack($editionId)
    {
        if (!$this->hasRole('track_supervisor')) {
            return null;
        }

        return $this->supervisedTracks()
            ->where('tracks.edition_id', $editionId)
            ->first();
    }

    /**
     * Get the single track ID for this supervisor
     */
    public function getAssignedTrackId($editionId)
    {
        $track = $this->getAssignedTrack($editionId);
        return $track ? $track->id : null;
    }

    /**
     * Get assigned track IDs for a specific edition (or all if admin)
     */
    public function getAssignedTrackIds($editionId = null)
    {
        // System admins and hackathon admins have no restrictions
        if ($this->hasRole('system_admin') || $this->hasRole('hackathon_admin')) {
            return null; // No restriction
        }

        // Track supervisors only see their assigned tracks
        if ($this->hasRole('track_supervisor')) {
            $query = $this->supervisedTracks();
            if ($editionId) {
                $query->where('tracks.edition_id', $editionId);
            }
            return $query->pluck('tracks.id')->toArray();
        }

        // Other roles have no track access
        return [];
    }

    /**
     * Check if user can access a specific track
     */
    public function canAccessTrack($trackId, $editionId = null)
    {
        // Admins can access all tracks
        if ($this->hasRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors check assigned tracks
        if ($this->hasRole('track_supervisor')) {
            $assignedTracks = $this->getAssignedTrackIds($editionId);
            return in_array($trackId, $assignedTracks);
        }

        return false;
    }

    /**
     * Check if user has any tracks in the given edition
     */
    public function hasTracksInEdition($editionId)
    {
        if ($this->hasRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        if ($this->hasRole('track_supervisor')) {
            return $this->tracksInEdition($editionId)->exists();
        }

        return false;
    }

    /**
     * Get supervised workshops for workshop supervisors
     */
    public function supervisedWorkshops()
    {
        return $this->belongsToMany(Workshop::class, 'workshop_supervisors', 'user_id', 'workshop_id')
                    ->withTimestamps();
    }

    /**
     * Get the user's role (alias for user_type)
     */
    public function getRoleAttribute()
    {
        return $this->user_type;
    }

    /**
     * Set the user's role (alias for user_type)
     */
    public function setRoleAttribute($value)
    {
        $this->user_type = $value;
    }
}
