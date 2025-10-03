<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function kpicomments()
    {
        return $this->hasMany(Kpicomment::class);
    }

    public function org_kpicomments(Organisation $organisation)
    {
        $k = $this->kpicomments->makeVisible(['created_at_for_humans', 'user']);
        return $k->filter(function ($item) use ($organisation) {
            $u = $item->user;
            if ($u->organisation->id == $organisation->id) {
                return $item;
            }
        });
    }
}
