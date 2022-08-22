<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComponentStoreRequest;
use App\Http\Requests\ComponentUpdateRequest;
use App\Models\Component;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $components = Component::all()->sortBy('sort_order');
        return view('models.components.index', compact('components'));
    }
    //
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        try {
            $sort_order_placeholder = (Component::latest('sort_order')->first()->sort_order) + 1;
        } catch (\Throwable $th) {
            //throw $th;
            $sort_order_placeholder = 0;
        }
        $periods = Period::all();
        return view('models.components.create', compact('periods', 'sort_order_placeholder'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ComponentStoreRequest $request)
    {
        //
        $data = $request->all();
        Component::create($data);
        return redirect()->route('components.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Component  $component
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Component $component)
    {
        //
        try {
            $sort_order_placeholder = (Component::latest('sort_order')->first()->sort_order) + 1;
        } catch (\Throwable $th) {
            //throw $th;
            $sort_order_placeholder = 0;
        }
        $periods = Period::all();
        return view('models.components.edit', compact('component', 'periods', 'sort_order_placeholder'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Component  $component
     * @return \Illuminate\Http\Response
     */
    public function update($locale, ComponentUpdateRequest $request, Component $component)
    {
        //
        $component->update($request->all());
        return redirect()->route('components.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }
}
