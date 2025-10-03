<?php

namespace App\Http\Controllers;

use App\Http\Requests\IssueCategoryStoreRequest;
use App\Http\Requests\IssueCategoryUpdateRequest;
use App\Models\IssueCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class IssueCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['issue_categories'] = IssueCategory::all();

        return view('models/issue_categories/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $data['action_url'] = route('issue_categories.store', App::currentLocale());
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.issue_category') . ' ' . __('messages.create');

        return view('models/issue_categories/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(IssueCategoryStoreRequest $request)
    {
        $data = $request->validated();
        IssueCategory::create($data);

        return redirect()->route('issue_categories.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\IssueCategory $issueCategory
     * @return \Illuminate\Http\Response
     */
    public function show(IssueCategory $issueCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\IssueCategory $issueCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, IssueCategory $issueCategory)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['action_url'] = route('issue_categories.update', [App::currentLocale(), $issueCategory->id]);
        $data['issue_category'] = $issueCategory;
        $data['action_msg'] = trans('messages.edit');
        $data['title'] = $data['title'] = trans('messages.issue_categories') . ' ' . trans('messages.edit');

        return view('models/issue_categories/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IssueCategory $issueCategory
     * @return \Illuminate\Http\Response
     */
    public function update(IssueCategoryUpdateRequest $request, $locale, IssueCategory $issueCategory)
    {
        $data = $request->validated();
        $issueCategory->update($data);

        return redirect()->route('issue_categories.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\IssueCategory $issueCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(IssueCategory $issueCategory)
    {
        //
    }
}
