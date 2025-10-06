<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatementStoreRequest;
use App\Http\Requests\StatementUpdateRequest;
use App\Models\Component;
use App\Models\Statement;
use App\Models\StatementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class StatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $statements = Statement::all()->load('statement_type')->makeVisible('statement_type', 'subcode')->sortBy('subcode', SORT_NATURAL);
        return view('models.statements.index', compact('statements'));
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
            $sort_order_placeholder = (Statement::latest('sort_order')->first()->sort_order) + 1;
        } catch (\Throwable $th) {
            //throw $th;
            $sort_order_placeholder = 0;
        }
        $components = Component::all();
        $statementTypes = StatementType::all();
        return view('models.statements.create', compact(['components', 'sort_order_placeholder', 'statementTypes']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatementStoreRequest $request)
    {
        //
        $data = $request->all();
        Statement::create($data);
        return redirect()->route('statements.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Statement $statement
     * @return \Illuminate\Http\Response
     */
    public function show(Statement $statement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Statement $statement
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Statement $statement)
    {
        //
        try {
            $sort_order_placeholder = (Statement::latest('sort_order')->first()->sort_order) + 1;
        } catch (\Throwable $th) {
            //throw $th;
            $sort_order_placeholder = 0;
        }
        $components = Component::all();
        $statementTypes = StatementType::all();
        return view('models.statements.edit', compact(['components', 'sort_order_placeholder', 'statementTypes', 'statement']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statement $statement
     * @return \Illuminate\Http\Response
     */
    public function update($locale, StatementUpdateRequest $request, Statement $statement)
    {
        //
        $data = $request->all();
        $statement->update($data);
        return redirect()->route('statements.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Statement $statement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Statement $statement)
    {
        //
    }


}
