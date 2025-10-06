<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $plans = Plan::all()->sortBy('sort_order');
        return view('models.plans.index', compact('plans'));
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
            $sort_order_placeholder = (Plan::latest('sort_order')->first()->sort_order) + 1;
        } catch (\Throwable $th) {
            //throw $th;
            $sort_order_placeholder = 0;
        }
        return view('models.plans.create', compact('sort_order_placeholder'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($locale, PlanStoreRequest $request)
    {
        //
        $data = $request->validated();
        Plan::create($data);
        return redirect()->route('plans.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Plan $plan)
    {
        //
        try {
            $sort_order_placeholder = (Plan::latest('sort_order')->first()->sort_order) + 1;
        } catch (\Throwable $th) {
            //throw $th;
            $sort_order_placeholder = 0;
        }
        return view('models.plans.edit', compact('plan', 'sort_order_placeholder'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update($locale, PlanUpdateRequest $request, Plan $plan)
    {
        //
        $data = $request->validated();
        $plan->update($data);
        return redirect()->route('plans.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        //
    }
}
