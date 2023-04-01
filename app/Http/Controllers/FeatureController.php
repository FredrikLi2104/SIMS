<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationStatementImplementationUpdateRequest;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeatureController extends Controller
{
    public function index()
    {
        $messages = __('messages');
        $statements = auth()->user()->organisation->statements;
        $statements = $statements->map(function ($statement) {
            $statement->implementation = $statement->pivot->implementation;
            return $statement;
        })->makeVisible(['subcode', 'implementation']);
        $organisations = auth()->user()->organisation->organisations;

        return view('features', compact('messages', 'statements', 'organisations'));
    }

    public function updateImplementations(OrganisationStatementImplementationUpdateRequest $request)
    {
        $statements = $request->post('statements');
        $organisations = $request->post('organisations');

        foreach ($organisations as $organisationId) {
            $organisation = Organisation::find($organisationId);
            foreach ($statements as $statementId) {
                $statement = auth()->user()->organisation->statements()->where('statements.id', $statementId)->first();
                $organisation->statements()->syncWithoutDetaching([$statementId => ['implementation' => $statement->pivot->implementation]]);
            }
        }

        return ['success' => true];
    }
}
