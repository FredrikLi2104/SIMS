<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationStoreRequest;
use App\Http\Requests\OrganisationUpdateRequest;
use App\Models\Action;
use App\Models\Faq;
use App\Models\Link;
use App\Models\Organisation;
use App\Models\Sni;
use App\Models\Statement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrganisationController extends Controller
{
    public function act()
    {
        return view('models.organisations.act');
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
        return view('models.organisations.review');
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

        $type = $action?->action_type_id == 6 ? 'report' : 'statements';
        $actionId = $action?->id;
        $statements = Statement::all()->sortBy('sort_order');
        return view('models.organisations.plan', compact('statements', 'type', 'actionId'));
    }

    public function check()
    {
        $data['faqs'] = Faq::where('live', true)->orderBy('sort_order')->get();
        $data['links'] = Link::where('live', true)->orderBy('sort_order')->get();
        $data['messages'] = __('messages');

        return view('models.organisations.check', $data);
    }
}
