<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'agenda',
        'interviewee',
        'creator_id',
        'organisation_id',
        'plan_id',
        'emails',
        'status',
        'scheduled_date',
        'conducted_date',
        'notes',
        'attachments'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'conducted_date' => 'datetime',
    ];

    // Status constants
    const STATUS_PLANNED = 'planned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function statements() {
        return $this->belongsToMany(Statement::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Status helper methods
    public function isCompleted() {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function markAsInProgress() {
        $this->status = self::STATUS_IN_PROGRESS;
        $this->save();
        return $this;
    }

    public function markAsCompleted() {
        $this->status = self::STATUS_COMPLETED;
        if (!$this->conducted_date) {
            $this->conducted_date = now();
        }
        $this->save();
        return $this;
    }

    public function markAsCancelled() {
        $this->status = self::STATUS_CANCELLED;
        $this->save();
        return $this;
    }

}
