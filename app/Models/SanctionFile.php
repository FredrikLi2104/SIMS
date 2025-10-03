<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SanctionFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $appends = ['created_at_for_humans', 'url'];

    protected function createdAtForHumans(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => Carbon::parse($attribute['created_at'])->format('Y-m-d')
        );
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn($values, $attribute) => Storage::url($attribute['path'])
        );
    }
}
