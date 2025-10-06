<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganisationsKpicommentsStoreRequest extends FormRequest
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
            'target' => ['required', 'integer', 'min:0'],
            'value' => ['required', 'integer', 'min:0'],
            'comment' => ['required'],
            'kpi_id' => ['required', 'exists:kpis,id']
        ];
    }
}
