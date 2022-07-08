<?php

namespace App\Http\Controllers;

use App\Http\Requests\SanctionUpdateRequest;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Sanction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SanctionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $sanctions = Sanction::all();
        return view('models.sanctions.index', compact('sanctions'));
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
     * @param  \App\Models\Sanction  $sanction
     * @return \Illuminate\Http\Response
     */
    public function show(Sanction $sanction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sanction  $sanction
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Sanction $sanction)
    {
        //
        $countries = Country::all()->sortBy('name');
        $currencies = Currency::all();
        return view('models.sanctions.edit', compact('sanction', 'countries', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sanction  $sanction
     * @return \Illuminate\Http\Response
     */
    public function update($locale, SanctionUpdateRequest $request, Sanction $sanction)
    {
        //
        $data = $request->validated();
        $sanction->update(['decided_at' => $data['decided_at']]);
        return redirect()->route('sanctions.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sanction  $sanction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sanction $sanction)
    {
        //
    }
}
