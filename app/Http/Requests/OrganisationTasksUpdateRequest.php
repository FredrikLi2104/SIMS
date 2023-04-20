<?php

namespace App\Http\Requests;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class OrganisationTasksUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tasks' => [
                'required',
                function ($attribute, $value, $fail) {
                    $tasks = Task::with('taskStatus')
                        ->whereIn('created_by', auth()->user()->organisation->users->pluck('id'))
                        ->get();

                    foreach ($value as $taskId) {
                        $taskExists = $tasks->contains(function ($task) use ($taskId) {
                            return $task->id == $taskId;
                        });

                        if (!$taskExists) {
                            $fail(__('messages.error') . ' ' . $attribute);
                        }
                    }
                }
            ],
            'organisations' => [
                'required',
                function ($attribute, $value, $fail) {
                    $organisations = auth()->user()->organisation->organisations;
                    foreach ($value as $organisationId) {
                        $organisationExists = $organisations->contains(function ($organisation) use ($organisationId) {
                            return $organisation->id == $organisationId;
                        });

                        if (!$organisationExists) {
                            $fail(__('messages.error') . ' ' . $attribute);
                        }
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'tasks.required' => __('messages.items_required')
        ];
    }
}
