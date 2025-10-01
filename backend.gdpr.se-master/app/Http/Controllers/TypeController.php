<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeStoreRequest;
use App\Http\Requests\TypeUpdateRequest;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['types'] = Type::all();

        return view('models/types/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $data['action_url'] = route('types.store', App::currentLocale());
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.types') . ' ' . __('messages.create');

        return view('models/types/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TypeStoreRequest $request)
    {
        $data = $request->all();
        Type::create($data);

        return redirect()->route('types.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Type $type
     * @return \Illuminate\Http\Response
     */
    public function show(Type $type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Type $type
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Type $type)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['action_url'] = route('types.update', [App::currentLocale(), $type->id]);
        $data['type'] = $type;
        $data['action_msg'] = trans('messages.edit');
        $data['title'] = $data['title'] = trans('messages.types') . ' ' . trans('messages.edit');

        return view('models/types/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Type $type
     * @return \Illuminate\Http\Response
     */
    public function update(TypeUpdateRequest $request, $locale, Type $type)
    {
        $type->text_en = $request->input('text_en');
        $type->text_se = $request->input('text_se');
        $type->save();

        return redirect()->route('types.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Type $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Type $type)
    {
        //
    }
}
