<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'name',
        'email',
        'phone',
        'national_id',
        'job_title',
        'job_type',
        'workshop_id',
        'attended',
        'registered_at',
        'attended_at',
        'attended_by',
        'notes',
    ];

    protected $casts = [
        'attended' => 'boolean',
        'registered_at' => 'datetime',
        'attended_at' => 'datetime',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function attendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attended_by');
    }

    public function getStatusAttribute(): string
    {
        return $this->attended ? 'attended' : 'registered';
    }
}