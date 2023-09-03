<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InterviewStoreRequest extends FormRequest
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
            'agenda' => ['required'],
            'interviewee' => ['required'],
            'statements.*' => ['required', 'exists:statements,id'],
            'organisation_id' => ['required', 'exists:organisations,id']
        ];
    }
}
