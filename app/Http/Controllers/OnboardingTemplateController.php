<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TaskStatus;
use Illuminate\Http\Request;

class OnboardingTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($locale)
    {
        $templates = Template::with(['templateTasks'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('onboarding-templates.index', compact('templates', 'locale'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        $taskStatuses = TaskStatus::all();
        return view('onboarding-templates.create', compact('taskStatuses', 'locale'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $locale)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_se' => 'required|string|max:255',
            'summary_en' => 'nullable|string',
            'summary_se' => 'nullable|string',
            'organization_type' => 'nullable|string',
            'organization_size' => 'nullable|string',
            'requires_existing_gdpr' => 'boolean',
            'estimated_months' => 'nullable|integer',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Add default empty strings for nullable fields to avoid SQL errors
        $validated['desc_en'] = $validated['desc_en'] ?? '';
        $validated['desc_se'] = $validated['desc_se'] ?? '';
        $validated['summary_en'] = $validated['summary_en'] ?? '';
        $validated['summary_se'] = $validated['summary_se'] ?? '';

        $template = Template::create($validated);

        return redirect()->route('onboarding-templates.index', ['locale' => $locale])
            ->with('success', 'Template created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($locale, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, $id)
    {
        $template = Template::with('templateTasks')->findOrFail($id);
        $taskStatuses = TaskStatus::all();
        return view('onboarding-templates.edit', compact('template', 'taskStatuses', 'locale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $locale, $id)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_se' => 'required|string|max:255',
            'summary_en' => 'nullable|string',
            'summary_se' => 'nullable|string',
            'organization_type' => 'nullable|string',
            'organization_size' => 'nullable|string',
            'requires_existing_gdpr' => 'boolean',
            'estimated_months' => 'nullable|integer',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $template = Template::findOrFail($id);
        $template->update($validated);

        return redirect()->route('onboarding-templates.edit', ['locale' => $locale, 'onboarding_template' => $id])
            ->with('success', app()->getLocale() === 'en' ? 'Template updated successfully' : 'Mall uppdaterad framgångsrikt');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($locale, $id)
    {
        $template = Template::findOrFail($id);
        $template->delete();

        return redirect()->route('onboarding-templates.index', ['locale' => $locale])
            ->with('success', app()->getLocale() === 'en' ? 'Template deleted successfully' : 'Mall raderad framgångsrikt');
    }
}
