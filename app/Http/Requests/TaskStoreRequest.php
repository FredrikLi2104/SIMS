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
        for ($i = 1; $i <= 2; $i++) {
            $validator->sometimes("action_type_items.$i", 'required|exists:components,id', function ($input) use ($i) {
                return in_array($i, $input->action_type_id ?? []);
            });
        }

        for ($i = 3; $i <= 5; $i++) {
            $validator->sometimes("action_type_items.$i", 'required|exists:statements,id', function ($input) use ($i) {
                return in_array($i, $input->action_type_id ?? []);
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
            'title_en' => 'required',
            'title_se' => 'required',
            'desc_en' => 'required',
            'desc_se' => 'required',
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
        $actionTypes = ActionType::whereIn('id', [1, 2, 3, 4, 5])->get();

        $attributes = [];
        $actionTypes->each(function ($actionType) use ($locale, &$attributes) {
            $attributes["action_type_items.$actionType->id"] = $actionType->{"name_$locale"};
        });

        return $attributes;
    }
}
