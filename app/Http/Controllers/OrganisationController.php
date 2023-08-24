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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrganisationController extends Controller
{
    public function insights($locale)
    {
        $dpas = Dpa::with(['country'])->select(['dpas.id', 'dpas.title', 'dpas.country_id'])
            ->selectRaw('count(*) AS count')
            ->join('sanctions', 'dpas.id', '=', 'sanctions.dpa_id')
            ->groupBy(['dpas.id', 'dpas.title', 'dpas.country_id'])
            ->orderBy('dpas.title')
            ->get()
            ->makeVisible(['count', 'country'])
            ->makeHidden(['country_id']);

        $dpas = $dpas->map(function ($dpa) {
            $dpa->title = str_replace('Category:', '', $dpa->title);
            return $dpa;
        });

        $countries = $dpas->pluck('country')->filter()->unique()->sortBy('name')->values();
        $snis = Sni::select(['id', 'code', 'desc_en', 'desc_se'])->orderBy('code')->get();
        $outcomes = Outcome::orderBy("desc_$locale")->get();
        $tags = Tag::orderBy("tag_$locale")->get();
        $components = Component::all()->sortBy('code', SORT_NATURAL);
        $statements = Statement::all()->makeVisible(['subcode'])->sortBy('subcode', SORT_NATURAL);

        return view('models.organisations.insights', compact('dpas', 'countries', 'snis', 'outcomes', 'tags', 'components', 'statements'));
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
        $columns = array('component', 'code', 'component_name_en', 'component_name_se', 'content_en', 'content_se', 'desc_en', 'desc_se', 'guide_en', 'guide_se', 'implementation', 'value', 'comment', 'review', 'plan');
        $callback = function () use ($statements, $columns, $locale) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($statements as $statement) {
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
                $row['implementation'] = $statement->deed?->implementation;
                $row['value'] = $statement->deed?->value;
                $row['comment'] = $statement->deed?->comment;
                $row['review'] = $statement->review?->review;
                $row['plan'] = $statement->plan['name_' . $locale];
                // post processing for 696 (csv issue on import, clear characters)
                foreach ($row as $key => $val) {
                    $row[$key] = str_replace(["\r", "\n"], '', $val);
                }
                fputcsv($file, array($row['component'], $row['code'], $row['component_name_en'], $row['component_name_se'], $row['content_en'], $row['content_se'], $row['desc_en'], $row['desc_se'], $row['guide_en'], $row['guide_se'], $row['implementation'], $row['value'], $row['comment'], $row['review'], $row['plan']));
            }



            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
}
