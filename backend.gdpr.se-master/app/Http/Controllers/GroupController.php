<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupStoreRequest;
use App\Http\Requests\GroupUpdateRequest;
use App\Models\Country;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['groups'] = Group::all();

        return view('models/groups/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['countries'] = Country::all();
        $data['action_url'] = route('groups.store', App::currentLocale());
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.groups') . ' ' . __('messages.create');

        return view('models/groups/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupStoreRequest $request)
    {
        $data = $request->validated();

        $countries = $data['countries'];
        unset($data['countries']);

        $group = Group::create($data);
        $group->countries()->attach($countries);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Group $group)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['action_url'] = route('groups.update', [App::currentLocale(), $group->id]);
        $data['group'] = $group->load('countries');
        $data['countries'] = Country::all();
        $data['country_ids'] = $group->countries->pluck('id')->all();
        $data['action_msg'] = __('messages.edit');
        $data['title'] = __('messages.group') . ' ' . __('messages.edit');
        $data['messages'] = __('messages');

        return view('models/groups/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\Response
     */
    public function update(GroupUpdateRequest $request, $locale, Group $group)
    {
        $data = $request->validated();

        $countries = $data['countries'];
        unset($data['countries']);

        $group->update($data);
        $group->countries()->sync($countries);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        //
    }
}
