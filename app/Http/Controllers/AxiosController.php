<?php

namespace App\Http\Controllers;

use App\Http\Requests\AxiosOrganisationUpdateRequest;
use App\Http\Requests\OrganisationsComponentsPeriodsUpdateRequest;
use App\Http\Requests\OrganisationsKpicommentsStoreRequest;
use App\Http\Requests\OrganisationsKpicommentStoreRequest;
use App\Http\Requests\OrganisationsPlanAuditorUpdateRequest;
use App\Http\Requests\OrganisationsStatementsActionsUpdateRequest;
use App\Http\Requests\OrganisationsStatementsDeedsUpdateAllRequest;
use App\Http\Requests\OrganisationsStatementsDeedsUpdateRequest;
use App\Http\Requests\OrganisationsStatementsPlansUpdate;
use App\Http\Requests\OrganisationsStatementsPlansUpdateRequest;
use App\Http\Requests\OrganisationsStatementsReviewsUpdateRequest;
use App\Http\Requests\RiskCommentStoreRequest;
use App\Models\Component;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Deed;
use App\Models\Dpa;
use App\Models\Faq;
use App\Models\Kpi;
use App\Models\Kpicomment;
use App\Models\Link;
use App\Models\Organisation;
use App\Models\Period;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Risk;
use App\Models\RiskComment;
use App\Models\Sanction;
use App\Models\Statement;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

class AxiosController extends Controller
{
    //

    /**
     * Return all countries along with dictionary
     *
     * Undocumented function long description
     *
     * @param string $locale app locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function countries($locale)
    {
        $countries = Country::all();
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['countries' => $countries, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    public function currencies()
    {
        $currencies = Currency::all();

        $currencies = $currencies->map(function ($currency) {
            $currency->updated_at_for_humans = $currency->updated_at->format('Y-m-d H:i:s');
            return $currency;
        });

        return ['currencies' => $currencies];
    }

    public function currenciesRatesUpdate($locale)
    {
        $currencies = Currency::all();
        $currencies = $currencies->pluck('symbol')->all();

        $rates = \AmrShawky\Currency::rates()
            ->latest()
            ->symbols($currencies)
            ->round(2)
            ->get();

        foreach ($rates as $symbol => $rate) {
            Currency::where('symbol', $symbol)
                ->update(['value' => $rate]);
        }
    }

    /**
     * Return all dpas along with dictionary
     *
     * Undocumented function long description
     *
     * @param string $locale app locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function dpas($locale)
    {
        $dpas = Dpa::all()->load('country')->makeVisible(['country', 'created_at_for_humans', 'name', 'url']);
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['dpas' => $dpas, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    public function faqs()
    {
        return Faq::all();
    }

    /**
     * Return all kpis along with dictionary
     *
     * Undocumented function long description
     *
     * @param string $locale app locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function kpis($locale)
    {
        $kpis = Kpi::all();
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['kpis' => $kpis, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    public function links()
    {
        return Link::all();
    }

    /**
     * Return the messages dictionaries
     *
     * no further desc
     *
     * @param String $var App locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function messages($locale)
    {
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['messages' => $messages];
        $r = collect($r);
        return $r;
    }

    /**
     * Return the data about the organisation (and its suborganisations) relevant to the act page
     *
     * Undocumented function long description
     *
     * @param String $locale AppLocale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function organisationsAct($locale)
    {
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $orgData = [];
        $organisation = Auth::user()->organisation->load('organisations')->makeVisible(['organisations', 'risks']);
        $organisations = [$organisation];
        foreach ($organisation->organisations->makeVisible(['risks']) as $v) {
            $organisations[] = $v;
        }
        foreach ($organisations as $org) {
            $data = [];
            $years = $org->deedsYears();
            if ($years == []) {
                $years = [Carbon::now()->format('Y')];
            }
            sort($years);
            foreach ($years as $year) {
                $data[$year] = [];
                $components = Component::all();
                foreach ($components as $comp) {
                    $data[$year]['components'][] = $comp->code;
                    $data[$year]['commitment'][] = $org->commitment;
                    $data[$year]['mean'][] = $comp->statementMeanValue($org, $year);
                    $data[$year]['codenames'][] = $comp->codeName;
                    $name = mb_strlen($comp->{'name_' . App::currentLocale()}) > 16 ? mb_substr($comp->{'name_' . App::currentLocale()}, 0, 13) . '...' : $comp->{'name_' . App::currentLocale()};
                    //$name = strlen($comp->{'name_' . App::currentLocale()}) > 16 ? substr($comp->{'name_' . App::currentLocale()}, 0, 13) . '...' : $comp->{'name_' . App::currentLocale()};
                    $data[$year]['table'][] = ['id' => $comp->id, 'code' => $comp->code, 'name' => $name, 'commitment' => $org->commitment, 'mean' => $comp->statementMeanValue($org, $year), 'fullname' => $comp->{'name_' . App::currentLocale()}, 'deeds' => $comp->organisationStatementsYear($org, $year)];
                }
                // Kpis
                $kpis = Kpi::all();
                foreach ($kpis as $kpi) {
                    $orgKpiComments = $kpi->org_kpicomments($org);
                    $orgKpiLast = $orgKpiComments->last();
                    $target = '';
                    $value = '';
                    $comment = '';
                    if ($orgKpiLast) {
                        $target = $orgKpiLast->target;
                        $value = $orgKpiLast->value;
                        $comment = $orgKpiLast->comment;
                    }
                    $data[$year]['kpis'][] = ['id' => $kpi->id, 'name' => $kpi->{'name_' . App::currentLocale()}, 'desc' => $kpi->{'desc_' . App::currentLocale()}, 'target' => $target, 'value' => $value, 'comment' => $comment];
                }
                // Risks
                $scatterRisks = $org->risks->sortBy('created_at');
                $data[$year]['risks']['datasets'] = [];
                foreach ($scatterRisks as $risk) {
                    if (Carbon::parse($risk->created_at)->lessThanOrEqualTo(Carbon::create($year)->lastOfYear())) {
                        $r = $scatterRisks->filter(function ($item) use ($risk, $year) {
                            return ($item->consequence == $risk->consequence && $item->probability == $risk->probability && Carbon::parse($item->created_at)->lessThanOrEqualTo(Carbon::create($year)->lastOfYear()));
                        });
                        $r = 10 * count($r);
                        $data[$year]['risks']['datasets'][] = [
                            'label' => $risk->title,
                            'backgroundColor' => $risk->risk()['colour'],
                            'borderColor' => $risk->risk()['colour'],
                            'data' => [
                                ['x' => $risk->consequence, 'y' => $risk->probability, 'r' => $r],
                            ],
                            'count' => $r / 10,
                            'fs' => 11 + ($r / 10),
                        ];
                    }
                }
                $data[$year]['risks']['legend'] = [
                    ['text' => $messages['low'], 'colour' => '#28c76f'],
                    ['text' => $messages['lowMed'], 'colour' => '#cab707'],
                    ['text' => $messages['medium'], 'colour' => '#FF9F43'],
                    ['text' => $messages['mediumHigh'], 'colour' => '#ff5f43'],
                    ['text' => $messages['high'], 'colour' => '#EA5455'],

                ];
            }
            $orgData[] = ['name' => $org->name, 'data' => $data];
        }
        // Localization
        $r = ['data' => $orgData, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    /**
     * Show organisation do
     *
     * Retrieve all statements and return them as data in relation to organisations deeds on said statements if any
     *
     * @param String $locale
     * @return \Illuminate\Http\Response
     **/
    public function organisationsDo($locale)
    {
        $statements = Statement::all()->load('component')->makeVisible(['component', 'deed', 'implementation', 'plan', 'review', 'subcode']);
        foreach ($statements as $statement) {
            $op = $statement->component->organisationPeriod(Auth::user()->organisation);
            $statement->component->makeVisible(['organisation_period']);
            $statement->component->organisation_period = $op;
            //$statement->plan = null;
            $statement->implementation = null;
            $op = $statement->organisationPlan(Auth::user()->organisation);
            if ($op) {
                //$statement->plan = $op->plan;
                $statement->implementation = $op->implementation;
            }
            $statement->deed = $statement->organisationDeed(Auth::user()->organisation);
            $statement->review = $statement->organisationReview(Auth::user()->organisation);
            // new badge
            if ($statement->deed && $statement->review) {
                $statement->review->makeVisible(['updated_at_for_humans', 'new']);
                $statement->review->new = false;
                if ((Carbon::parse($statement->deed->updated_at) < Carbon::parse($statement->review->updated_at)) && $statement->review->accepted != true) {
                    $statement->review->new = true;
                } else {
                    $statement->review->new = false;
                }
                $statement->deed->makeVisible(['updated_at_for_humans']);
            }
        };
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['statements' => $statements, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    /**
     * update the period of the component in relation to an organisation, e.g: set P1 to period Third Quarter for Organisation x
     */
    public function organisationsComponentsPeriodsUpdate(OrganisationsComponentsPeriodsUpdateRequest $request)
    {
        $data = $request->validated();
        $o = Auth::user()->organisation;
        $role = Auth::user()->role;
        if (!(in_array($role, ['user', 'auditor']))) {
            $role = 'user';
        }
        // find if this component has a period
        $x = DB::table('component_organisation')->where('organisation_id', $o->id)->where('component_id', $data['component_id'])->where('role', $role)->first();
        if ($x) {
            DB::table('component_organisation')->where('organisation_id', $o->id)->where('component_id', $data['component_id'])->where('role', $role)->update(['period_id' => $data['period_id'], 'updated_at' => Carbon::now()]);
        } else {
            $o->components()->attach([$data['component_id'] => ['period_id' => $data['period_id'], 'role' => $role, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]]);
        }
        return response('success', 200);
    }

    /**
     * Store a kpicomment by an organisation user in relation to a kpi
     *
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function organisationsKpicommentsStore($locale, OrganisationsKpicommentsStoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;
        Kpicomment::create($data);
        return response('success', 200);
    }

    /**
     * Return all kpis and inject kpicomments relevant to said organisation along with the dictionary
     *
     * Undocumented function long description
     *
     * @param string $locale App Locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function organisationsKpis($locale)
    {
        $org = Auth::user()->organisation->makeVisible(['kpis']);
        $kpis = $org->kpis();
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['kpis' => $kpis, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    /**
     * Show a kpi in relation to organisation along its kpicomments
     *
     * Undocumented function long description
     *
     * @param string $locale AppLocale
     * @return App\Models\Kpi $kpi the kpi to be returned
     * @throws conditon
     **/
    public function organisationsKpisShow($locale, Kpi $kpi)
    {
        $kpiComments = $kpi->org_kpicomments(Auth::user()->organisation);
        $kpi->makeVisible(['kpicomment', 'kpicomments', 'targets', 'values', 'xaxis']);
        $kpi->kpicomments = $kpiComments;
        $kpi->kpicomment = $kpiComments->last();
        // chart data
        $t = [];
        $v = [];
        $x = [];
        foreach ($kpi->kpicomments as $comment) {
            $t[] = ['x' => Carbon::parse($comment->created_at)->format('Y-m-d'), 'y' => $comment->target, 'user' => $comment->user->name . ' [' . $comment->user->role . ']', 'comment' => $comment->comment];
            $v[] = ['x' => Carbon::parse($comment->created_at)->format('Y-m-d'), 'y' => $comment->value, 'user' => $comment->user->name . ' [' . $comment->user->role . ']', 'comment' => $comment->comment];
            $x[] = Carbon::parse($comment->created_at)->format('Y-m-d');
        }
        $kpi->targets = $t;
        $kpi->values = $v;
        $kpi->xaxis = $x;
        return $kpi;
    }

    public function organisationsPlan($locale)
    {
        $components = Component::all()->load('period')->makeVisible(['code_name', 'period', 'periods']);
        foreach ($components as $component) {
            $op = $component->organisationUserPeriod(Auth::user()->organisation);
            $component->periods = Period::all()->sortBy('sort_order');
            foreach ($component->periods as $period) {
                if ($period->id == $op->id) {
                    $period->selected = true;
                } else {
                    $period->selected = false;
                }
            }
        };
        // statements periods for this organisation would be the same for their component organisationPeriod
        $statements = Statement::all()->load('component')->makeVisible(['component', 'implementation', 'responsibility', 'period', 'subcode']);
        foreach ($statements as $statement) {
            // calculate component periods for this organisation
            $oup = $statement->component->organisationUserPeriod(Auth::user()->organisation);
            $statement->component->makeVisible(['organisation_period']);
            $statement->component->organisation_period = $oup;
            $sp = $statement->organisationPlan(Auth::user()->organisation);
            $statement->implementation = $sp->implementation;
            $statement->responsibility = $sp->responsibility;
            // calculate statement plan for this organisation [cancelled]
            /*
            $statement->plans = Plan::all();
            foreach ($statement->plans as $plan) {
                $plan->selected = false;
                if ($sp) {
                    if ($sp->plan?->id == $plan->id) {
                        $plan->selected = true;
                    }
                }
            }*/
        }
        // Organisation data for report
        $organisation = Auth::user()->organisation->makeVisible(['orgcolor', 'logo']);
        // Report Chart
        $quarterchart = [];
        $quarters = Period::whereIn('id', [1, 2, 3, 4])->get();
        foreach ($quarters as $quarter) {
            $quarterchart['labels'][] = $quarter->{'name_' . $locale};
            // loop all components
            $count = 0;
            $quarterchartcomponents = [];
            foreach ($components as $component) {
                if ($component->organisationuserPeriod($organisation)->id == $quarter->id) {
                    $count += 1;
                    $quarterchartcomponents[] = $component->code_name;
                }
            }
            $quarterchart['data'][] = $count;
            $quarterchart['components'][] = $quarterchartcomponents;
            /*
            $comps = Component::where('period_id', $quarter->id)->all();
            // if any of those components period id has been planned differently in component org by user, remove them from this quarter
            $quarterchart['data'][] = DB::table('component_organisation')->where('period_id', $quarter->id)->get()->count();
            */
        }
        $cs = Component::all()->sortBy('sort_order')->makeVisible(['code_name']);
        $quarterchart['componentsfinal'] = [];
        foreach ($cs as $c) {
            $quarterchart['componentsfinal'][] = ['codename' => $c->code_name, 'desc' => $c->{'desc_' . $locale}, 'implementation' => '[Placeholder for Implementation]?'];
        }
        // Localization
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['components' => $components, 'organisation' => $organisation, 'statements' => $statements, 'quarterchart' => $quarterchart, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    /**
     * Return a collection of statements and relations to the auditor organisation along with their guide and plan (review) type
     *
     * Undocumented function long description
     *
     * @param string $locale AppLocale
     * @return Illuminate\Support\Collection the statements
     **/
    public function organisationsPlanAuditor($locale)
    {
        $statements = Statement::all()->makeVisible(['concat', 'guide', 'plans']);
        $plans = Plan::all()->sortBy('sort_order');
        $statementPlans = [];
        foreach ($statements as $statement) {
            $statementReviewPlan = $statement->reviewPlan();
            if ($statementReviewPlan) {
                $org = Auth::user()->organisation;
                $usersIds = $org->users->pluck('id');
                $r = DB::table('auditor_statement')->whereIn('user_id', $usersIds)->where('statement_id', $statement->id)->get()->first();
                $statement->guide = $r->guide;
            } else {
                $statement->guide = '';
            }
            foreach ($plans as $plan) {
                $selected = false;
                if ($statementReviewPlan) {
                    if ($statementReviewPlan->id == $plan->id) {
                        $selected = true;
                    }
                }
                $statementPlans[] = ['plan' => $plan, 'selected' => $selected];
            }
            $statement->plans = $statementPlans;
            $statementPlans = [];
        }
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['statements' => $statements, 'messages' => $messages];
        return $r;
    }

    /**
     * Update the plan status for a statement by an auditor
     *
     * Undocumented function long description
     *
     * @param App\Http\Requests\OrganisationsPlanAuditorUpdateRequest $request load
     * @param string $locale AppLocale
     * @return \Illuminate\Http\Response
     **/
    public function organisationsPlanAuditorUpdate(OrganisationsPlanAuditorUpdateRequest $request, $locale)
    {
        // as exists?
        try {
            $as = DB::table('auditor_statement')->where('statement_id', $request->statement_id)->get()->first();
            if ($as) {
                DB::table('auditor_statement')->where('id', $as->id)->update(['plan_id' => $request->plan_id, 'user_id' => Auth::user()->id, 'guide' => $request->guide, 'updated_at' => Carbon::now()]);
            } else {
                DB::table('auditor_statement')->insert(['statement_id' => $request->statement_id, 'plan_id' => $request->plan_id, 'user_id' => Auth::user()->id, 'guide' => $request->guide, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            abort(500, $th->getMessage());
        }
        return response('success', 200);
    }

    /**
     * Get organisation review statements
     *
     * Get organisation statements, with their deeds and return them for auditor to review them
     *
     * @param String $locale
     * @return \Illuminate\Http\Response
     **/
    public function organisationsReview($locale)
    {
        $statements = Statement::all()->load('component')->makeVisible(['component', 'deed', 'implementation', 'guide', 'plan', 'review', 'subcode']);
        $plans = Plan::all()->sortBy('sort_order');
        foreach ($statements as $statement) {
            $op = $statement->component->organisationPeriod(Auth::user()->organisation);
            $statement->component->makeVisible(['organisation_period']);
            $statement->component->organisation_period = $op;
            //$statement->plan = null;
            $statement->implementation = null;
            $op = $statement->organisationPlan(Auth::user()->organisation);
            if ($op) {
                //$statement->plan = $op->plan;
                $statement->implementation = $op->implementation;
            }
            $statement->deed = $statement->organisationDeed(Auth::user()->organisation);
            $statement->review = $statement->organisationReview(Auth::user()->organisation);
            $statementReviewPlan = $statement->reviewPlan();
            if ($statementReviewPlan) {
                $org = Auth::user()->organisation;
                $usersIds = $org->users->pluck('id');
                $r = DB::table('auditor_statement')->whereIn('user_id', $usersIds)->where('statement_id', $statement->id)->get()->first();
                $statement->guide = $r->guide;
            } else {
                $statement->guide = '';
            }
            $statement->plan = ['name_en' => '', 'name_se' => ''];
            foreach ($plans as $plan) {
                if ($statementReviewPlan) {
                    if ($statementReviewPlan->id == $plan->id) {
                        $statement->plan = $plan;
                    }
                }
            }
        };
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['statements' => $statements, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    /**
     * Return a collection of all organisation risks along the messages dictionaries
     *
     * no further desc is neccessary
     *
     * @param String $locale AppLocale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function organisationsRisksIndex($locale)
    {
        $org = Auth::user()->organisation->makevisible(['risks']);
        $risks = $org->risks->load(['organisation', 'risk_comments', 'component', 'user'])->makeVisible(['created_at_for_humans', 'factor', 'organisation', 'risk', 'risk_comments', 'component', 'user']);
        foreach ($risks as $risk) {
            $risk->risk = $risk->risk();
            $risk->factor = $risk->factor();
            if ($risk->component) {
                $risk->component->makeVisible(['code_name']);
            }
            foreach ($risk->risk_comments as $risk_comment) {
                $risk_comment->load('user')->makeVisible(['created_at_for_humans', 'user']);
            }
            $risk->makeVisible(['risk_comments_sorted']);
            $risk->risk_comments_sorted = collect($risk->risk_comments)->sortByDesc('id')->values()->all();
        }
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $now = Carbon::now()->format('Y-m-d H:i:s T');
        $colours = ['success' => '#28c76f', 'lowMed' => '#cab707', 'warning' => '#FF9F43', 'medHigh' => '#ff5f43', 'danger' => '#EA5455'];
        $series = [count($risks->filter(function ($item) {
            return $item->risk['class'] == 'success';
        })->all()), count($risks->filter(function ($item) {
            return $item->risk['class'] == 'low-med';
        })->all()), count($risks->filter(function ($item) {
            return $item->risk['class'] == 'warning';
        })->all()), count($risks->filter(function ($item) {
            return $item->risk['class'] == 'med-high';
        })->all()), count($risks->filter(function ($item) {
            return $item->risk['class'] == 'danger';
        })->all())];
        // Scatter Data New with Range
        /*
        $dataSets = [];
        foreach ($risks as $risk) {
            $dataSets[] = ['label' => $risk->title, 'backgroundColor' => $risk->risk['colour'], 'borderColor' => $risk->risk['colour']];
        };
        */
        // Scatter Data New with Range End
        //Scatter Data
        //Carbon::create()->startOfMonth()->month($period->start)->locale(__('messages.localeCarbon'))->getTranslatedMonthName('M');
        $dataSets = [];
        $rangeDates = [$messages['rangeAllTime']];
        $risks = $risks->sortBy('created_at');

        $risks->each(function ($risk) use (&$rangeDates) {
            if (!(in_array(Carbon::parse($risk->created_at)->locale(__('messages.localeCarbon'))->isoFormat('Y MMMM'), $rangeDates))) {
                $rangeDates[] = Carbon::parse($risk->created_at)->locale(__('messages.localeCarbon'))->isoFormat('Y MMMM');
            }
        });

        foreach ($rangeDates as $key => $date) {
            if ($key === 0) {
                $risksUpToMonth = $risks;
            } else {
                $risksUpToMonth = $risks->where('created_at', '<=', Carbon::parse($date)->endOfMonth());
            }

            foreach ($risksUpToMonth as $scatterRisk) {
                $dataSets[] = [
                    'label' => $scatterRisk->title,
                    'backgroundColor' => $scatterRisk->risk['colour'],
                    'borderColor' => $scatterRisk->risk['colour'],
                    'data' => [[
                        'x' => $scatterRisk->consequence,
                        'y' => $scatterRisk->probability,
                        'r' => 10 * count($risks->filter(function ($item) use ($scatterRisk) {
                                return ($item->consequence == $scatterRisk->consequence && $item->probability == $scatterRisk->probability);
                            })->all())
                    ]],
                    'count' => count($risks->filter(function ($item) use ($scatterRisk) {
                        return ($item->consequence == $scatterRisk->consequence && $item->probability == $scatterRisk->probability);
                    })->all()), 'date' => $date];
            }
        };

        //Scatter Date End
        // history data new
        // x axis
        $historyCategories = [];
        // y axis
        $historyLow = [];
        $historyLowMed = [];
        $historyMed = [];
        $historyMedHigh = [];
        $historyHigh = [];
        for ($i = 0; $i < 13; $i++) {
            $month = Carbon::now()->addMonths($i - 12);
            $monthlyRisks = $risks->filter(function ($item) use ($month) {
                return Carbon::parse($item->created_at) <= $month->endOfMonth();
            });
            $historyLow[] = count($monthlyRisks->filter(function ($item) {
                return $item->risk['class'] == 'success';
            })->all());
            $historyLowMed[] = count($monthlyRisks->filter(function ($item) {
                return $item->risk['class'] == 'low-med';
            })->all());
            $historyMed[] = count($monthlyRisks->filter(function ($item) {
                return $item->risk['class'] == 'warning';
            })->all());
            $historyMedHigh[] = count($monthlyRisks->filter(function ($item) {
                return $item->risk['class'] == 'med-high';
            })->all());
            $historyHigh[] = count($monthlyRisks->filter(function ($item) {
                return $item->risk['class'] == 'danger';
            })->all());
            $year = $month->format('y');
            $day = $month->locale(__('messages.localeCarbon'))->getTranslatedShortMonthName('M');
            $historyCategories[] = $day . '-' . $year;
        }
        $history = ['xaxis' => $historyCategories, 'yaxis' => ['historyLow' => $historyLow, 'historyLowMed' => $historyLowMed, 'historyMed' => $historyMed, 'historyMedHigh' => $historyMedHigh, 'historyHigh' => $historyHigh]];
        // history data new end
        // history data old
        /*
        $cats = [];
        $avgs = [];
        for ($i = 0; $i < 13; $i++) {
            $r = Carbon::now()->addMonths($i - 12);
            $intersectedRisks = $risks->filter(function ($item) use ($r) {
                return ($r->startOfMonth() <= Carbon::parse($item->created_at) && Carbon::parse($item->created_at) <= $r->endOfMonth());
            });
            $a = 0;
            $a += round(floatval($intersectedRisks->average('factor')), 2);
            $y = $r->format('y');
            $d = $r->locale(__('messages.localeCarbon'))->getTranslatedShortMonthName('M');
            $cat = $d . '-' . $y;
            $cats[] = $cat;
            $avgs[] = $a;
        };
        */
        // history data old end
        $r = ['messages' => $messages, 'risks' => $risks, 'now' => $now, 'colours' => $colours, 'series' => $series, 'dataSets' => $dataSets, 'history' => $history, 'rangeDates' => $rangeDates];
        $r = collect($r);
        return $r;
    }

    /**
     * Update or create a statement action
     *
     * Update or create a statement action in relation to an organisation, e.g: set statement x action for organization y to be "value 2, comment something"
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function organisationsStatementsDeedsUpdate(OrganisationsStatementsDeedsUpdateRequest $request)
    {
        $data = $request->validated();
        $o = Auth::user()->organisation;
        // find if this statement already has an organisation deed
        $d = Deed::where('organisation_id', $o->id)->where('statement_id', $data['statement_id'])->first();
        if ($d) {
            $d->update(['user_id' => Auth::user()->id, 'value' => $data['value'], 'comment' => $data['comment']]);
        } else {
            Deed::create(['organisation_id' => $o->id, 'statement_id' => $data['statement_id'], 'user_id' => Auth::user()->id, 'value' => $data['value'], 'comment' => $data['comment']]);
        }
        return response('success', 200);
    }

    /**
     * Update or create an array of statement deeds
     *
     * Undocumented function long description
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     **/
    public function organisationsStatementsDeedsUpdateAll(OrganisationsStatementsDeedsUpdateAllRequest $request)
    {
        $data = $request->validated();
        $organisation = Auth::user()->organisation;
        try {
            DB::transaction(function () use ($data, $organisation) {
                foreach ($data['statements'] as $statement) {
                    // exists?
                    $deed = Deed::where('organisation_id', $organisation->id)->where('statement_id', $statement['id'])->first();
                    if ($deed) {
                        // update
                        $deed->update(['user_id' => Auth::user()->id, 'value' => $statement['value'], 'comment' => $statement['comment']]);
                    } else {
                        Deed::create(['organisation_id' => $organisation->id, 'statement_id' => $statement['id'], 'user_id' => Auth::user()->id, 'value' => $statement['value'], 'comment' => $statement['comment']]);
                    }
                }
            });
            return response('success', 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the plan of the statement in relation to an organisation, e.g: set statement x plan for organisation x to be "Inspection"
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function organisationsStatementsPlansUpdate(OrganisationsStatementsPlansUpdateRequest $request)
    {
        //
        $data = $request->validated();
        $o = Auth::user()->organisation;
        // find if this statement already has an entry
        $x = DB::table('organisation_statement')->where('organisation_id', $o->id)->where('statement_id', $data['statement_id'])->first();
        $responsibility = null;
        if (isset($data['responsibility'])) {
            $responsibility = $data['responsibility'];
        }
        if ($x) {
            DB::table('organisation_statement')->where('organisation_id', $o->id)->where('statement_id', $data['statement_id'])->update(['implementation' => $data['implementation'], 'responsibility' => $responsibility, 'updated_at' => Carbon::now()]);
        } else {
            $o->statements()->attach([$data['statement_id'] => ['implementation' => $data['implementation'], 'responsibility' => $responsibility, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]]);
        }
        return response('success', 200);
    }

    /**
     * Update the review of the statement in relation to an organisation
     *
     * e.g: set statement x review or organisation y and deed z to be "you did this wrong"
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function organisationsStatementsReviewsUpdate(OrganisationsStatementsReviewsUpdateRequest $request)
    {
        $data = $request->validated();
        $o = Auth::user()->organisation;
        // find if this statement has a a review
        $r = Review::where('organisation_id', $o->id)->where('statement_id', $data['statement_id'])->first();
        if ($r) {
            // update
            $r->update(['user_id' => Auth::user()->id, 'accepted' => $data['accepted'], 'review' => $data['review']]);
        } else {
            // create
            Review::create(['organisation_id' => $o->id, 'statement_id' => $data['statement_id'], 'user_id' => Auth::user()->id, 'accepted' => $data['accepted'], 'review' => $data['review']]);
        }
        return response('success', 200);
    }

    /**
     * Update organisation details
     *
     * Undocumented function long description
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function organisationsUpdate(AxiosOrganisationUpdateRequest $request)
    {
        $data = $request->validated();
        $organisation = Auth::user()->organisation;
        $update = [];
        // logo uploaded?
        if (isset($data['logo'])) {
            $update['logofile'] = Storage::putFile('public/organisations/logos', $data['logo']);
        }
        // color uploaded?
        if (isset($data['color'])) {
            $update['color'] = $data['color'];
        }
        // has phone?
        if (isset($data['phone'])) {
            $update['phone'] = $data['phone'];
        }
        if (isset($data['address1'])) {
            $update['address1'] = $data['address1'];
        }
        if (isset($data['address2'])) {
            $update['address2'] = $data['address2'];
        }
        if (isset($data['email'])) {
            $update['email'] = $data['email'];
        }
        if (isset($data['website'])) {
            $update['website'] = $data['website'];
        }
        $organisation->update($update);
        return $organisation->makeVisible(['orgcolor', 'logo']);
    }

    /**
     * Store a risk comment
     *
     * N/A
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function riskCommentsStore(RiskCommentStoreRequest $request)
    {
        $data = $request->validated();
        $risk = Risk::where('id', $data['risk_id'])->first();
        // verify this risk belongs to the user organisation
        if ($risk->organisation->id == Auth::user()->organisation->id) {
            $data['user_id'] = Auth::user()->id;
            RiskComment::create($data);
            return response('success', 200);
        } else {
            abort(403, 'This risk does not belong to your organisation, you can not comment on it.');
        }
    }


    /**
     * Return a risk along with messages dictionaries
     *
     * no further desc is required
     *
     * @param string $locale App locale
     * @param App\Models\Risk $risk the risk to be returned
     * @return Illuminate\Database\Eloquent\Collection
     **@throws Illuminate\Http\Response when the user is not authorized ot access said risk
     */
    public function risksShow($locale, Risk $risk)
    {
        // abort 403 if the risk requested does not belong to the user organisation
        if ($risk->organisation_id != Auth::user()->organisation->id) {
            return response('This risk does not belong to your organisation!', 403);
        } else {
            App::setlocale($locale);
            $messages = Lang::get('messages');
            $risk->load('risk_comments')->makeVisible('risk_comments', 'risk_comments_sorted');
            foreach ($risk->risk_comments as $risk_comment) {
                $risk_comment->load('user')->makeVisible(['created_at_for_humans', 'user']);
            }
            $risk->risk_comments_sorted = collect($risk->risk_comments)->sortByDesc('id')->values()->all();
            $r = ['messages' => $messages, 'risk' => $risk];
            $r = collect($r);
            return $r;
        }
    }

    /**
     * Return a sanction
     *
     * Undocumented function long description
     *
     * @param string $locale app locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function sanction($locale, Sanction $sanction)
    {
        $sanction = $sanction->makeVisible(['desc_en', 'desc_se']);
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['sanction' => $sanction, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }

    /**
     * Return all sanctions along with dictionary
     *
     * Undocumented function long description
     *
     * @param string $locale app locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function sanctions($locale, Request $request)
    {
        $start = $request->get('start');
        $length = $request->get('length');
        $searchVal = $request->get('search')['value'];
        $orderByColIndex = $request->get('order')[0]['column'];
        $orderByColName = $orderBy ?? $request->get('columns')[$orderByColIndex]['data'];
        $orderByColName = str_replace('_for_humans', '', $orderByColName);
        $orderDir = $request->get('order')[0]['dir'];
        $filterByDpa = $request->get('filters')['dpa_id'];
        $filterBySni = $request->get('filters')['sni_id'];
        $filterByStatement = $request->get('filters')['statement_id'];
        $filterByType = $request->get('filters')['type_id'];
        $filterByUser = $request->get('filters')['user_id'];

        $sanctions = Sanction::select('sanctions.*')
            ->when($searchVal, function ($query, $searchVal) {
                $query->where(function ($query) use ($searchVal) {
                    $query->where('sanctions.id', 'like', "%$searchVal%")
                        ->orWhereDate('sanctions.created_at', 'like', "%$searchVal%")
                        ->orWhereRelation('dpa', 'title', 'like', "Category:%$searchVal%")
                        ->orWhereDate('sanctions.decided_at', 'like', "%$searchVal%")
                        ->orWhere('sanctions.fine', 'like', "%$searchVal%")
                        ->orWhere('sanctions.title', 'like', "%$searchVal%")
                        ->orWhereRaw('LOWER(sanctions.desc_en) LIKE ?', "{\"ops\":[{\"insert\":\"%" . strtolower($searchVal) . "%")
                        ->orWhereRaw('LOWER(sanctions.desc_se) LIKE ?', "{\"ops\":[{\"insert\":\"%" . strtolower($searchVal) . "%");
                });
            })->when($filterByDpa, function ($query, $filterByDpa) {
                $query->where('dpa_id', $filterByDpa);
            })->when($filterBySni, function ($query, $filterBySni) {
                if ($filterBySni == -1) {
                    $query->whereNull('sni_id');
                } else {
                    $query->where('sni_id', $filterBySni);
                }
            })->when($filterByStatement, function ($query, $filterByStatement) {
                if ($filterByStatement == -1) {
                    $query->whereDoesntHave('statements');
                } else {
                    $query->join('sanction_statement', function ($join) use ($filterByStatement) {
                        $join->on('sanctions.id', '=', 'sanction_statement.sanction_id')
                            ->where('sanction_statement.statement_id', $filterByStatement);
                    });
                }
            })->when($filterByType, function ($query, $filterByType) {
                if ($filterByType == -1) {
                    $query->whereNull('type_id');
                } else {
                    $query->where('type_id', $filterByType);
                }
            })->when($filterByUser, function ($query, $filterByUser) {
                $query->where('user_id', $filterByUser);
            })->when($orderByColName, function ($query, $orderByColName) use ($orderDir) {
                if ($orderByColName == 'dpa') {
                    $query->join('dpas', 'sanctions.dpa_id', '=', 'dpas.id')
                        ->orderBy('dpas.title', $orderDir);
                } else {
                    $query->orderBy($orderByColName, $orderDir);
                }
            })->skip($start)
            ->take($length)
            ->get();

        $sanctionsTotal = Sanction::count();
        $sanctionsFiltered = Sanction::when($searchVal, function ($query, $searchVal) {
            $query->where(function ($query) use ($searchVal) {
                $query->where('sanctions.id', 'like', "%$searchVal%")
                    ->orWhereDate('sanctions.created_at', 'like', "%$searchVal%")
                    ->orWhereRelation('dpa', 'title', 'like', "Category:%$searchVal%")
                    ->orWhereDate('sanctions.decided_at', 'like', "%$searchVal%")
                    ->orWhere('sanctions.fine', 'like', "%$searchVal%")
                    ->orWhere('sanctions.title', 'like', "%$searchVal%")
                    ->orWhereRaw('LOWER(sanctions.desc_en) LIKE ?', "{\"ops\":[{\"insert\":\"%" . strtolower($searchVal) . "%")
                    ->orWhereRaw('LOWER(sanctions.desc_se) LIKE ?', "{\"ops\":[{\"insert\":\"%" . strtolower($searchVal) . "%");
            });
        })->when($filterByDpa, function ($query, $filterByDpa) {
            $query->where('dpa_id', $filterByDpa);
        })->when($filterBySni, function ($query, $filterBySni) {
            if ($filterBySni == -1) {
                $query->whereNull('sni_id');
            } else {
                $query->where('sni_id', $filterBySni);
            }
        })->when($filterByStatement, function ($query, $filterByStatement) {
            if ($filterByStatement == -1) {
                $query->whereDoesntHave('statements');
            } else {
                $query->join('sanction_statement', function ($join) use ($filterByStatement) {
                    $join->on('sanctions.id', '=', 'sanction_statement.sanction_id')
                        ->where('sanction_statement.statement_id', $filterByStatement);
                });
            }
        })->when($filterByType, function ($query, $filterByType) {
            if ($filterByType == -1) {
                $query->whereNull('type_id');
            } else {
                $query->where('type_id', $filterByType);
            }
        })->when($filterByUser, function ($query, $filterByUser) {
            $query->where('user_id', $filterByUser);
        })->count();

        $sanctions->load(['articles', 'dpa', 'user'])->makeVisible(['articles', 'articlesSorted', 'created_at_for_humans', 'started_at_for_humans', 'decided_at_for_humans', 'published_at_for_humans', 'dpa', 'url', 'etid', 'updated_at_for_humans', 'user', 'party']);

        foreach ($sanctions as $sanction) {
            $articles = $sanction->articles;
            $sanction->articlesSorted = $articles->sortBy('title')->values();
            $sanction->dpa->load('country')->makeVisible(['country', 'name']);

            if ($sanction->currency?->symbol && $sanction->currency->symbol != 'EUR') {
                $currency = Currency::where('symbol', $sanction->currency->symbol)->first();

                if ($currency) {
                    try {
                        $sanction->fine = $sanction->fine / $currency->value;
                    } catch (\Throwable $th) {

                    }
                }
            }
        }

        App::setlocale($locale);
        $r = ['sanctions' => $sanctions, 'draw' => $request->get('draw'), 'recordsTotal' => $sanctionsTotal, 'recordsFiltered' => $sanctionsFiltered];
        $r = collect($r);
        return $r;
    }

    /**
     * Return a specific sanction model
     *
     * Undocumented function long description
     *
     * @param string $locale app locale
     * @param \App\Models\Sanction $sanction
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function sanctionsShow($locale, Sanction $sanction)
    {
        $sanction->load(['articles', 'currency', 'dpa'])->makeVisible(['articles', 'articlesSorted', 'currency', 'created_at_for_humans', 'dpa', 'started_at_for_humans', 'decided_at_for_humans', 'published_at_for_humans', 'url']);
        $articles = $sanction->articles;
        $sanction->articlesSorted = $articles->sortBy('title')->values();
        $sanction->dpa->load('country')->makeVisible(['country', 'name']);
        return $sanction;
    }

    public function sanctionsStats($locale, $by)
    {
        return match ($by) {
            'component' => $this->sanctionsByCmponent($locale),
            'statement' => $this->sanctionsByStatement(),
            'chronological' => $this->sanctionsImposedOverTime(),
            'country' => $this->sanctionsByCountry(),
            'sector' => $this->sanctionsBySector($locale),
            'individual' => $this->sanctionsIndividual($locale),
            default => [],
        };
    }

    /**
     * Return all sanctions for the act route in ajax datatable format
     *
     * Undocumented function long description
     *
     * @param string $locale app locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function sanctionsTable($locale, Request $request)
    {
        if ($request->search['value'] == '' || $request->search['value'] == null) {
            $sanctions = Sanction::all()->sortByDesc('id');
        } else {
            $needle = $request->search['value'];
            // spider search
            $sanctions = Sanction::where(function ($query) use ($needle) {
                $query->where('title', 'like', '%' . $needle . '%')->orWhere('started_at', 'like', '%' . $needle . '%')->orWhere('decided_at', 'like', '%' . $needle . '%')->orWhere('published_at', 'like', '%' . $needle . '%')->orWhere('fine', 'like', '%' . $needle . '%');
            })->get();
            $sanctions = $sanctions->sortByDesc('id');
        }
        $draw = $request->draw;
        $recordsTotal = $sanctions->count();
        $recordsFiltered = $sanctions->count();
        $data = $sanctions->chunk($request->length);
        if (is_int($request->start / $request->length)) {
            $data = $data[$request->start / $request->length];
        } else {
            $data = $data[count($data) - 1];
        }
        $data = $data->load(['articles', 'currency', 'dpa'])->makeVisible(['articles', 'articlesSorted', 'currency', 'created_at_for_humans', 'decided_at_for_humans', 'dpa', 'url'])->take($request->length);
        foreach ($data as $sanction) {
            $articles = $sanction->articles;
            $sanction->articlesSorted = $articles->sortBy('title')->values();
            $sanction->dpa->load('country')->makeVisible(['country', 'name']);
        }
        $data = $data->values();
        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'index' => $request->start,
        ];
    }

    public function tags($locale)
    {
        App::setlocale($locale);
        $tags = Tag::all();
        $messages = Lang::get('messages');

        return collect(['tags' => $tags, 'messages' => $messages]);
    }

    private function sanctionsByCmponent($locale)
    {
        $sanctions = Sanction::select("components.name_$locale", DB::raw('SUM(fine / currencies.value) AS sum'))
            ->join('sanction_statement', 'sanctions.id', '=', 'sanction_statement.sanction_id')
            ->join('statements', 'sanction_statement.statement_id', '=', 'statements.id')
            ->join('components', 'statements.component_id', '=', 'components.id')
            ->join('currencies', 'sanctions.currency_id', '=', 'currencies.id')
            ->groupBy("components.name_$locale")
            ->orderBy('sum', 'desc')
            ->get();

        $data = $sanctions->pluck('sum');
        $data = $data->map(function ($fine) {
            return round($fine);
        });

        $sum = ['categories' => $sanctions->pluck("name_$locale"), 'data' => $data];

        $sanctions = Sanction::select("components.name_$locale", DB::raw('COUNT(1) AS count'))
            ->join('sanction_statement', 'sanctions.id', '=', 'sanction_statement.sanction_id')
            ->join('statements', 'sanction_statement.statement_id', '=', 'statements.id')
            ->join('components', 'statements.component_id', '=', 'components.id')
            ->groupBy("components.name_$locale")
            ->orderBy('count', 'desc')
            ->get();

        $count = ['categories' => $sanctions->pluck("name_$locale"), 'data' => $sanctions->pluck('count')];

        return ['sum' => $sum, 'count' => $count];
    }

    private function sanctionsByStatement()
    {
        $sanctions = Sanction::select(DB::raw("CONCAT(components.code, '.', statements.code) AS code, SUM(fine / currencies.value) AS sum"))
            ->join('sanction_statement', 'sanctions.id', '=', 'sanction_statement.sanction_id')
            ->join('statements', 'sanction_statement.statement_id', '=', 'statements.id')
            ->join('components', 'statements.component_id', '=', 'components.id')
            ->join('currencies', 'sanctions.currency_id', '=', 'currencies.id')
            ->groupBy(DB::raw("CONCAT(components.code, '.', statements.code)"))
            ->orderBy('sum', 'desc')
            ->get();

        $data = $sanctions->pluck('sum');
        $data = $data->map(function ($fine) {
            return round($fine);
        });

        $sum = ['categories' => $sanctions->pluck('code'), 'data' => $data];

        $sanctions = Sanction::select(DB::raw("CONCAT(components.code, '.', statements.code) AS code, COUNT(1) AS count"))
            ->join('sanction_statement', 'sanctions.id', '=', 'sanction_statement.sanction_id')
            ->join('statements', 'sanction_statement.statement_id', '=', 'statements.id')
            ->join('components', 'statements.component_id', '=', 'components.id')
            ->groupBy(DB::raw("CONCAT(components.code, '.', statements.code)"))
            ->orderBy('count', 'desc')
            ->get();

        $count = ['categories' => $sanctions->pluck("code"), 'data' => $sanctions->pluck('count')];

        return ['sum' => $sum, 'count' => $count];
    }

    private function sanctionsImposedOverTime()
    {
        $sanctions = Sanction::select(DB::raw("YEAR(sanctions.decided_at) AS year, MONTH(sanctions.decided_at) AS month, DATE_FORMAT(sanctions.decided_at, '%b %Y') AS month_year, SUM(fine / currencies.value) AS sum, COUNT(1) AS count"))
            ->join('currencies', 'sanctions.currency_id', '=', 'currencies.id')
            ->whereNotNull('decided_at')
            ->groupBy('year', 'month', 'month_year')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $sanctions = $sanctions->map(function ($sanction) {
            $sanction->sum = round($sanction->sum);

            return $sanction;
        });

        $sum = ['categories' => $sanctions->pluck('month_year'), 'data' => $sanctions->pluck('sum')];
        $count = ['categories' => $sanctions->pluck('month_year'), 'data' => $sanctions->pluck('count')];

        return ['sum' => $sum, 'count' => $count];
    }

    private function sanctionsByCountry()
    {
        $sanctions = Sanction::select('countries.name', DB::raw('SUM(fine / currencies.value) AS sum'))
            ->join('dpas', 'sanctions.dpa_id', '=', 'dpas.id')
            ->join('countries', 'dpas.country_id', '=', 'countries.id')
            ->join('currencies', 'sanctions.currency_id', '=', 'currencies.id')
            ->groupBy('countries.name')
            ->orderBy('sum', 'desc')
            ->take(10)
            ->get();

        $data = $sanctions->pluck('sum');
        $data = $data->map(function ($fine) {
            return round($fine);
        });

        $sum = ['categories' => $sanctions->pluck('name'), 'data' => $data];

        $sanctions = Sanction::select('countries.name', DB::raw('COUNT(1) AS count'))
            ->join('dpas', 'sanctions.dpa_id', '=', 'dpas.id')
            ->join('countries', 'dpas.country_id', '=', 'countries.id')
            ->groupBy('countries.name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        $count = ['categories' => $sanctions->pluck('name'), 'data' => $sanctions->pluck('count')];

        return ['sum' => $sum, 'count' => $count];
    }

    private function sanctionsBySector($locale)
    {
        $sanctions = Sanction::select("snis.desc_$locale", DB::raw('SUM(fine / currencies.value) AS sum'))
            ->join('snis', 'sanctions.sni_id', '=', 'snis.id')
            ->join('currencies', 'sanctions.currency_id', '=', 'currencies.id')
            ->groupBy("snis.desc_$locale")
            ->orderBy('sum', 'desc')
            ->get();

        $data = $sanctions->pluck('sum');
        $data = $data->map(function ($fine) {
            return round($fine);
        });

        $sum = ['categories' => $sanctions->pluck("desc_$locale"), 'data' => $data];

        $sanctions = Sanction::select("snis.desc_$locale", DB::raw('COUNT(1) AS count'))
            ->join('snis', 'sanctions.sni_id', '=', 'snis.id')
            ->groupBy("snis.desc_$locale")
            ->orderBy('count', 'desc')
            ->get();

        $count = ['categories' => $sanctions->pluck("desc_$locale"), 'data' => $sanctions->pluck('count')];

        return ['sum' => $sum, 'count' => $count];
    }

    private function sanctionsIndividual($locale)
    {
        $sanctions = Sanction::select('sanctions.title', 'sanctions.decided_at', "snis.desc_$locale AS sector", 'countries.name AS country', "types.text_$locale AS type", DB::raw('SUM(fine / currencies.value) AS sum'))
            ->join('snis', 'sanctions.sni_id', '=', 'snis.id')
            ->join('dpas', 'sanctions.dpa_id', '=', 'dpas.id')
            ->join('countries', 'dpas.country_id', '=', 'countries.id')
            ->join('types', 'sanctions.type_id', '=', 'types.id')
            ->join('currencies', 'sanctions.currency_id', '=', 'currencies.id')
            ->groupBy('sanctions.title', 'sanctions.decided_at', "snis.desc_$locale", 'countries.name', "types.text_$locale")
            ->orderBy('sum', 'desc')
            ->take(10)
            ->get();

        $sanctions = $sanctions->map(function ($sanction) {
            $sanction->decided_at = $sanction->decided_at_for_humans;
            $sanction->sum = round($sanction->sum);

            return $sanction;
        });

        return $sanctions->makeVisible(['sector', 'country', 'type', 'sum']);
    }
}
