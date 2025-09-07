<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'user_id',
        'barcode',
        'status',
        'registered_at',
        'confirmed_at',
        'attended_at',
        'attendance_method',
        'marked_by',
        'notes',
        'additional_data',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'attended_at' => 'datetime',
        'additional_data' => 'array',
    ];

    /**
     * Get the workshop this registration belongs to.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Get the user who registered.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who marked attendance.
     */
    public function attendanceMarker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Confirm the registration.
     */
    public function confirm(): bool
    {
        if ($this->status !== 'registered') {
            return false;
        }

        return $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Cancel the registration.
     */
    public function cancel(): bool
    {
        if (in_array($this->status, ['attended', 'cancelled'])) {
            return false;
        }

        $success = $this->update(['status' => 'cancelled']);

        if ($success) {
            $this->workshop()->decrement('current_attendees');
        }

        return $success;
    }

    /**
     * Mark attendance.
     */
    public function markAttended(?User $markedBy = null, string $method = 'manual'): bool
    {
        if (!in_array($this->status, ['registered', 'confirmed'])) {
            return false;
        }

        return $this->update([
            'status' => 'attended',
            'attended_at' => now(),
            'attendance_method' => $method,
            'marked_by' => $markedBy?->id,
        ]);
    }

    /**
     * Mark as no-show.
     */
    public function markNoShow(?User $markedBy = null): bool
    {
        if (!in_array($this->status, ['registered', 'confirmed'])) {
            return false;
        }

        return $this->update([
            'status' => 'no_show',
            'marked_by' => $markedBy?->id,
        ]);
    }

    /**
     * Check if registration is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if user attended.
     */
    public function hasAttended(): bool
    {
        return $this->status === 'attended';
    }

    /**
     * Check if registration is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if user was a no-show.
     */
    public function isNoShow(): bool
    {
        return $this->status === 'no_show';
    }

    /**
     * Get the registration status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'registered' => 'blue',
            'confirmed' => 'green',
            'cancelled' => 'red',
            'attended' => 'emerald',
            'no_show' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get the registration status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'registered' => 'Registered',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'attended' => 'Attended',
            'no_show' => 'No Show',
            default => 'Unknown',
        };
    }

    /**
     * Get the barcode image URL.
     */
    public function getBarcodeImageUrlAttribute(): string
    {
        // This would generate a barcode image URL
        // You might want to implement a barcode generator service
        return route('barcode.generate', $this->barcode);
    }

    /**
     * Scope to get registrations by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get attended registrations.
     */
    public function scopeAttended($query)
    {
        return $query->where('status', 'attended');
    }

    /**
     * Scope to get confirmed registrations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope to get pending registrations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'registered');
    }
}
