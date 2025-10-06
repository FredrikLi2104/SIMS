<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KpiUpdateRequest extends FormRequest
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
            'name_en' => ['required', Rule::unique('kpis')->ignore($this->route()->kpi->id)],
            'name_se' => ['required', Rule::unique('kpis')->ignore($this->route()->kpi->id)],
            'desc_en' => ['required'],
            'desc_se' => ['required'],
        ];
    }
}
