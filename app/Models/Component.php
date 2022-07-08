<?php

namespace App\Models;

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

    public function codeName(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => $attributes['code'] . ' - ' . $attributes['name_' . App::currentLocale()],
        );
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

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function statements()
    {
        return $this->hasMany(Statement::class);
    }
}
