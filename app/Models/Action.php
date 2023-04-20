<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function actionType()
    {
        return $this->belongsTo(ActionType::class);
    }

    public function components()
    {
        return $this->morphedByMany(Component::class, 'actionable');
    }

    public function statements()
    {
        return $this->morphedByMany(Statement::class, 'actionable');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
