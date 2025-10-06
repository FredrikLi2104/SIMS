<?php

namespace App\Http\Requests;

use App\Models\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;

class OrganisationComponentsUpdateRequest extends FormRequest
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
            'components' => [
                'required',
                function ($attribute, $value, $fail) {
                    $components = Component::whereHas('statements.reviews', function (Builder $query) {
                        $query->where('organisation_id', auth()->user()->organisation->id);
                    })->get();

                    foreach ($value as $componentId) {
                        $componentExists = $components->contains(function ($component) use ($componentId) {
                            return $component->id == $componentId;
                        });

                        if (!$componentExists) {
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
