<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagUpdateRequest extends FormRequest
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
            'tag_en' => ['required', Rule::unique('tags')->ignore($this->route()->tag->id)],
            'desc_en' => 'required',
            'tag_se' => ['required', Rule::unique('tags')->ignore($this->route()->tag->id)],
            'desc_se' => 'required',
        ];
    }
}
