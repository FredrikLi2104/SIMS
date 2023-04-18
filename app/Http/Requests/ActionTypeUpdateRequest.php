<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionTypeUpdateRequest extends FormRequest
{
    public function withValidator($validator)
    {
        $validator->sometimes('url', ['required', Rule::in(['auditor/plan', 'report', 'review'])], function ($input) {
            return $input->role === 'auditor';
        });

        $validator->sometimes('url', ['required', Rule::in(['do/components', 'do/statements', 'plan/components', 'plan/statements', 'report'])], function ($input) {
            return $input->role === 'user';
        });
    }

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
            'name_en' => 'required',
            'name_se' => 'required',
            'role' => ['required', Rule::in(['auditor', 'user'])],
            'model' => ['nullable', 'sometimes', Rule::in(['component', 'statement'])],
        ];
    }
}
