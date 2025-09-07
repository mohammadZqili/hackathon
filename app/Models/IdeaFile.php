<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class IdeaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'idea_id',
        'original_name',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'file_category',
        'description',
        'uploaded_by',
        'is_virus_scanned',
        'is_safe',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_virus_scanned' => 'boolean',
        'is_safe' => 'boolean',
    ];

    /**
     * Get the idea this file belongs to.
     */
    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

    /**
     * Get the user who uploaded this file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file's URL.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the file's download URL.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('files.download', $this->id);
    }

    /**
     * Get human-readable file size.
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the file's icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->file_category) {
            'presentation' => 'presentation',
            'document' => 'document-text',
            'image' => 'photograph',
            'video' => 'video-camera',
            default => 'document',
        };
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if file is a video.
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * Check if file is a document.
     */
    public function isDocument(): bool
    {
        return in_array($this->file_type, ['pdf', 'doc', 'docx', 'txt']);
    }

    /**
     * Check if file is a presentation.
     */
    public function isPresentation(): bool
    {
        return in_array($this->file_type, ['ppt', 'pptx']);
    }

    /**
     * Delete the physical file.
     */
    public function deleteFile(): bool
    {
        if (Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }
        
        return true;
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::deleted(function ($file) {
            $file->deleteFile();
        });
    }

    /**
     * Scope to get safe files.
     */
    public function scopeSafe($query)
    {
        return $query->where('is_safe', true);
    }

    /**
     * Scope to get files by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('file_category', $category);
    }
}
