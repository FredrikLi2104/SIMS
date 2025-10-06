<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqStoreRequest extends FormRequest
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
            'question_en' => 'required|unique:faqs',
            'question_se' => 'required|unique:faqs',
            'answer_en' => 'required',
            'answer_se' => 'required',
            'sort_order' => 'required|integer|unique:faqs',
        ];
    }
}
