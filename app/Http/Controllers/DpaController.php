<?php

namespace App\Http\Controllers;

use App\Http\Requests\DpaUpdateRequest;
use App\Models\Country;
use App\Models\Dpa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DpaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //$dpas = Dpa::with('country')->get();
        return view('models.dpas.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dpa  $dpa
     * @return \Illuminate\Http\Response
     */
    public function show(Dpa $dpa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dpa  $dpa
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Dpa $dpa)
    {
        //
        $countries = Country::all()->sortBy('name');
        return view('models.dpas.edit', compact('countries', 'dpa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dpa  $dpa
     * @return \Illuminate\Http\Response
     */
    public function update($locale, DpaUpdateRequest $request, Dpa $dpa)
    {
        //
        $data = $request->validated();
        $dpa->update(['country_id' => $data['country_id']]);
        return redirect()->route('dpas.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dpa  $dpa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dpa $dpa)
    {
        //
    }
}
