<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanStoreRequest extends FormRequest
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
            'name_en' => ['required', 'unique:plans'],
            'name_se' => ['required', 'unique:plans'],
            'desc_en' => ['nullable'],
            'desc_se' => ['nullable'],
            'sort_order' => ['required', 'integer', 'unique:plans']
        ];
    }
}
