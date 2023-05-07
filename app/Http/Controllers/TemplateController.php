<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateStoreRequest;
use App\Models\Template;
use App\Models\TemplateAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TemplateStoreRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $template = Template::create([
                'name_en' => $data['name_en'],
                'name_se' => $data['name_se'],
                'desc_en' => $data['desc_en'],
                'desc_se' => $data['desc_se'],
                'start' => $data['start'],
                'end' => $data['end'],
                'hours' => $data['hours'],
                'task_status_id' => $data['task_status_id'],
            ]);

            $action = TemplateAction::create([
                'template_id' => $template->id,
                'action_type_id' => $data['action_type_id'],
                'action_status_id' => 1,
            ]);

            if ($action->actionType->model == 'component') {
                $action->components()->attach($data['action_type_items'][$data['action_type_id']]);
            } elseif ($action->actionType->model == 'statement') {
                $action->statements()->attach($data['action_type_items'][$data['action_type_id']]);
            }
        });

        return ['success' => true];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Template $template
     * @return \Illuminate\Http\Response
     */
    public function show(Template $template)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Template $template
     * @return \Illuminate\Http\Response
     */
    public function edit(Template $template)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Template $template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Template $template)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Template $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(Template $template)
    {
        //
    }
}
