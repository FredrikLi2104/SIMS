<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Action;
use App\Models\ActionType;
use App\Models\Organisation;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($locale)
    {
        $messages = __('messages');
        $statuses = TaskStatus::orderBy('sort_order')->get();
        $actionTypes = ActionType::where('role', auth()->user()->role)
            ->orderBy("name_$locale")
            ->get();

        $localeForCarbon = $locale == 'se' ? 'sv-SE' : $locale;
        $date = Carbon::now()->locale($localeForCarbon);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $date->month = $i;
            $months[] = $date->shortMonthName;
        }

        $years = [
            $date->copy()->subYear()->format('Y'),
            $date->format('Y'),
            $date->copy()->addYear()->format('Y')
        ];

        return view('models/tasks/index', compact('messages', 'statuses', 'actionTypes', 'months', 'years'));
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
    public function store(TaskStoreRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $org = Organisation::find(session('selected_org')['id']);
            $auditor = $org->users->where('role', 'auditor')->first();
            $task = Task::create([
                'title_en' => $data['title_en'] ?? $data['title_se'],
                'title_se' => $data['title_se'] ?? $data['title_en'],
                'desc_en' => $data['desc_en'] ?? $data['desc_se'],
                'desc_se' => $data['desc_se'] ?? $data['desc_en'],
                'start' => $data['start'],
                'end' => $data['end'],
                'hours' => $data['hours'],
                'task_status_id' => $data['task_status_id'],
                'created_by' => $auditor->id ?? auth()->user()->id,
            ]);

            $action = Action::create([
                'task_id' => $task->id,
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
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show($locale, Task $task)
    {
        return $task->load('taskStatus', 'action', 'action.actionType', 'action.components', 'action.statements');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update($locale, TaskUpdateRequest $request, Task $task)
    {
        $data = $request->all();

        DB::transaction(function () use ($data, $task) {
            $task->update([
                'title_en' => $data['title_en'],
                'title_se' => $data['title_se'],
                'desc_en' => $data['desc_en'],
                'desc_se' => $data['desc_se'],
                'start' => $data['start'],
                'end' => $data['end'],
                'hours' => $data['hours'],
                'task_status_id' => $data['task_status_id'],
            ]);

            if ($task->action->action_type_id == $data['action_type_id']) { // Update
                if ($task->action->actionType->model == 'component') {
                    $task->action->components()->sync($data['action_type_items'][$task->action->action_type_id]);
                } elseif ($task->action->actionType->model == 'statement') {
                    $task->action->statements()->sync($data['action_type_items'][$task->action->action_type_id]);
                }
            } else {
                // Delete existing
                if ($task->action->actionType->model == 'component') {
                    $task->action->components()->detach();
                } elseif ($task->action->actionType->model == 'statement') {
                    $task->action->statements()->detach();
                }

                $task->action->delete();

                // Add new
                $action = Action::create([
                    'task_id' => $task->id,
                    'action_type_id' => $data['action_type_id'],
                    'action_status_id' => 1,
                ]);

                if ($action->actionType->model == 'component') {
                    $action->components()->attach($data['action_type_items'][$data['action_type_id']]);
                } elseif ($action->actionType->model == 'statement') {
                    $action->statements()->attach($data['action_type_items'][$data['action_type_id']]);
                }
            }
        });

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($locale, Task $task)
    {
        $result = $task->delete();
        $data = $result ? ['success' => true, 'msg' => __('messages.delete_success')] : ['error' => true, 'msg' => __('messages.error')];

        return response()->json($data);
    }
}
