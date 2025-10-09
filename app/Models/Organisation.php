<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Organisation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $visible = ['id', 'name', 'turnover', 'employees', 'number', 'commitment', 'logofile', 'color', 'phone', 'address1', 'address2', 'email', 'website'];
    protected $appends = ['orgcolor', 'logo'];

    /**
     * Get all auditor statements from auditor_statement
     *
     * Undocumented function long description
     *
     * @return Collection auditor statements
     **/
    public function auditorStatements(Action $action)
    {
        // Get all actionables with the given action_id
        $actionables = DB::table('actionables')
            ->where('action_id', $action->id)
            ->get();

        // Convert the actionables to Eloquent models (Statement and Component models)
        $actionableIds = $actionables->pluck('actionable_id')->toArray();
        $actionableType = $actionables->pluck('actionable_type')->toArray();
        $actionableModels = [];
        foreach ($actionableType as $index => $type) {
            $modelClass = $type === 'Statement' ? Statement::class : Component::class;
            $actionableModels[] = $modelClass::find($actionableIds[$index]);
        }
        $actionableModels = collect($actionableModels)->filter(); // Remove any null values

        // Get all statements
        $statements = Statement::all();

        // Get all users of this org
        $orgUsers = $this->users->pluck('id');

        // Get all planned statements by finding the ones in the auditor statements
        $plannedStatements = DB::table('auditor_statement')->whereIn('user_id', $orgUsers)->get();

        // Find the unplanned statements
        $unplannedStatements = $statements->filter(function ($statement) use ($plannedStatements) {
            return !$plannedStatements->contains('statement_id', $statement->id);
        });

        // Group the planned statements by their plan_id
        $plannedStatementsGrouped = $plannedStatements->groupBy('plan_id');
        // Prepare the 'planned' and 'remaining' subcollections for each plan ID
        $planned = collect();
        $allPlanIds = Plan::all()->pluck('id')->toArray();
        foreach ($allPlanIds as $planId) {
            // Skip the iteration for plan_id 4
            if ($planId === 4) {
                continue;
            }

            $plannedStatementsForPlan = $plannedStatementsGrouped[$planId] ?? collect();
            $filteredStatements = $statements->filter(function ($statement) use ($actionableModels) {
                // Check if the statement or its parent component is found in actionables
                return $actionableModels->contains(function ($actionableModel) use ($statement) {
                    return $actionableModel->getKey() === $statement->id
                        || ($actionableModel instanceof Component && $actionableModel->getKey() === $statement->component_id);
                });
            });

            // Convert plannedStatementsForPlan to Eloquent models
            $plannedStatementsForPlan = $plannedStatementsForPlan->map(function ($item) {
                return Statement::find($item->statement_id);
            });

            // Filter planned statements based on the intersection
            $plannedStatementsWithActionable = $plannedStatementsForPlan->intersect($filteredStatements);

            // Filter remaining planned statements
            $remainingPlannedStatements = $plannedStatementsForPlan->diff($plannedStatementsWithActionable);

            // Calculate the percentage
            $totalPlannedStatements = $plannedStatementsWithActionable->count() + $remainingPlannedStatements->count();
            $percentage = $totalPlannedStatements > 0 ? $plannedStatementsWithActionable->count() / $totalPlannedStatements : 0;

            // Determine the class based on the percentage
            if ($percentage < 0.33) {
                $class = 'progress progress-bar-danger';
            } elseif ($percentage < 1) {
                $class = 'progress progress-bar-warning';
            } else {
                $class = 'progress progress-bar-success';
            }

            // Add 'this', 'remaining', 'percentage', 'thisCount', 'remainingCount', 'totalCount', and 'class' keys to the planned collection
            $planned[$planId] = [
                'this' => $plannedStatementsWithActionable,
                'remaining' => $remainingPlannedStatements,
                'percentage' => round($percentage * 100), // Convert to percentage and round to whole number
                'thisCount' => $plannedStatementsWithActionable->count(),
                'remainingCount' => $remainingPlannedStatements->count(),
                'totalCount' => $plannedStatementsWithActionable->count() + $remainingPlannedStatements->count(),
                'class' => $class,
            ];
        }

        // Calculate the completion details for all planned statements
        $reviewedCount = Review::whereIn('statement_id', $planned->pluck('this')->flatten()->pluck('id'))->count();
        $allCount = $planned->pluck('totalCount')->sum();
        $completionPercentage = $allCount > 0 ? round(($reviewedCount / $allCount) * 100) : 0;

        // Return the final result with both 'planned' and 'unplanned' keys, along with the 'completion' key
        return [
            'planned' => $planned,
            'unplanned' => $unplannedStatements,
            'completion' => [
                'reviewedCount' => $reviewedCount,
                'allCount' => $allCount,
                'percentage' => $completionPercentage,
            ],
        ];
    }







    public function components()
    {
        return $this->belongsToMany(Component::class)->withPivot('period_id');
    }

    public function logo(): Attribute
    {
        $logo = $this->logofile;
        if (!($logo)) {
            $logo = '/images/portrait/small/it.png';
        } else {
            $logo = Storage::url($logo);
        }
        return new Attribute(
            get: fn ($value) => $logo
        );
    }

    public function kpis()
    {
        $kpis = Kpi::all();
        foreach ($kpis as $kpi) {
            $kpi->kpicomments = $kpi->org_kpicomments($this);
            $kpi->kpicomment = $kpi->kpicomments->last();
            // chart data
            $t = [];
            $v = [];
            foreach ($kpi->kpicomments as $comment) {
                $t[] = ['x' => Carbon::parse($comment->created_at)->format('Y-m-d'), 'y' => $comment->target, 'user' => $comment->user->name . ' [' . $comment->user->role . ']', 'comment' => $comment->comment];
                $v[] = ['x' => Carbon::parse($comment->created_at)->format('Y-m-d'), 'y' => $comment->value, 'user' => $comment->user->name . ' [' . $comment->user->role . ']', 'comment' => $comment->comment];
            }
            $kpi->targets = $t;
            $kpi->values = $v;
        }
        return $kpis;
    }

    public function organisations()
    {
        return $this->hasMany(Organisation::class);
    }

    public function orgcolor(): Attribute
    {
        $color = $this->color;
        if (!($color)) {
            $color = '00315c';
        }
        return new Attribute(
            get: fn ($value) => $color
        );
    }

    public function sni()
    {
        return $this->belongsTo(Sni::class);
    }

    public function risks()
    {
        return $this->hasMany(Risk::class);
    }

    public function statements()
    {
        return $this->belongsToMany(Statement::class)->withPivot('implementation')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function onboardingTemplate()
    {
        return $this->belongsTo(Template::class, 'onboarding_template_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function usersOnly()
    {
        return DB::table('users')->where('organisation_id', $this->id)->where('role', 'user')->get();
    }

    public function deedsYears()
    {
        $years = [];
        $deeds = Deed::where('organisation_id', $this->id)->get();
        foreach ($deeds as $deed) {
            $year = Carbon::parse($deed->created_at)->format('Y');
            if (!(in_array($year, $years))) {
                $years[] = $year;
            }
        }
        return $years;
    }
}
