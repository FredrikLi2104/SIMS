<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganisationStatementImplementationUpdateRequest extends FormRequest
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
            'statements' => [
                'required',
                function ($attribute, $value, $fail) {
                    $statements = auth()->user()->organisation->statements;
                    foreach ($value as $statementId) {
                        $statementExists = $statements->contains(function ($statement) use ($statementId) {
                            return $statement->id == $statementId;
                        });

                        if (!$statementExists) {
                            $fail(__('messages.error') . ' ' . $attribute);
                        }
                    }
                }
            ],
            'organisations' => [
                'required',
                function ($attribute, $value, $fail) {
                    $organisations = auth()->user()->organisation->organisations;
                    foreach ($value as $organisationId) {
                        $organisationExists = $organisations->contains(function ($organisation) use ($organisationId) {
                            return $organisation->id == $organisationId;
                        });

                        if (!$organisationExists) {
                            $fail(__('messages.error') . ' ' . $attribute);
                        }
                    }
                }
            ]
        ];
    }
}
