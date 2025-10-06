<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStatusStoreRequest extends FormRequest
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
            'name_en' => 'required|unique:task_statuses',
            'name_se' => 'required|unique:task_statuses',
            'color' => ['required', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{8})$/i'],
            'sort_order' => 'required|integer|unique:task_statuses'
        ];
    }
}
