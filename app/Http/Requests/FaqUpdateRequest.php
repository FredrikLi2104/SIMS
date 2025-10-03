<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FaqUpdateRequest extends FormRequest
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
            'question_en' => [
                'required',
                Rule::unique('faqs')->ignore($this->route()->faq->id)
            ],
            'question_se' => [
                'required',
                Rule::unique('faqs')->ignore($this->route()->faq->id)
            ],
            'answer_en' => 'required',
            'answer_se' => 'required',
            'sort_order' => [
                'required',
                'integer',
                Rule::unique('faqs')->ignore($this->route()->faq->id)
            ],
        ];
    }
}
