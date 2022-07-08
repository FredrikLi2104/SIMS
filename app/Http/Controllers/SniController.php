<?php

namespace App\Http\Controllers;

use App\Http\Requests\SniStoreRequest;
use App\Http\Requests\SniUpdateRequest;
use App\Models\Sni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SniController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //
        $snis = Sni::all();
        return view('models.snis.index', compact('snis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('models.snis.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SniStoreRequest $request)
    {
        //
        $data = $request->all();
        Sni::create($data);
        return redirect()->route('snis.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sni  $sni
     * @return \Illuminate\Http\Response
     */
    public function show(Sni $sni)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sni  $sni
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Sni $sni)
    {
        //
        return view('models.snis.edit', compact('sni'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sni  $sni
     * @return \Illuminate\Http\Response
     */
    public function update($locale, SniUpdateRequest $request, Sni $sni)
    {
        //
        $sni->update($request->all());
        return redirect()->route('snis.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sni  $sni
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sni $sni)
    {
        //
    }
}
