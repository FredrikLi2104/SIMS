<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganisationStoreRequest extends FormRequest
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
            'name' => ['required', 'unique:organisations'],
            'number' => ['required', 'unique:organisations'],
            'turnover' => ['sometimes', 'nullable', 'integer'],
            'commitment' => ['required', 'integer', 'between:1,5'],
            'sni_id' => ['sometimes', 'nullable', 'exists:snis,id'],
            'organisation_id' => ['sometimes', 'nullable', 'exists:organisations,id'],
            'logofile' => ['sometimes', 'nullable', 'mimes:jpg,jpeg,png', 'max: 1024'],
            'color' => ['sometimes', 'nullable'],
            'phone' => ['sometimes', 'nullable', 'starts_with:+', function ($attribute, $value, $fail) {
                $digits = substr($value, 1);
                if (!(is_numeric($digits))) {
                    $fail($attribute . ' can contain only numbers and +');
                }
            }, 'min:8', 'max:16'],
            'address1' => ['sometimes', 'nullable', 'max:24'],
            'address2' => ['sometimes', 'nullable', 'max:24'],
            'email' => ['sometimes', 'nullable', 'email', 'max:24'],
            'website' => ['sometimes', 'nullable', 'url', 'max:36'],
        ];
    }
}
