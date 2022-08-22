<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodStoreRequest;
use App\Http\Requests\PeriodUpdateRequest;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $periods = Period::all()->sortBy('sort_order');
        return view('models.periods.index', compact('periods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        try {
            $sortOrderPlaceholder = (Period::latest('sort_order')->first()->sort_order) + 1;
        } catch (\Throwable $th) {
            //throw $th;
            $sortOrderPlaceholder = 0;
        }
        return view('models.periods.create', compact('sortOrderPlaceholder'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PeriodStoreRequest $request)
    {
        //
        $data = $request->all();
        Period::create($data);
        return redirect()->route('periods.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Period  $period
     * @return \Illuminate\Http\Response
     */
    public function show(Period $period)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Period  $period
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Period $period)
    {
        //
        try {
            $sortOrderPlaceholder = (Period::latest('sort_order')->first()->sort_order) + 1;
        } catch (\Throwable $th) {
            //throw $th;
            $sortOrderPlaceholder = 0;
        }
        return view('models.periods.edit', compact('period', 'sortOrderPlaceholder'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Period  $period
     * @return \Illuminate\Http\Response
     */
    public function update($locale, PeriodUpdateRequest $request, Period $period)
    {
        //
        $period->update($request->all());
        return redirect()->route('periods.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Period  $period
     * @return \Illuminate\Http\Response
     */
    public function destroy(Period $period)
    {
        //
    }
}
