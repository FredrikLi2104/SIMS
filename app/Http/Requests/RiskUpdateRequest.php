<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiskUpdateRequest extends FormRequest
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
            'probability' => ['required', 'integer', 'min:1', 'max:5'],
            'consequence' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['required'],
            'desc' => ['required'],
            'component_id' => ['nullable', 'sometimes', 'exists:components,id'],
            'responsible' => ['nullable'],
        ];
    }
}
