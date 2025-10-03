<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dpa extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $visible = ['id', 'pageid', 'title', 'country_id', 'created_at', 'updated_at'];
    protected $appends = ['created_at_for_humans', 'name', 'url'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function createdAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($this->created_at)->format('Y-m-d')
        );
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn ($value) => substr($this->title, 9)
        );
    }

    public function url(): Attribute
    {
        return new Attribute(
            get: fn ($value) => 'https://gdprhub.eu/index.php?title=' . urlencode($this->title)
        );
    }
}
