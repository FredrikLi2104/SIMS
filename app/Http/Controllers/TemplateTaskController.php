<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TemplateTask;
use Illuminate\Http\Request;

class TemplateTaskController extends Controller
{
    /**
     * Store a newly created template task.
     */
    public function store(Request $request, $locale, $templateId)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_se' => 'required|string|max:255',
            'desc_en' => 'nullable|string',
            'desc_se' => 'nullable|string',
            'offset_days' => 'required|integer|min:0',
            'duration_days' => 'required|integer|min:1',
            'hours' => 'required|numeric|min:0',
            'task_status_id' => 'required|exists:task_statuses,id',
            'action_type' => 'nullable|string|in:plan,do,review',
            'sort_order' => 'required|integer|min:0',
        ]);

        $template = Template::findOrFail($templateId);

        $validated['template_id'] = $template->id;
        TemplateTask::create($validated);

        return redirect()->route('onboarding-templates.edit', ['locale' => $locale, 'onboarding_template' => $templateId])
            ->with('success', app()->getLocale() === 'en' ? 'Task added successfully' : 'Uppgift tillagd framgångsrikt');
    }

    /**
     * Remove the specified template task.
     */
    public function destroy($locale, $templateId, $taskId)
    {
        $task = TemplateTask::where('template_id', $templateId)->findOrFail($taskId);
        $task->delete();

        return redirect()->route('onboarding-templates.edit', ['locale' => $locale, 'onboarding_template' => $templateId])
            ->with('success', app()->getLocale() === 'en' ? 'Task deleted successfully' : 'Uppgift raderad framgångsrikt');
    }
}
