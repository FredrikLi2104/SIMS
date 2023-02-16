<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['start_for_humans', 'end_for_humans'];

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    protected function startForHumans(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => Carbon::parse($attribute['start'])->format('Y-m-d')
        );
    }

    protected function endForHumans(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => Carbon::parse($attribute['end'])->format('Y-m-d')
        );
    }
}
