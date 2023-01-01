<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;

class Risk extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $visible = ['id', 'title', 'desc', 'probability', 'consequence', 'responsible', 'organisation_id', 'component_id', 'user_id', 'created_at'];
    protected $appends = ['created_at_for_humans'];

    protected static function booted()
    {
        static::deleted(function ($risk) {
            $risk->risk_comments()->delete();
        });
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function createdAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn($value) => Carbon::parse($this->created_at)->format('Y-m-d')
        );
    }

    public function factor()
    {
        return floatval($this->probability) * floatval($this->consequence);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function risk()
    {
        $p = intval($this->probability);
        $c = intval($this->consequence);
        $cl = "info";
        $t = "Undefined";
        $colour = '#222';
        $messages = Lang::get('messages');
        if ((0 < $p && $p < 5 && $c == 1) || ($p == 1 && $c == 2)) {
            $cl = "success";
            $t = $messages['low'];
            $colour = '#28c76f';
        }
        if (($p == 5 && $c == 1) || (1 < $p && $p < 5 && $c == 2) || (0 < $p && $p < 3 && $c == 3)) {
            $cl = "low-med";
            $t = $messages['lowMed'];
            $colour = '#cab707';
        }
        if (($p == 5 && $c == 2) || (2 < $p && $p < 5 && $c == 3) || (0 < $p && $p < 3 && $c == 4) || ($p == 1 && $c == 5)) {
            $cl = "warning";
            $t = $messages['medium'];
            $colour = '#FF9F43';
        }
        if (($p == 5 && $c == 3) || (2 < $p && $p < 5 && $c == 4) || (1 < $p && $p < 4 && $c == 5)) {
            $cl = "med-high";
            $t = $messages['mediumHigh'];
            $colour = '#ff5f43';
        }
        if (($p == 5) & ($c == 4) || (3 < $p && $c == 5)) {
            $cl = "danger";
            $t = $messages['high'];
            $colour = '#EA5455';
        }
        return collect(['class' => $cl, 'text' => $t, 'colour' => $colour]);
    }

    public function risk_comments()
    {
        return $this->hasMany(RiskComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
