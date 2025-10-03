<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationStoreRequest;
use App\Http\Requests\OrganisationUpdateRequest;
use App\Models\Action;
use App\Models\Component;
use App\Models\Dpa;
use App\Models\Faq;
use App\Models\Link;
use App\Models\Organisation;
use App\Models\Outcome;
use App\Models\Plan;
use App\Models\ReviewStatus;
use App\Models\Sni;
use App\Models\Statement;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrganisationController extends Controller
{
    public function insights($locale)
    {
        // Cache DPAs with sanctions count for 1 hour
        $dpas = Cache::remember('insights_dpas', 3600, function () {
            return Dpa::with(['country'])
                ->select(['dpas.id', 'dpas.title', 'dpas.country_id'])
                ->selectRaw('count(*) AS count')
                ->join('sanctions', 'dpas.id', '=', 'sanctions.dpa_id')
                ->groupBy(['dpas.id', 'dpas.title', 'dpas.country_id'])
                ->orderBy('dpas.title')
                ->get()
                ->makeVisible(['count', 'country'])
                ->makeHidden(['country_id'])
                ->map(function ($dpa) {
                    $dpa->title = str_replace('Category:', '', $dpa->title);
                    return $dpa;
                });
        });

        // Cache countries for 1 hour
        $countries = Cache::remember('insights_countries', 3600, function () use ($dpas) {
            return $dpas->pluck('country')->filter()->unique()->sortBy('name')->values();
        });

        // Cache SNIs for 1 hour
        $snis = Cache::remember('insights_snis', 3600, function () {
            return Sni::select(['id', 'code', 'desc_en', 'desc_se'])
                ->orderBy('code')
                ->get();
        });

        // Cache outcomes for 1 hour (locale-specific)
        $outcomes = Cache::remember("insights_outcomes_{$locale}", 3600, function () use ($locale) {
            return Outcome::orderBy("desc_$locale")->get();
        });

        // Cache tags for 1 hour (locale-specific)
        $tags = Cache::remember("insights_tags_{$locale}", 3600, function () use ($locale) {
            return Tag::orderBy("tag_$locale")->get();
        });

        // Cache components for 1 hour
        $components = Cache::remember('insights_components', 3600, function () {
            return Component::all()->sortBy('code', SORT_NATURAL);
        });

        // Cache statements for 1 hour
        $statements = Cache::remember('insights_statements', 3600, function () {
            return Statement::all()
                ->makeVisible(['subcode'])
                ->sortBy('subcode', SORT_NATURAL);
        });

        return view('models.organisations.insights', compact(
            'dpas', 'countries', 'snis', 'outcomes', 'tags', 'components', 'statements'
        ));
    }

    /**
     * Show the organisation plan for the auditors
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function auditorPlan($locale, Action $action = null)
    {
        if ($action !== null && auth()->user()->cannot('view', $action)) {
            abort(403);
        }

        $actionId = $action?->id;

        return view('models.organisations.auditor.plan', compact('actionId'));
    }

    /**
     * Show the organisation do-able statements
     *
     * Show a table of all statements for the user to create deeds in relation to them
     *
     * @return \Illuminate\Http\Response
     **/
    public function do($locale, Action $action = null)
    {
        if ($action !== null && auth()->user()->cannot('view', $action)) {
            abort(403);
        }

        $actionId = $action?->id;
        return view('models.organisations.do', compact('actionId'));
    }

    /**
     * Return all kpis, inject them with kpi comments left by users of this organisation if any
     *
     * Undocumented function long description
     *
     * @param string $var App Locale
     * @return \Illuminate\Http\Response
     **/
    public function kpisIndex($locale)
    {
        return view('models.organisations.kpis');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $organisations = Organisation::all()->load('sni');
        return view('models.organisations.index', compact('organisations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $snis = Sni::all();
        $organisations = Organisation::all();
        return view('models.organisations.create', compact(['snis', 'organisations']));
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function exportCSV($locale, Action $action = null)
    {
        $org = Organisation::find(session('selected_org')['id']);

        // unify the fetched statements of all types

        if ($action) {
            //TODO: check access
            if ($action->actionType->model == 'component') {
                $components = $action->components;
                $statements = $components->flatMap(function ($component) {
                    return $component->statements->makeVisible(['component', 'deed', 'implementation', 'guide', 'plan', 'review', 'subcode']);
                });
            } elseif ($action->actionType->model == 'statement') {
                $statements = $action->statements->makeVisible(['component', 'deed', 'implementation', 'guide', 'plan', 'review', 'subcode']);
            }
        } else {
            $statements = Statement::all()->load('component')->makeVisible(['component', 'deed', 'implementation', 'guide', 'plan', 'review', 'subcode']);
        }
        $plans = Plan::all()->sortBy('sort_order');
        // statistics
        $statistics = [
            'statements' => [
                'interview' => [
                    'statements' => [],
                    'class' => 'progress progress-bar-success',
                    'title' => Plan::where('id', 1)->first()->{'name_' . $locale},
                    'count' => 0,
                ],
                'test' => [
                    'statements' => [],
                    'class' => 'progress progress-bar-success',
                    'title' => Plan::where('id', 2)->first()->{'name_' . $locale},
                    'count' => 0,
                ],
                'webform' => [
                    'statements' => [],
                    'class' => 'progress progress-bar-success',
                    'title' => Plan::where('id', 3)->first()->{'name_' . $locale},
                    'count' => 0,
                ],
                'check' => [
                    'statements' => [],
                    'class' => 'progress progress-bar-success',
                    'title' => Plan::where('id', 5)->first()->{'name_' . $locale},
                    'count' => 0,
                ],
            ],
            'unplanned' => [
                'statements' => [],
                'count' => 0
            ]
        ];
        foreach ($statements as $statement) {
            $op = $statement->component->organisationPeriod($org);
            $statement->component->makeVisible(['organisation_period']);
            $statement->component->organisation_period = $op;
            //$statement->plan = null;
            $statement->implementation = null;
            $op = $statement->organisationPlan($org);
            if ($op) {
                //$statement->plan = $op->plan;
                $statement->implementation = $op->implementation;
            }
            $statement->deed = $statement->organisationDeed($org);
            $statement->review = $statement->organisationReview($org);
            $statementReviewPlan = $statement->reviewPlan();
            if ($statementReviewPlan) {
                $usersIds = $org->users->pluck('id');
                $r = DB::table('auditor_statement')->whereIn('user_id', $usersIds)->where('statement_id', $statement->id)->get()->first();
                $statement->guide = $r?->guide;
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
        $r = ['statements' => $statements];
        $r = collect($r);
        //return $r;
        //return $r;
        $fileName = 'Export' . $org->name . '.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('ITSB_Statement', 'ITSB_Component_EN', 'ITSB_Component_SE', 'ITSB_Content_EN', 'ITSB_Content_SE', 'ITSB_Desc_EN', 'ITSB_Desc_SE', 'ITSB_For_User_Implementation_Example_EN', 'ITSB_For_User_Implementation_Example_SE', 'ITSB_For_Auditor_How_to_Review_EN', 'ITSB_For_Auditor_How_to_Review_SE', 'User_Statement_Implementation', 'User_Statement_Responsibility', 'User_Deed_Value', 'User_Deed_Comment', 'Auditor_Plan_How_to_Review', 'Auditor_Review', 'Auditor_Review_Plan', 'Auditor_Review_Result');
        try {
            $callback = function () use ($statements, $columns, $locale, $org) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($statements as $statement) {
                    $row['ITSB_Statement'] = $statement->subcode;
                    $row['ITSB_Component_EN'] = $statement->component?->name_en;
                    $row['ITSB_Component_SE'] = $statement->component?->name_se;
                    $row['ITSB_Content_EN'] = $statement->content_en;
                    $row['ITSB_Content_SE'] = $statement->content_se;
                    $row['ITSB_Desc_EN'] = $statement->desc_en;
                    $row['ITSB_Desc_SE'] = $statement->desc_se;
                    $row['ITSB_For_User_Implementation_Example_EN'] = $statement->implementation_en;
                    $row['ITSB_For_User_Implementation_Example_SE'] = $statement->implementation_se;
                    $row['ITSB_For_Auditor_How_to_Review_EN'] = $statement->guide_en;
                    $row['ITSB_For_Auditor_How_to_Review_SE'] = $statement->guide_se;
                    $orgst = DB::table('organisation_statement')->where('organisation_id', $org->id)->where('statement_id', $statement->id)->first();
                    if ($orgst) {
                        $imp = $orgst->implementation;
                        $resp = $orgst->responsibility;
                    } else {
                        $imp = 'Not Found';
                        $resp = 'Not Found';
                    }
                    $row['User_Statement_Implementation'] = $imp;
                    $row['User_Statement_Responsibility'] = $resp;
                    $row['User_Deed_Value'] = $statement->deed?->value;
                    $row['User_Deed_Comment'] = $statement->deed?->comment;
                    $orgUsers = $org->users->pluck('id');
                    $orgAudStat = DB::table('auditor_statement')->whereIn('user_id', $orgUsers)->get();
                    $orgAudStat = $orgAudStat->filter(function ($r) use ($statement) {
                        return $r->statement_id == $statement->id;
                    });
                    if ($orgAudStat->count() > 0) {
                        $aphr = $orgAudStat->first();
                        $aphr = $aphr->guide;
                    } else {
                        $aphr = 'Not Found';
                    }
                    $row['Auditor_Plan_How_to_Review'] = $aphr;
                    $row['Auditor_Review'] = $statement->review?->review;
                    $row['Auditor_Review_Plan'] = $statement->plan['name_'.$locale];
                    $row['Auditor_Review_Result'] = $statement->review?->reviewStatus['name_'.$locale];
                    
                    /*
                    $row['component']  = $statement->component?->code;
                    $row['code']    = $statement->code;
                    $row['component_name_en']    = $statement->component?->name_en;
                    $row['component_name_se']  = $statement->component?->name_se;
                    $row['content_en']  = $statement->content_en;
                    $row['content_se']  = $statement->content_se;
                    $row['desc_en']  = $statement->desc_en;
                    $row['desc_se']  = $statement->desc_se;
                    $row['guide_en']  = $statement->guide_en;
                    $row['guide_se']  = $statement->guide_se;
                    $row['implementation_en'] = $statement->implementation_en;
                    $row['implementation_se'] = $statement->implementation_se;
                    $row['value'] = $statement->deed?->value;
                    $row['comment'] = $statement->deed?->comment;
                    $row['review'] = $statement->review?->review;
                    $row['plan'] = $statement->plan['name_' . $locale];
                    */
                    // post processing for 696 (csv issue on import, clear characters)
                    foreach ($row as $key => $val) {
                        $row[$key] = str_replace(["\r", "\n"], '', $val);
                    }
                    fputcsv($file, array($row['ITSB_Statement'], $row['ITSB_Component_EN'], $row['ITSB_Component_SE'], $row['ITSB_Content_EN'], $row['ITSB_Content_SE'], $row['ITSB_Desc_EN'], $row['ITSB_Desc_SE'], $row['ITSB_For_User_Implementation_Example_EN'], $row['ITSB_For_User_Implementation_Example_SE'], $row['ITSB_For_Auditor_How_to_Review_EN'], $row['ITSB_For_Auditor_How_to_Review_SE'], $row['User_Statement_Implementation'], $row['User_Statement_Responsibility'], $row['User_Deed_Value'], $row['User_Deed_Comment'], $row['Auditor_Plan_How_to_Review'], $row['Auditor_Review'], $row['Auditor_Review_Plan'], $row['Auditor_Review_Result']));
                }



                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the organisation review page
     *
     * For organisation auditors show the review table
     *
     * @return Illuminate\Support\Facades\View
     **/
    public function review($locale, Action $action = null)
    {
        //dd(session()->all());
        if ($action !== null && auth()->user()->cannot('view', $action)) {
            abort(403);
        }

        $actionId = $action?->id;
        $reviewStatuses = ReviewStatus::where('name_en', '<>', 'Pending')->get();
        $plans = Plan::all()->sortBy('id');
        $org = Organisation::where('id', session()->get('selected_org')['id'])->first();
        $auditorStatements = $org->auditorStatements($action);
        //dd($auditorStatements);
        return view('models.organisations.review', compact('auditorStatements', 'reviewStatuses', 'actionId', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrganisationStoreRequest $request)
    {
        //
        $data = $request->all();
        // logo uploaded?
        if (isset($data['logofile'])) {
            $data['logofile'] = Storage::putFile('public/organisations/logos', $data['logofile']);
        }
        // color selected?
        if ($data['color'] != '#000001') {
            $data['color'] = substr($data['color'], 1);
        } else {
            $data['color'] = null;
        }
        try {
            DB::transaction(function () use ($data) {
                $organisation = Organisation::create($data);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->route('organisations.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Organisation $organisation
     * @return \Illuminate\Http\Response
     */
    public function show(Organisation $organisation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Organisation $organisation
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Organisation $organisation)
    {
        //
        $snis = Sni::all();
        $organisations = Organisation::all();
        return view('models.organisations.edit', compact(['organisation', 'organisations', 'snis']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Organisation $organisation
     * @return \Illuminate\Http\Response
     */
    public function update($locale, OrganisationUpdateRequest $request, Organisation $organisation)
    {
        //
        $data = $request->all();
        // logo uploaded?
        if (isset($data['logofile'])) {
            $data['logofile'] = Storage::putFile('public/organisations/logos', $data['logofile']);
        }
        // color selected?
        if ($data['color'] != '#00315c') {
            $data['color'] = substr($data['color'], 1);
        } else {
            $data['color'] = null;
        }
        try {
            DB::transaction(function () use ($data, $organisation) {
                $organisation->update($data);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->route('organisations.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Organisation $organisation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organisation $organisation)
    {
        //
    }

    public function plan($locale, Action $action = null)
    {
        if ($action !== null && auth()->user()->cannot('view', $action)) {
            abort(403);
        }

        $type = 'statements';
        $actionId = $action?->id;
        return view('models.organisations.plan', compact('type', 'actionId'));
    }

    public function report($locale, Action $action = null)
    {
        if ($action !== null && auth()->user()->cannot('view', $action)) {
            abort(403);
        }

        $type = 'report';
        $actionId = $action?->id;
        return view('models.organisations.plan', compact('type', 'actionId'));
    }

    public function knowledge()
    {
        $data['faqs'] = Faq::where('live', true)->orderBy('sort_order')->get();
        $data['links'] = Link::where('live', true)->orderBy('sort_order')->get();
        $data['messages'] = __('messages');

        return view('models.organisations.knowledge', $data);
    }

    public function componentSanctions($locale, Component $component)
    {
        $componentCode = $component->code;
        return view('models.organisations.component-sanctions', compact('componentCode'));
    }

    public function statementSanctions($locale, Statement $statement)
    {
        $statementSubCode = $statement->subcode;
        return view('models.organisations.statement-sanctions', compact('statementSubCode'));
    }

    /**
     * Clear insights cache
     *
     * Call this method whenever relevant data is updated:
     * - After importing sanctions
     * - After updating DPAs, SNIs, outcomes, tags, components, or statements
     * - Can be called manually or via observer/event listeners
     *
     * Example usage:
     * OrganisationController::clearInsightsCache();
     */
    public static function clearInsightsCache()
    {
        $locales = ['en', 'sv', 'se']; // Add all your supported locales

        Cache::forget('insights_dpas');
        Cache::forget('insights_countries');
        Cache::forget('insights_snis');
        Cache::forget('insights_components');
        Cache::forget('insights_statements');

        foreach ($locales as $locale) {
            Cache::forget("insights_outcomes_{$locale}");
            Cache::forget("insights_tags_{$locale}");
        }

        // Also clear sanctions count cache
        Cache::forget('sanctions_total_count');
    }

    /**
     * Force refresh insights cache
     *
     * Useful for admin panel or after bulk updates
     */
    public function refreshInsightsCache($locale)
    {
        self::clearInsightsCache();
        return $this->insights($locale);
    }
}
