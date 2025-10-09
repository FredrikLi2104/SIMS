<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'offset_days' => 'integer',
        'duration_days' => 'integer',
        'hours' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Get the template that owns this task.
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the task status for this template task.
     */
    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class);
    }
}
