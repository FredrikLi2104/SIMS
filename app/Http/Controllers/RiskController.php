<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiskStoreRequest;
use App\Http\Requests\RiskUpdateRequest;
use App\Models\Component;
use App\Models\Risk;
use App\Models\Statement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RiskController extends Controller
{
    /**
     * Authorize user for a risk by determining if it was its creator
     *
     * No furthe desc is neccessary
     *
     * @param App\Models\User $user user to check
     * @param App\Models\Risk $risk risk to auth against
     * @return bool
     **/
    public function auth(User $user, Risk $risk)
    {
        return $risk->user->id == $user->id;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $risks = Risk::all();
        return view('models.risks.index', compact('risks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $components = Component::all()->makeVisible(['code_name'])->sortBy('code_name');
        return view('models.risks.create', compact('components'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(RiskStoreRequest $request)
    {
        //
        $data = $request->validated();
        $data['organisation_id'] = Auth::user()->organisation->id;
        $data['user_id'] = Auth::user()->id;
        Risk::create($data);
        return redirect()->route('risks.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Risk $risk
     * @return \Illuminate\Http\Response
     */
    public function show(Risk $risk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Risk $risk
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Risk $risk)
    {
        //
        if ($this->auth(Auth::user(), $risk)) {
            $components = Component::all()->makeVisible(['code_name'])->sortBy('code_name');
            return view('models.risks.edit', compact('risk', 'components'));
        } else {
            abort(403, 'Only the risk creator, ' . ucfirst($risk->user->role) . ': ' . $risk->user->name . ', is allowed to edit it');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Risk $risk
     * @return \Illuminate\Http\Response
     */
    public function update($locale, RiskUpdateRequest $request, Risk $risk)
    {
        //
        if ($this->auth(Auth::user(), $risk)) {
            $data = $request->validated();
            $data['organisation_id'] = Auth::user()->organisation->id;
            $data['user_id'] = Auth::user()->id;
            $risk->update($data);
            return redirect()->route('risks.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
        } else {
            abort(403, 'Only the risk creator, ' . ucfirst($risk->user->role) . ': ' . $risk->user->name . ', is allowed to update it');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Risk $risk
     * @return \Illuminate\Http\Response
     */
    public function destroy($locale, Risk $risk)
    {
        $result = $risk->delete();
        $data = $result ? ['success' => true, 'msg' => __('messages.delete_success')] : ['error' => true, 'msg' => __('messages.error')];

        return response()->json($data);
    }
}
