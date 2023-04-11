<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActionTypeStoreRequest;
use App\Http\Requests\ActionTypeUpdateRequest;
use App\Models\ActionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ActionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['messages'] = __('messages');

        return view('models/action_types/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.actionType') . ' ' . __('messages.create');
        $data['messages'] = __('messages');
        $data['roles'] = ['auditor', 'user'];
        $data['urls'] = [
            'auditor' => ['plan/statements', 'report', 'review'],
            'user' => ['do/components', 'do/statements', 'plan/components', 'plan/statements', 'report'],
        ];
        $data['models'] = ['component', 'statement'];

        return view('models/action_types/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActionTypeStoreRequest $request)
    {
        $data = $request->validated();
        ActionType::create($data);

        return ['success' => true];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Http\Response
     */
    public function show(ActionType $actionType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, ActionType $actionType)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['action_type'] = $actionType;
        $data['action_msg'] = __('messages.edit');
        $data['title'] = __('messages.actionType') . ' ' . __('messages.edit');
        $data['messages'] = __('messages');
        $data['roles'] = ['auditor', 'user'];
        $data['urls'] = [
            'auditor' => ['plan/statements', 'report', 'review'],
            'user' => ['do/components', 'do/statements', 'plan/components', 'plan/statements', 'report'],
        ];
        $data['models'] = ['component', 'statement'];

        return view('models/action_types/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Http\Response
     */
    public function update(ActionTypeUpdateRequest $request, $locale, ActionType $actionType)
    {
        $data = $request->validated();
        $actionType->update($data);

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ActionType $actionType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActionType $actionType)
    {
        //
    }
}
