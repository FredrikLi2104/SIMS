<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationStoreRequest;
use App\Http\Requests\OrganisationUpdateRequest;
use App\Models\Action;
use App\Models\Component;
use App\Models\Faq;
use App\Models\Link;
use App\Models\Organisation;
use App\Models\ReviewStatus;
use App\Models\Sni;
use App\Models\Statement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrganisationController extends Controller
{
    public function insights()
    {
        return view('models.organisations.insights');
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
     * Show the organisation review page
     *
     * For organisation auditors show the review table
     *
     * @return Illuminate\Support\Facades\View
     **/
    public function review()
    {
        $reviewStatuses = ReviewStatus::all();
        return view('models.organisations.review', compact('reviewStatuses'));
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
