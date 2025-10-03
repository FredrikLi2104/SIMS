<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigStoreRequest;
use App\Http\Requests\ConfigUpdateRequest;
use App\Models\ActionType;
use App\Models\Config;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['messages'] = __('messages');

        return view('models/configs/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.configs') . ' ' . __('messages.create');
        $data['messages'] = __('messages');
        $data['task_statuses'] = TaskStatus::orderBy('sort_order')->get();
        $data['action_types'] = ActionType::orderBy("name_$locale")->get();

        return view('models/configs/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConfigStoreRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $config = Config::create([
                'name_en' => $data['name_en'],
                'name_se' => $data['name_se'],
                'desc_en' => $data['desc_en'],
                'desc_se' => $data['desc_se'],
            ]);

            $config->templates()->attach($data['template_id']);
        });

        return ['success' => true];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Config $config
     * @return \Illuminate\Http\Response
     */
    public function show(Config $config)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Config $config
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Config $config)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['config'] = $config->load('templates');
        $data['action_msg'] = __('messages.edit');
        $data['title'] = __('messages.configs') . ' ' . __('messages.edit');
        $data['messages'] = __('messages');
        $data['task_statuses'] = TaskStatus::orderBy('sort_order')->get();
        $data['action_types'] = ActionType::orderBy("name_$locale")->get();

        return view('models/configs/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Config $config
     * @return \Illuminate\Http\Response
     */
    public function update(ConfigUpdateRequest $request, $locale, Config $config)
    {
        $data = $request->validated();
        $config->update($data);
        $config->templates()->sync($data['template_id']);

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Config $config
     * @return \Illuminate\Http\Response
     */
    public function destroy(Config $config)
    {
        //
    }
}
