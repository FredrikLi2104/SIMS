<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutcomeStoreRequest extends FormRequest
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
            'desc_en' => 'required|unique:outcomes,desc_en',
            'desc_se' => 'required|unique:outcomes,desc_se',
        ];
    }
}
