<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateAction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function actionType()
    {
        return $this->belongsTo(ActionType::class);
    }

    public function components()
    {
        return $this->morphedByMany(Component::class, 'tmpl_actionable');
    }

    public function statements()
    {
        return $this->morphedByMany(Statement::class, 'tmpl_actionable');
    }
}
