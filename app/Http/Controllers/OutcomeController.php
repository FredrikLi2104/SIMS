<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutcomeStoreRequest;
use App\Http\Requests\OutcomeUpdateRequest;
use App\Models\Outcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class OutcomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['outcomes'] = Outcome::all();

        return view('models/outcomes/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $data['action_url'] = route('outcomes.store', App::currentLocale());
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.outcome') . ' ' . __('messages.create');

        return view('models/outcomes/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(OutcomeStoreRequest $request)
    {
        $data = $request->validated();
        Outcome::create($data);

        return redirect()->route('outcomes.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Outcome $outcome
     * @return \Illuminate\Http\Response
     */
    public function show(Outcome $outcome)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Outcome $outcome
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Outcome $outcome)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['action_url'] = route('outcomes.update', [App::currentLocale(), $outcome->id]);
        $data['outcome'] = $outcome;
        $data['action_msg'] = trans('messages.edit');
        $data['title'] = $data['title'] = trans('messages.outcomes') . ' ' . trans('messages.edit');

        return view('models/outcomes/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Outcome $outcome
     * @return \Illuminate\Http\Response
     */
    public function update(OutcomeUpdateRequest $request, $locale, Outcome $outcome)
    {
        $data = $request->validated();
        $outcome->update($data);

        return redirect()->route('outcomes.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Outcome $outcome
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outcome $outcome)
    {
        //
    }
}
