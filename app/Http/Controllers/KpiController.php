<?php

namespace App\Http\Controllers;

use App\Http\Requests\KpiStoreRequest;
use App\Http\Requests\KpiUpdateRequest;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class KpiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $kpis = Kpi::all();
        return view('models.kpis.index', compact('kpis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('models.kpis.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($locale, KpiStoreRequest $request)
    {
        //
        $data = $request->validated();
        Kpi::create($data);
        return redirect()->route('kpis.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\kpi  $kpi
     * @return \Illuminate\Http\Response
     */
    public function show(kpi $kpi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\kpi  $kpi
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Kpi $kpi)
    {
        //
        return view('models.kpis.edit', compact('kpi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\kpi  $kpi
     * @return \Illuminate\Http\Response
     */
    public function update($locale, KpiUpdateRequest $request, Kpi $kpi)
    {
        //
        $data = $request->validated();
        $kpi->update($data);
        return redirect()->route('kpis.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\kpi  $kpi
     * @return \Illuminate\Http\Response
     */
    public function destroy(kpi $kpi)
    {
        //
    }
}
