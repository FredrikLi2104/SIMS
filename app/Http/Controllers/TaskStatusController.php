<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStatusStoreRequest;
use App\Http\Requests\TaskStatusUpdateRequest;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TaskStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['messages'] = __('messages');

        return view('models/task_statuses/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $sortOrder = TaskStatus::latest('sort_order')->first()?->sort_order;
        $data['sort_order'] = is_null($sortOrder) ? 0 : $sortOrder + 1;
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.task_statuses') . ' ' . __('messages.create');
        $data['messages'] = __('messages');

        return view('models/task_statuses/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskStatusStoreRequest $request)
    {
        $data = $request->validated();
        TaskStatus::create($data);

        return ['success' => true];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\TaskStatus $taskStatus
     * @return \Illuminate\Http\Response
     */
    public function show(TaskStatus $taskStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\TaskStatus $taskStatus
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, TaskStatus $taskStatus)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['task_status'] = $taskStatus;
        $data['sort_order'] = $taskStatus->sort_order;
        $data['action_msg'] = __('messages.edit');
        $data['title'] = __('messages.task_statuses') . ' ' . __('messages.edit');
        $data['messages'] = __('messages');

        return view('models/task_statuses/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\TaskStatus $taskStatus
     * @return \Illuminate\Http\Response
     */
    public function update($locale, TaskStatusUpdateRequest $request, TaskStatus $taskStatus)
    {
        $data = $request->validated();
        $taskStatus->update($data);

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\TaskStatus $taskStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskStatus $taskStatus)
    {
        //
    }
}
