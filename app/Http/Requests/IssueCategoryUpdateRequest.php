<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IssueCategoryUpdateRequest extends FormRequest
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
            'desc_en' => ['required', Rule::unique('issue_categories')->ignore($this->route()->issue_category->id)],
            'desc_se' => ['required', Rule::unique('issue_categories')->ignore($this->route()->issue_category->id)],
        ];
    }
}
