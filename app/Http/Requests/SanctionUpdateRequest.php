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
            'fine' => 'nullable|sometimes|required_with:currency_id|numeric',
            'currency_id' => 'nullable|sometimes|required_with:fine|exists:currencies,id',
            'articles' => 'nullable|sometimes',
            'articles.*' => 'nullable|sometimes|exists:articles,id',
            'desc_en' => 'nullable|sometimes',
            'desc_se' => 'nullable|sometimes',
            'sni_id' => ['nullable', 'sometimes', 'exists:snis,id']
        ];
    }
}
