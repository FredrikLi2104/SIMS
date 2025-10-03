<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkStoreRequest;
use App\Http\Requests\LinkUpdateRequest;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['messages'] = __('messages');

        return view('models/links/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $sortOrder = Link::latest('sort_order')->first()?->sort_order;
        $data['sort_order'] = is_null($sortOrder) ? 0 : $sortOrder + 1;
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.faqs') . ' ' . __('messages.create');
        $data['messages'] = __('messages');

        return view('models/links/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(LinkStoreRequest $request)
    {
        $data = $request->validated();
        $data['live'] = $request->input('live') ?? false;

        Link::create($data);

        return ['success' => true];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Link $link
     * @return \Illuminate\Http\Response
     */
    public function show(Link $link)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Link $link
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Link $link)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['link'] = $link;
        $data['sort_order'] = $link->sort_order;
        $data['action_msg'] = __('messages.edit');
        $data['title'] = __('messages.links') . ' ' . __('messages.edit');
        $data['messages'] = __('messages');

        return view('models/links/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Link $link
     * @return \Illuminate\Http\Response
     */
    public function update(LinkUpdateRequest $request, $locale, Link $link)
    {
        $data = $request->validated();
        $data['live'] = $request->input('live') ?? false;
        $link->update($data);

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Link $link
     * @return \Illuminate\Http\Response
     */
    public function destroy($locale, Link $link)
    {
        $result = $link->delete();
        $data = $result ? ['success' => true, 'msg' => __('messages.delete_success')] : ['error' => true, 'msg' => __('messages.error')];

        return response()->json($data);
    }
}
