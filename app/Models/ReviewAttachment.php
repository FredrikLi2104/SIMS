<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReviewAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'statement_id',
        'organisation_id',
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'attachment_type',
    ];

    /**
     * Relationships
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function statement()
    {
        return $this->belongsTo(Statement::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the file URL for download
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get human-readable file size
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Delete file from storage when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }
        });
    }
}
