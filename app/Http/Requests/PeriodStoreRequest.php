<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeriodStoreRequest extends FormRequest
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
            'name_en' => ['required', 'unique:periods'],
            'name_se' => ['required', 'unique:periods'],
            'start' => ['required', 'integer', 'min:1', 'max:12'],
            'end' => ['required', 'integer', 'min:1', 'max:12'],
            'sort_order' => ['required', 'integer', 'unique:periods']
        ];
    }
}
