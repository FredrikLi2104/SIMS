<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskComment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $visible = ['id', 'comment', 'risk_id', 'user_id'];
    protected $appends = ['created_at_for_humans'];

    public function CreatedAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        );
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
