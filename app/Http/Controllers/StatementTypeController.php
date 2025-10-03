<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatementTypeStoreRequest;
use App\Http\Requests\StatementTypeUpdateRequest;
use App\Models\StatementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class StatementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $models = StatementType::all();
        return view('models.statement_types.index', compact('models'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('models.statement_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatementTypeStoreRequest $request)
    {
        //
        $data = $request->all();
        StatementType::create($data);
        return redirect()->route('statement_types.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StatementType  $component
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, StatementType $statement_type)
    {
        //
        return view('models.statement_types.edit', compact('statement_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StatementType  $component
     * @return \Illuminate\Http\Response
     */
    public function update($locale, StatementTypeUpdateRequest $request, StatementType $statement_type)
    {
        //
        $statement_type->update($request->all());
        return redirect()->route('statement_types.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));

    }
}
