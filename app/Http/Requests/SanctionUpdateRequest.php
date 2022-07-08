<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SanctionUpdateRequest extends FormRequest
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
            'started_at' => 'nullable|sometimes|date',
            'decided_at' => 'nullable|sometimes|date',
            'published_at' => 'nullable|sometimes|date',
            'fine' => 'nullable|sometimes|numeric',
            'currency_id' => 'nullable|sometimes|exists:currencies,id'

        ];
    }
}
