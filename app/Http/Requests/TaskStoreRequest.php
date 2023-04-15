<?php

namespace App\Http\Requests;

use App\Models\ActionType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class TaskStoreRequest extends FormRequest
{
    public function withValidator($validator)
    {
        $actionTypeIds = $this->action_type_id ?? [];
        foreach ($actionTypeIds as $actionTypeId) {
            $actionType = ActionType::find($actionTypeId);

            $validator->sometimes("action_type_items.$actionTypeId", 'required|exists:components,id', function ($input) use ($actionType) {
                return $actionType->model == 'component';
            });

            $validator->sometimes("action_type_items.$actionTypeId", 'required|exists:statements,id', function ($input) use ($actionType) {
                return $actionType->model == 'statement';
            });
        }
    }

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
            'title_en' => 'required_without:title_se',
            'title_se' => 'required_without:title_en',
            'desc_en' => 'required_without:desc_se',
            'desc_se' => 'required_without:desc_en',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'hours' => 'required|numeric|min:0',
            'task_status_id' => 'required|exists:task_statuses,id',
            'assigned_to' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
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

                    $assigneeExists = $assignees->contains(function ($assignee) use ($value) {
                        return $value == $assignee->id;
                    });

                    if (!$assigneeExists) {
                        $fail($attribute . ' ' . __('messages.error'));
                    }
                }
            ],
            'action_type_id' => 'required|exists:action_types,id',
        ];
    }

    public function attributes()
    {
        $locale = App::currentLocale();
        $actionTypes = ActionType::whereIn('model', ['component', 'statement'])->get();
        $attributes = [];
        $actionTypes->each(function ($actionType) use ($locale, &$attributes) {
            $attributes["action_type_items.$actionType->id"] = $actionType->{"name_$locale"};
        });

        return $attributes;
    }
}
