<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'requires_existing_gdpr' => 'boolean',
        'is_active' => 'boolean',
        'estimated_months' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the tasks associated with this template.
     */
    public function templateTasks()
    {
        return $this->hasMany(TemplateTask::class)->orderBy('sort_order');
    }

    /**
     * Get the statements associated with this template.
     */
    public function statements()
    {
        return $this->belongsToMany(Statement::class, 'template_statements')
            ->withPivot('sort_order')
            ->orderBy('sort_order')
            ->withTimestamps();
    }

    /**
     * Get the configs that use this template.
     */
    public function configs()
    {
        return $this->belongsToMany(Config::class);
    }

    /**
     * Scope to get only active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get templates recommended for the given criteria.
     */
    public static function getRecommended($organizationType = null, $organizationSize = null, $hasExistingGdpr = false)
    {
        $query = self::active()
            ->has('templateTasks') // Only templates with tasks
            ->orderBy('sort_order');

        if ($organizationType) {
            $query->where(function($q) use ($organizationType) {
                $q->where('organization_type', $organizationType)
                  ->orWhereNull('organization_type');
            });
        }

        if ($organizationSize) {
            $query->where(function($q) use ($organizationSize) {
                $q->where('organization_size', $organizationSize)
                  ->orWhereNull('organization_size');
            });
        }

        if (!$hasExistingGdpr) {
            $query->where('requires_existing_gdpr', false);
        }

        return $query->get();
    }
}
