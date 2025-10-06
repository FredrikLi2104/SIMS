<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganisationsStatementsDeedsUpdateRequest extends FormRequest
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
            'statement_id' => ['required', 'exists:statements,id'],
            'value' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required']
        ];
    }
}
