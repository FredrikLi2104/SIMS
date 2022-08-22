<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Organisation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $visible = ['id', 'name', 'number', 'commitment', 'logofile', 'color', 'phone', 'address1', 'address2', 'email', 'website'];
    protected $appends = ['orgcolor', 'logo'];

    public function orgcolor(): Attribute
    {
        $color = $this->color;
        if (!($color)) {
            $color = '00315c';
        }
        return new Attribute(
            get: fn ($value) => $color
        );
    }
    public function components()
    {
        return $this->belongsToMany(Component::class)->withPivot('period_id');
    }

    public function logo(): Attribute
    {
        $logo = $this->logofile;
        if (!($logo)) {
            $logo = '/images/portrait/small/it.png';
        } else {
            $logo = Storage::url($logo);
        }
        return new Attribute(
            get: fn ($value) => $logo
        );
    }
    public function kpis()
    {
        $kpis = Kpi::all();
        foreach ($kpis as $kpi) {
            $kpi->kpicomments = $kpi->org_kpicomments($this);
            $kpi->kpicomment = $kpi->kpicomments->last();
            // chart data
            $t = [];
            $v = [];
            foreach ($kpi->kpicomments as $comment) {
                $t[] = ['x' => Carbon::parse($comment->created_at)->format('Y-m-d'), 'y' => $comment->target, 'user' => $comment->user->name . ' [' . $comment->user->role . ']', 'comment' => $comment->comment];
                $v[] = ['x' => Carbon::parse($comment->created_at)->format('Y-m-d'), 'y' => $comment->value, 'user' => $comment->user->name . ' [' . $comment->user->role . ']', 'comment' => $comment->comment];
            }
            $kpi->targets = $t;
            $kpi->values = $v;
        }
        return $kpis;
    }
    public function sni()
    {
        return $this->belongsTo(Sni::class);
    }

    public function risks()
    {
        return $this->hasMany(Risk::class);
    }

    public function statements()
    {
        return $this->belongsToMany(Statement::class);
    }
}
