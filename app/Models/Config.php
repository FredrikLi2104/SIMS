<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function templates()
    {
        return $this->belongsToMany(Template::class);
    }
}
