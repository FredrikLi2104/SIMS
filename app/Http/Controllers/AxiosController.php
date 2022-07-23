<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationsComponentsPeriodsUpdateRequest;
use App\Http\Requests\OrganisationsKpicommentsStoreRequest;
use App\Http\Requests\OrganisationsKpicommentStoreRequest;
use App\Http\Requests\OrganisationsStatementsActionsUpdateRequest;
use App\Http\Requests\OrganisationsStatementsDeedsUpdateAllRequest;
use App\Http\Requests\OrganisationsStatementsDeedsUpdateRequest;
use App\Http\Requests\OrganisationsStatementsPlansUpdate;
use App\Http\Requests\OrganisationsStatementsPlansUpdateRequest;
use App\Http\Requests\OrganisationsStatementsReviewsUpdateRequest;
use App\Http\Requests\RiskCommentStoreRequest;
use App\Models\Component;
use App\Models\Country;
use App\Models\Deed;
use App\Models\Dpa;
use App\Models\Kpi;
use App\Models\Kpicomment;
use App\Models\Organisation;
use App\Models\Period;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Risk;
use App\Models\RiskComment;
use App\Models\Sanction;
use App\Models\Statement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

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
            if($statement->deed && $statement->review) {
                $statement->review->makeVisible(['updated_at_for_humans', 'new']);
                $statement->review->new = false;
                if (Carbon::parse($statement->deed->updated_at) < Carbon::parse($statement->review->updated_at)) {
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
        if(!(in_array($role, ['user', 'auditor']))) {
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
        $statements = Statement::all()->load('component')->makeVisible(['component', 'implementation', 'period', 'subcode']);
        foreach ($statements as $statement) {
            // calculate component periods for this organisation
            $oup = $statement->component->organisationUserPeriod(Auth::user()->organisation);
            $statement->component->makeVisible(['organisation_period']);
            $statement->component->organisation_period = $oup;
            $sp = $statement->organisationPlan(Auth::user()->organisation);
            $statement->implementation = $sp->implementation;
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
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['components' => $components, 'statements' => $statements, 'messages' => $messages];
        $r = collect($r);
        return $r;
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
        $statements = Statement::all()->load('component')->makeVisible(['component', 'deed', 'implementation', 'plan', 'review', 'subcode']);
        foreach ($statements as $statement) {
            $op = $statement->component->organisationPeriod(Auth::user()->organisation);
            $statement->component->makeVisible(['organisation_period']);
            $statement->component->organisation_period = $op;
            $statement->plan = null;
            $statement->implementation = null;
            $op = $statement->organisationPlan(Auth::user()->organisation);
            if ($op) {
                $statement->plan = $op->plan;
                $statement->implementation = $op->implementation;
            }
            $statement->deed = $statement->organisationDeed(Auth::user()->organisation);
            $statement->review = $statement->organisationReview(Auth::user()->organisation);
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
        //Scatter Data
        $dataSets = [];
        foreach ($risks as $risk) {
            $dataSets[] = ['label' => $risk->title, 'backgroundColor' => $risk->risk['colour'], 'borderColor' => $risk->risk['colour'], 'data' => [['x' => $risk->consequence, 'y' => $risk->probability, 'r' => 10 * count($risks->filter(function ($item) use ($risk) {
                return ($item->consequence == $risk->consequence && $item->probability == $risk->probability);
            })->all())]], 'count' => count($risks->filter(function ($item) use ($risk) {
                return ($item->consequence == $risk->consequence && $item->probability == $risk->probability);
            })->all())];
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
                return ($month->startOfMonth() <= Carbon::parse($item->created_at) && Carbon::parse($item->created_at) <= $month->endOfMonth());
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
        $r = ['messages' => $messages, 'risks' => $risks, 'now' => $now, 'colours' => $colours, 'series' => $series, 'dataSets' => $dataSets, 'history' => $history];
        $r = collect($r);
        return $r;
    }

    /**
     * Update or create a statement action
     *
     * Update or create a statement action in relation to an organisation, e.g: set statement x action for organization y to be "value 2, comment something"
     *
     * @param \Illuminate\Http\Request  $request
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
     * @param \Illuminate\Http\Request  $request
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function organisationsStatementsPlansUpdate(OrganisationsStatementsPlansUpdateRequest $request)
    {
        //
        $data = $request->validated();
        $o = Auth::user()->organisation;
        // find if this statement already has an entry
        $x = DB::table('organisation_statement')->where('organisation_id', $o->id)->where('statement_id', $data['statement_id'])->first();
        if ($x) {
            DB::table('organisation_statement')->where('organisation_id', $o->id)->where('statement_id', $data['statement_id'])->update(['implementation' => $data['implementation'], 'updated_at' => Carbon::now()]);
        } else {
            $o->statements()->attach([$data['statement_id'] => ['implementation' => $data['implementation'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]]);
        }
        return response('success', 200);
    }

    /**
     * Update the review of the statement in relation to an organisation
     *
     * e.g: set statement x review or organisation y and deed z to be "you did this wrong"
     *
     * @param  \Illuminate\Http\Request  $request
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
     * @throws Illuminate\Http\Response when the user is not authorized ot access said risk
     * @return Illuminate\Database\Eloquent\Collection
     **/
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
     * Return all sanctions along with dictionary
     *
     * Undocumented function long description
     *
     * @param string $locale app locale
     * @return Illuminate\Database\Eloquent\Collection
     **/
    public function sanctions($locale)
    {
        $sanctions = Sanction::all()->load(['articles', 'currency','dpa'])->makeVisible(['articles', 'articlesSorted', 'currency', 'created_at_for_humans', 'started_at_for_humans', 'decided_at_for_humans', 'published_at_for_humans', 'dpa', 'url']);
        foreach ($sanctions as $sanction) {
            $articles = $sanction->articles;
            $sanction->articlesSorted = $articles->sortBy('title')->values();
            $sanction->dpa->load('country')->makeVisible(['country', 'name']);
        }
        App::setlocale($locale);
        $messages = Lang::get('messages');
        $r = ['sanctions' => $sanctions, 'messages' => $messages];
        $r = collect($r);
        return $r;
    }
}
