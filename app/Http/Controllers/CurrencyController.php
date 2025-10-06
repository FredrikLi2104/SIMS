<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyStoreRequest;
use App\Http\Requests\CurrencyUpdateRequest;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('models/currencies/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $data['action_url'] = route('currencies.store', App::currentLocale());
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.currencies') . ' ' . __('messages.create');

        return view('models/currencies/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CurrencyStoreRequest $request)
    {
        $data = $request->all();
        Currency::create($data);

        return redirect()->route('currencies.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Currency $currency
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Currency $currency
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Currency $currency)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['action_url'] = route('currencies.update', [App::currentLocale(), $currency->id]);
        $data['currency'] = $currency;
        $data['action_msg'] = trans('messages.edit');
        $data['title'] = $data['title'] = trans('messages.currencies') . ' ' . trans('messages.edit');

        return view('models/currencies/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Currency $currency
     * @return \Illuminate\Http\Response
     */
    public function update($locale, CurrencyUpdateRequest $request, Currency $currency)
    {
        $currency->symbol = $request->input('symbol');
        $currency->value = $request->input('value');
        $currency->save();

        return redirect()->route('currencies.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Currency $currency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
        //
    }
}
