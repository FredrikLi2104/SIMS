<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\Organisation;
use App\Models\Task;
use App\Models\Template;
use App\Models\TemplateTask;
use App\Models\TaskStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    /**
     * Get organisations for the dropdown.
     */
    public function getOrganisations($locale)
    {
        $user = Auth::user();

        // If user is super admin or auditor, get all organisations
        // Otherwise get only user's organisation
        if ($user->role === 'super' || $user->role === 'auditor') {
            $organisations = Organisation::select('id', 'name')->orderBy('name')->get();
        } else {
            $organisations = Organisation::where('id', $user->organisation_id)->select('id', 'name')->get();
        }

        return response()->json([
            'organisations' => $organisations
        ]);
    }

    /**
     * Get onboarding questions for the wizard.
     */
    public function getQuestions($locale)
    {
        return response()->json([
            'questions' => [
                [
                    'id' => 'organization_type',
                    'question_se' => 'Vilken typ av organisation är det?',
                    'question_en' => 'What type of organization is this?',
                    'type' => 'select',
                    'options' => [
                        ['value' => 'public', 'label_se' => 'Offentlig myndighet', 'label_en' => 'Public authority'],
                        ['value' => 'private', 'label_se' => 'Privat bolag', 'label_en' => 'Private company'],
                        ['value' => 'nonprofit', 'label_se' => 'Ideell organisation', 'label_en' => 'Non-profit organization'],
                        ['value' => 'other', 'label_se' => 'Annat', 'label_en' => 'Other'],
                    ],
                ],
                [
                    'id' => 'organization_size',
                    'question_se' => 'Hur stor är organisationen?',
                    'question_en' => 'How large is the organization?',
                    'type' => 'select',
                    'options' => [
                        ['value' => 'small', 'label_se' => 'Liten (1-50 anställda)', 'label_en' => 'Small (1-50 employees)'],
                        ['value' => 'medium', 'label_se' => 'Mellan (51-250 anställda)', 'label_en' => 'Medium (51-250 employees)'],
                        ['value' => 'large', 'label_se' => 'Stor (250+ anställda)', 'label_en' => 'Large (250+ employees)'],
                    ],
                ],
                [
                    'id' => 'has_existing_gdpr',
                    'question_se' => 'Har ni ett befintligt dataskyddsarbete på plats?',
                    'question_en' => 'Do you have existing data protection work in place?',
                    'type' => 'boolean',
                    'options' => [
                        ['value' => true, 'label_se' => 'Ja', 'label_en' => 'Yes'],
                        ['value' => false, 'label_se' => 'Nej', 'label_en' => 'No'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Get recommended templates based on answers.
     */
    public function getRecommendations(Request $request, $locale)
    {
        $organizationType = $request->input('organization_type');
        $organizationSize = $request->input('organization_size');
        $hasExistingGdpr = $request->input('has_existing_gdpr', false);

        $templates = Template::getRecommended($organizationType, $organizationSize, $hasExistingGdpr);

        // Load template tasks for each template
        $templates->load(['templateTasks' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        return response()->json([
            'templates' => $templates->map(function ($template) use ($locale) {
                return [
                    'id' => $template->id,
                    'name' => $template->{"name_$locale"},
                    'description' => $template->{"desc_$locale"},
                    'summary' => $template->{"summary_$locale"},
                    'organization_type' => $template->organization_type,
                    'organization_size' => $template->organization_size,
                    'estimated_months' => $template->estimated_months,
                    'task_count' => $template->templateTasks->count(),
                    'tasks_preview' => $template->templateTasks->take(5)->map(function ($task) use ($locale) {
                        return [
                            'title' => $task->{"title_$locale"},
                            'description' => $task->{"desc_$locale"},
                            'offset_days' => $task->offset_days,
                            'duration_days' => $task->duration_days,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Apply a template to an organisation.
     */
    public function applyTemplate(Request $request, $locale, Organisation $organisation)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
        ]);

        $template = Template::with(['templateTasks', 'statements'])->findOrFail($request->template_id);

        DB::beginTransaction();

        try {
            $createdBy = Auth::id();
            $createdTasks = [];
            $activatedStatements = [];
            $userRole = Auth::user()->role;

            // Create tasks from template
            foreach ($template->templateTasks as $templateTask) {
                $startDate = Carbon::now()->addDays($templateTask->offset_days);
                $endDate = $startDate->copy()->addDays($templateTask->duration_days);

                $task = Task::create([
                    'organisation_id' => $organisation->id,
                    'title_en' => $templateTask->title_en,
                    'title_se' => $templateTask->title_se,
                    'desc_en' => $this->convertToQuillFormat($templateTask->desc_en),
                    'desc_se' => $this->convertToQuillFormat($templateTask->desc_se),
                    'start' => $startDate,
                    'end' => $endDate,
                    'hours' => $templateTask->hours,
                    'task_status_id' => $templateTask->task_status_id,
                    'created_by' => $createdBy,
                ]);

                // Create corresponding action
                Action::create([
                    'task_id' => $task->id,
                    'action_type_id' => $this->getActionTypeId($templateTask->action_type ?? 'plan', $userRole),
                    'action_status_id' => 1, // Pending
                ]);

                $createdTasks[] = $task;
            }

            // Activate statements from template
            if ($template->statements->isNotEmpty()) {
                foreach ($template->statements as $statement) {
                    // Check if statement is not already activated for this organisation
                    if (!$organisation->statements()->where('statement_id', $statement->id)->exists()) {
                        $organisation->statements()->attach($statement->id, [
                            'implementation' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $activatedStatements[] = $statement;
                    }
                }
            }

            // Save which template was used and when
            $organisation->onboarding_template_id = $template->id;
            $organisation->onboarding_completed_at = now();
            $organisation->save();

            DB::commit();

            Log::info("Template {$template->id} applied to organisation {$organisation->id}", [
                'template_name' => $template->{"name_$locale"},
                'tasks_created' => count($createdTasks),
                'statements_activated' => count($activatedStatements),
                'organisation' => $organisation->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully applied template: {$template->{"name_$locale"}}",
                'tasks_created' => count($createdTasks),
                'statements_activated' => count($activatedStatements),
                'organisation_id' => $organisation->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to apply template {$template->id} to organisation {$organisation->id}", [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply template',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete onboarding process for an organisation.
     * This can be called after organisation creation to set up initial data.
     */
    public function completeOnboarding(Request $request, $locale)
    {
        $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'template_id' => 'required|exists:templates,id',
            'answers' => 'array',
        ]);

        $organisation = Organisation::findOrFail($request->organisation_id);

        // Apply the selected template
        $applyRequest = new Request(['template_id' => $request->template_id]);
        return $this->applyTemplate($applyRequest, $locale, $organisation);
    }

    /**
     * Convert plain text to Quill Delta format.
     * Quill expects: {"ops":[{"insert":"text\n"}]}
     */
    private function convertToQuillFormat($text)
    {
        if (empty($text)) {
            return null;
        }

        // Check if already in Quill format
        if (is_string($text) && str_starts_with($text, '{"ops":[')) {
            return $text;
        }

        // Convert plain text to Quill Delta JSON format
        $delta = [
            'ops' => [
                [
                    'insert' => $text . "\n"
                ]
            ]
        ];

        return json_encode($delta);
    }

    /**
     * Map template action_type to action_type_id based on user role.
     */
    private function getActionTypeId($actionType, $userRole)
    {
        // Map template action types to actual action type IDs
        // Based on the action_types table and user role

        $mapping = [
            'plan' => [
                'user' => 1,     // Planera datasyddsarbetet [komponenter]
                'auditor' => 4,  // Planera granskning [komponenter]
            ],
            'do' => [
                'user' => 2,     // Arbeta med dataskydd [komponenter]
                'auditor' => 7,  // Genomför granskning [komponenter]
            ],
            'review' => [
                'user' => 2,     // Arbeta med dataskydd [komponenter] (fallback)
                'auditor' => 7,  // Genomför granskning [komponenter]
            ],
        ];

        $actionType = strtolower($actionType);

        // Default to 'plan' if action_type not recognized
        if (!isset($mapping[$actionType])) {
            $actionType = 'plan';
        }

        // Get appropriate action_type_id for user role
        if ($userRole === 'auditor' || $userRole === 'super') {
            return $mapping[$actionType]['auditor'];
        }

        return $mapping[$actionType]['user'];
    }
}
