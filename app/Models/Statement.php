<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Statement extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $visible = ['id', 'content_en', 'content_se', 'desc_en', 'desc_se', 'k1_en', 'k1_se', 'k2_en', 'k2_se', 'k3_en', 'k3_se', 'k4_en', 'k4_se', 'k5_en', 'k5_se', 'implementation_en', 'implementation_se', 'guide_en', 'guide_se', 'sort_order'];
    protected $appends = ['concat', 'period', 'subcode'];

    public function component()
    {
        return $this->belongsTo(Component::class);
    }
    public function deeds()
    {
        return $this->hasMany(Deed::class);
    }
    public function period(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->component->period
        );
    }
    public function organisationDeed(Organisation $organisation)
    {
        return $this->deeds->where('organisation_id', $organisation->id)->first();
    }
    public function organisationPlan(Organisation $organisation)
    {
        $sp = collect([]);
        //$sp->plan = null;
        $sp->implementation = null;
        $statementOrganisationPlan = DB::table('organisation_statement')->where('organisation_id', $organisation->id)->where('statement_id', $this->id)->first();
        if ($statementOrganisationPlan) {
            //$sp->plan = Plan::where('id', $statementOrganisationPlan->plan_id)->first();
            $sp->implementation = $statementOrganisationPlan->implementation;
        }
        return $sp;
    }
    public function organisationReview(Organisation $organisation)
    {
        return $this->reviews->where('organisation_id', $organisation->id)->first();
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function subcode(): Attribute
    {
        $s = $this->component?->statements?->sortBy('sort_order');
        $i = intval($s?->search($this)) + 1;
        return new Attribute(
            get: fn ($value) => $this->component?->code . '.' . $i
        );
    }
    public function concat(): Attribute
    {
        $r = '';
        $r .= $this->subcode;
        $r .= '-'.$this->{'content_' . App::currentLocale()};
        return new Attribute(
            get: fn ($value) => $r
        );
    }
    public function statement_type()
    {
        return $this->belongsTo(StatementType::class);
    }
}
