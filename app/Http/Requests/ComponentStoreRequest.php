<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComponentStoreRequest extends FormRequest
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
            'name_en' => ['required', 'unique:components'],
            'name_se' => ['required', 'unique:components'],
            'desc_en' => ['required'],
            'desc_se' => ['required'],
            'code' => ['required', 'unique:components'],
            'sort_order' => ['required', 'numeric', 'unique:components'],
            'period_id' => ['required', 'exists:periods,id']
        ];
    }
}
