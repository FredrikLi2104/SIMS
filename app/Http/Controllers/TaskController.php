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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($locale)
    {
        $messages = __('messages');
        $statuses = TaskStatus::orderBy('sort_order')->get();
        $assignees = User::where('organisation_id', auth()->user()->organisation->id)
            ->orderBy('name')
            ->get();

        $subOrganisations = auth()->user()->organisation->organisations->all();

        while (count($subOrganisations)) {
            $next = [];
            foreach ($subOrganisations as $organisation) {
                $assignees = $assignees->merge($organisation->users);
                $next = array_merge($next, $organisation->organisations->all());
            }

            $subOrganisations = $next;
        }

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

        return view('models/tasks/index', compact('messages', 'statuses', 'assignees', 'actionTypes', 'months', 'years'));
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
            $task = Task::create([
                'title_en' => $data['title_en'],
                'title_se' => $data['title_se'],
                'desc_en' => $data['desc_en'],
                'desc_se' => $data['desc_se'],
                'start' => $data['start'],
                'end' => $data['end'],
                'hours' => $data['hours'],
                'task_status_id' => $data['task_status_id'],
                'created_by' => auth()->user()->id,
                'assigned_to' => $data['assigned_to'],
            ]);

            foreach ($data['action_type_id'] as $actionTypeId) {
                $action = Action::create([
                    'task_id' => $task->id,
                    'action_type_id' => $actionTypeId,
                    'action_status_id' => 1,
                ]);

                if (in_array($actionTypeId, [1, 2])) { // Component
                    $action->components()->attach($data['action_type_items'][$actionTypeId]);
                } elseif (in_array($actionTypeId, [3, 4, 5])) { // Statement
                    $action->statements()->attach($data['action_type_items'][$actionTypeId]);
                }
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
        return $task->load('assignee', 'taskStatus', 'actions', 'actions.actionType', 'actions.components', 'actions.statements');
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
                'assigned_to' => $data['assigned_to'],
            ]);

            $toUpdate = $task->actions()->whereIn('action_type_id', $data['action_type_id'])->get();
            $toDelete = $task->actions()->whereNotIn('action_type_id', $data['action_type_id'])->get();
            $toInsert = array_diff($data['action_type_id'], $toUpdate->pluck('action_type_id')->all(), $toDelete->pluck('action_type_id')->all());

            foreach ($toInsert as $actionTypeId) {
                $action = Action::create([
                    'task_id' => $task->id,
                    'action_type_id' => $actionTypeId,
                    'action_status_id' => 1,
                ]);

                if (in_array($actionTypeId, [1, 2])) { // Component
                    $action->components()->attach($data['action_type_items'][$actionTypeId]);
                } elseif (in_array($actionTypeId, [3, 4, 5])) { // Statement
                    $action->statements()->attach($data['action_type_items'][$actionTypeId]);
                }
            }

            $toUpdate->each(function ($action) use ($data) {
                if (in_array($action->action_type_id, [1, 2])) { // Component
                    $action->components()->sync($data['action_type_items'][$action->action_type_id]);
                } elseif (in_array($action->action_type_id, [3, 4, 5])) { // Statement
                    $action->statements()->sync($data['action_type_items'][$action->action_type_id]);
                }
            });

            $toDelete->each(function ($action) use ($data) {
                if (in_array($action->action_type_id, [1, 2])) { // Component
                    $action->components()->detach();
                } elseif (in_array($action->action_type_id, [3, 4, 5])) { // Statement
                    $action->statements()->detach();
                }

                $action->delete();
            });
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
