<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStatusUpdateRequest extends FormRequest
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
            'name_en' => [
                'required',
                'unique' => Rule::unique('task_statuses')->ignore($this->route()->task_status->id)
            ],
            'name_se' => [
                'required',
                'unique' => Rule::unique('task_statuses')->ignore($this->route()->task_status->id)
            ],
            'color' => [
                'required',
                'regex:/^#([a-f0-9]{6}|[a-f0-9]{8})$/i'
            ],
            'sort_order' => [
                'required',
                'integer',
                'unique' => Rule::unique('task_statuses')->ignore($this->route()->task_status->id)
            ]
        ];
    }
}
