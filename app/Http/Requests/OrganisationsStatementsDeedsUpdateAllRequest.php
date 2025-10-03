<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class OrganisationsStatementsDeedsUpdateAllRequest extends FormRequest
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
            'statements' => ['required'],
            'statements.*.id' => ['required', 'exists:statements,id'],
            'statements.*.value' => ['required', 'integer', 'between:1,5'],
            'statements.*.comment' => ['required'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        App::setLocale($this['locale']);
        switch (App::getLocale()) {
            case 'en':
                $r = [
                    'statements.*.value.required' => 'Value field is required for all statements, please check all statements have a value selected',
                    'statements.*.value.integer' => 'Value field must be an integer, please check all statements have a an integer value',
                    'statements.*.value.between' => 'Value field must be between 1 and 5, please check all statements values',
                    'statements.*.comment.required' => 'Comment field is required for all statements, please check all statements have a comment',
                ];
                break;
            case 'se':
                $r = [
                    'statements.*.value.required' => 'Fältet värde krävs för alla anmärkningar. Kontrollera att alla anmärkningar har ett värde valt',
                    'statements.*.value.integer' => 'Fältet värde måste vara ett heltal. Kontrollera att alla anmärkningar har ett heltalsvärde',
                    'statements.*.value.between' => 'Fältet värde måste vara mellan 1 och 5. Kontrollera alla värden för anmärkningar',
                    'statements.*.comment.required' => 'Kommentarfältet är obligatoriskt för alla anmärkningar. Kontrollera att alla uttalanden har en kommentar',
                ];
                break;
            default:
                $r = [
                    'statements.*.value.required' => 'Value field is required for all statements, please check all statements have a value selected',
                    'statements.*.value.integer' => 'Value field must be an integer, please check all statements have a an integer value',
                    'statements.*.value.between' => 'Value field must be between 1 and 5, please check all statements values',
                    'statements.*.comment.required' => 'Comment field is required for all statements, please check all statements have a comment',
                ];
                break;
        }
        return $r;
    }
}
