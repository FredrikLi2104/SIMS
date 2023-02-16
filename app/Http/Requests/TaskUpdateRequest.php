<?php

namespace App\Http\Requests;

use App\Models\ActionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class TaskUpdateRequest extends FormRequest
{
    public function withValidator($validator)
    {

        $validator->sometimes('action_type_items.1', 'required|exists:components,id', function ($input) {
            return in_array(1, $input->action_type_id ?? []);
        });

        for ($i = 2; $i <= 4; $i++) {
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
            'assigned_to' => 'required|exists:users,id',
            'action_type_id' => 'required|exists:action_types,id',
        ];
    }

    public function attributes()
    {
        $locale = App::currentLocale();
        $actionTypes = ActionType::whereIn('id', [1, 2, 3, 4])->get();

        $attributes = [];
        $actionTypes->each(function ($actionType) use ($locale, &$attributes) {
            $attributes["action_type_items.$actionType->id"] = $actionType->{"name_$locale"};
        });

        return $attributes;
    }
}
