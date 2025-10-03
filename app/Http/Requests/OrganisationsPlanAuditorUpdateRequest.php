<?php

namespace App\Http\Requests;

use App\Models\Organisation;
use App\Models\Plan;
use App\Models\Statement;
use Illuminate\Foundation\Http\FormRequest;

class OrganisationsPlanAuditorUpdateRequest extends FormRequest
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
            '*' => function ($attribute, $value, $fail) {
                $plan = Plan::find($value['plan_id']);
                if ($plan->name_en === 'Check') {
                    $statement = Statement::find($value['statement_id']);
                    $org = Organisation::find(session('selected_org')['id']);
                    $deed = $statement->organisationDeed($org);
                    if (empty($deed)) {
                        $fail(__('messages.review_type_alert'));
                    }
                }
            },
            '*.statement_id' => [
                'required',
                'exists:statements,id',
            ],
            '*.plan_id' => ['required', 'exists:plans,id'],
        ];
    }
}
