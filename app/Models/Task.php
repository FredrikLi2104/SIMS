<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $appends = ['start_for_humans', 'end_for_humans', 'title_truncated'];

    public function action()
    {
        return $this->hasOne(Action::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    protected function titleTruncated(): Attribute
    {
        $locale = App::currentLocale();
        return Attribute::make(get: fn($value, $attribute) => Str::limit($attribute["title_$locale"], 40));
    }
}
