<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PlanUpdateRequest extends FormRequest
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
            //
            'name_en' => ['required', Rule::unique('plans')->ignore($this->route()->plan->id)],
            'name_se' => ['required', Rule::unique('plans')->ignore($this->route()->plan->id)],
            'desc_en' => ['nullable'],
            'desc_se' => ['nullable'],
            'sort_order' => ['required', 'integer', Rule::unique('plans')->ignore($this->route()->plan->id)]
        ];
    }
}
