<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ComponentUpdateRequest extends FormRequest
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
            'name_en' => ['required', Rule::unique('components')->ignore($this->route()->component->id)],
            'name_se' => ['required', Rule::unique('components')->ignore($this->route()->component->id)],
            'desc_en' => ['required'],
            'desc_se' => ['required'],
            'code' => ['required', Rule::unique('components')->ignore($this->route()->component->id)],
            'sort_order' => ['required', 'numeric', Rule::unique('components')->ignore($this->route()->component->id)],
            'period_id' => ['required', 'exists:periods,id']
        ];
    }
}
