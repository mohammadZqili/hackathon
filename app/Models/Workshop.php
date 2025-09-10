<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackathon_id',
        'title',
        'slug',
        'description',
        'type',
        'start_time',
        'end_time',
        'format',
        'location',
        'max_attendees',
        'current_attendees',
        'prerequisites',
        'materials',
        'thumbnail_path',
        'is_active',
        'requires_registration',
        'registration_deadline',
        'settings',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'registration_deadline' => 'datetime',
        'materials' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'requires_registration' => 'boolean',
        'max_attendees' => 'integer',
        'current_attendees' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($workshop) {
            if (empty($workshop->slug)) {
                $workshop->slug = Str::slug($workshop->title);
            }
        });
    }

    /**
     * Get the hackathon this workshop belongs to.
     */
    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    /**
     * Get the edition this workshop belongs to.
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class, 'hackathon_edition_id');
    }

    /**
     * Get the organizations associated with this workshop.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'workshop_organizations')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Get the speakers for this workshop.
     */
    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'workshop_speakers')
            ->withPivot(['role', 'order'])
            ->withTimestamps()
            ->orderBy('workshop_speakers.order');
    }

    /**
     * Get the registrations for this workshop.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(WorkshopRegistration::class);
    }

    /**
     * Get the attendance records for this workshop.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(WorkshopAttendance::class);
    }

    /**
     * Get the users registered for this workshop.
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workshop_registrations')
            ->withPivot(['barcode', 'status', 'registered_at', 'attended_at'])
            ->withTimestamps();
    }

    /**
     * Get confirmed attendees.
     */
    public function confirmedAttendees(): BelongsToMany
    {
        return $this->attendees()->wherePivot('status', 'confirmed');
    }

    /**
     * Get users who actually attended.
     */
    public function actualAttendees(): BelongsToMany
    {
        return $this->attendees()->wherePivot('status', 'attended');
    }

    /**
     * Register a user for this workshop.
     */
    public function registerUser(User $user): ?WorkshopRegistration
    {
        if (!$this->canRegister()) {
            return null;
        }

        if ($this->isUserRegistered($user)) {
            return null;
        }

        if ($this->isFull()) {
            return null;
        }

        $registration = $this->registrations()->create([
            'user_id' => $user->id,
            'barcode' => $this->generateBarcode(),
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        $this->increment('current_attendees');

        return $registration;
    }

    /**
     * Check if user is registered for this workshop.
     */
    public function isUserRegistered(User $user): bool
    {
        return $this->registrations()
            ->where('user_id', $user->id)
            ->whereIn('status', ['registered', 'confirmed', 'attended'])
            ->exists();
    }

    /**
     * Mark attendance for a user by barcode.
     */
    public function markAttendance(string $barcode, ?User $markedBy = null): ?WorkshopRegistration
    {
        $registration = $this->registrations()
            ->where('barcode', $barcode)
            ->whereIn('status', ['registered', 'confirmed'])
            ->first();

        if (!$registration) {
            return null;
        }

        $registration->update([
            'status' => 'attended',
            'attended_at' => now(),
            'attendance_method' => 'barcode_scan',
            'marked_by' => $markedBy?->id,
        ]);

        return $registration;
    }

    /**
     * Check if workshop is full.
     */
    public function isFull(): bool
    {
        if (!$this->max_attendees) {
            return false;
        }

        return $this->current_attendees >= $this->max_attendees;
    }

    /**
     * Check if registration is open.
     */
    public function canRegister(): bool
    {
        if (!$this->is_active || !$this->requires_registration) {
            return false;
        }

        if ($this->registration_deadline && now()->isAfter($this->registration_deadline)) {
            return false;
        }

        if ($this->isFull()) {
            return false;
        }

        return true;
    }

    /**
     * Check if workshop has started.
     */
    public function hasStarted(): bool
    {
        return now()->isAfter($this->start_time);
    }

    /**
     * Check if workshop has ended.
     */
    public function hasEnded(): bool
    {
        return now()->isAfter($this->end_time);
    }

    /**
     * Check if workshop is currently running.
     */
    public function isRunning(): bool
    {
        return $this->hasStarted() && !$this->hasEnded();
    }

    /**
     * Generate a unique barcode for registration.
     */
    private function generateBarcode(): string
    {
        do {
            $barcode = strtoupper(Str::random(10));
        } while (WorkshopRegistration::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Get workshop duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Get available spots.
     */
    public function getAvailableSpotsAttribute(): ?int
    {
        if (!$this->max_attendees) {
            return null;
        }

        return max(0, $this->max_attendees - $this->current_attendees);
    }

    /**
     * Get attendance rate.
     */
    public function getAttendanceRateAttribute(): float
    {
        if ($this->current_attendees === 0) {
            return 0;
        }

        $attended = $this->registrations()->where('status', 'attended')->count();
        return round(($attended / $this->current_attendees) * 100, 2);
    }

    /**
     * Get the workshop's thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    /**
     * Scope to get active workshops.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get upcoming workshops.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    /**
     * Scope to get workshops by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get workshops for a specific hackathon.
     */
    public function scopeForHackathon($query, int $hackathonId)
    {
        return $query->where('hackathon_id', $hackathonId);
    }
}
