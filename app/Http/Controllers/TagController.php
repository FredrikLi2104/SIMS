<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagStoreRequest;
use App\Http\Requests\TagUpdateRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('models/tags/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $data['action_url'] = route('tags.store', App::currentLocale());
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.tag') . ' ' . __('messages.create');

        return view('models/tags/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TagStoreRequest $request)
    {
        $data = $request->validated();
        Tag::create($data);

        return redirect()->route('tags.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Tag $tag)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['action_url'] = route('tags.update', [App::currentLocale(), $tag->id]);
        $data['tag'] = $tag;
        $data['action_msg'] = trans('messages.edit');
        $data['title'] = $data['title'] = trans('messages.tags') . ' ' . trans('messages.edit');

        return view('models/tags/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Http\Response
     */
    public function update(TagUpdateRequest $request, $locale, Tag $tag)
    {
        $data = $request->validated();
        $tag->update($data);

        return redirect()->route('tags.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        //
    }
}
