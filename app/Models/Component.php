<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Component extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $visible = ['id', 'name_en', 'name_se', 'desc_en', 'desc_se', 'code', 'sort_order'];
    protected $appends = ['code_name'];

    public function actions()
    {
        return $this->morphToMany(Action::class, 'actionable');
    }

    public function codeName(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => $attributes['code'] . ' - ' . $attributes['name_' . App::currentLocale()],
        );
    }

    public function organisationStatementsYear(Organisation $organisation, $year)
    {
        $x = Deed::where('organisation_id', $organisation->id)->whereIn('statement_id', $this->statements->pluck('id'))->get()->load('statement')->makeVisible(['statement']);
        $r = $x->filter(function ($item) use ($year) {
            $y = Carbon::parse($item->created_at)->format('Y');
            if ($y == $year) {
                return $item;
            }
        });
        return $r;
    }

    public function organisationPeriod(Organisation $organisation)
    {
        $componentOrganisationPeriod = DB::table('component_organisation')->where('organisation_id', $organisation->id)->where('component_id', $this->id)->first();
        if ($componentOrganisationPeriod) {
            $op = Period::where('id', $componentOrganisationPeriod->period_id)->first();
        } else {
            $op = $this->period;
        }
        return $op;
    }

    public function organisationUserPeriod(Organisation $organisation)
    {
        $componentOrganisationUserPeriod = DB::table('component_organisation')->where('organisation_id', $organisation->id)->where('component_id', $this->id)->where('role', 'user')->first();
        if ($componentOrganisationUserPeriod) {
            $oup = Period::where('id', $componentOrganisationUserPeriod->period_id)->first();
        } else {
            $oup = $this->period;
        }
        return $oup;
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function statements()
    {
        return $this->hasMany(Statement::class);
    }

    public function statementMeanValue(Organisation $organisation, $year)
    {
        $statements = Deed::where('organisation_id', $organisation->id)->whereIn('statement_id', $this->statements->pluck('id'))->get();
        $statements = $statements->filter(function ($item, $value) use ($year) {
            return Carbon::parse($item->created_at)->format('Y') == $year;
        });
        $mean = $statements->avg('value');
        if ($mean == null) {
            $mean = 0;
        }
        return $mean;
    }
}
