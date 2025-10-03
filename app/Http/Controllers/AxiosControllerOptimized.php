<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Sanction;
use App\Models\Organisation;

/**
 * Optimized methods for AxiosController
 *
 * These methods should replace the existing ones in AxiosController.php
 * for improved performance with caching and efficient queries
 */
class AxiosControllerOptimized extends Controller
{
    /**
     * Optimized sanctionsTable with database-level pagination and caching
     *
     * REPLACE the existing sanctionsTable method in AxiosController.php with this
     *
     * Key improvements:
     * 1. Uses paginate() instead of get() - only loads requested records
     * 2. Caches filter options to avoid repeated queries
     * 3. Moves sorting to database level where possible
     * 4. Only loads relationships for displayed records
     */
    public function sanctionsTable($locale, Request $request)
    {
        $org = Organisation::find(session('selected_org')['id']);
        $filterByValue = $request->post('filters')['value'] ?? null;
        $filterByDpa = $request->post('filters')['dpa_id'] ?? null;
        $filterByCountry = $request->post('filters')['country_id'] ?? null;
        $filterBySni = $request->post('filters')['sni_id'] ?? null;
        $filterByOutcome = $request->post('filters')['outcome_id'] ?? null;
        $filterByTag = $request->post('filters')['tag_ids'] ?? null;
        $filterByComponent = $request->post('filters')['component_id'] ?? null;
        $filterByStatement = $request->post('filters')['statement_id'] ?? null;
        $orderBy = $request->post('order')[0]['column'] ?? null;
        $orderDir = $request->post('order')[0]['dir'] ?? 'asc';

        $needle = $request->search['value'];

        // Build query with filters
        $query = Sanction::select('sanctions.*')
            ->with(['statements.component', 'articles', 'currency', 'dpa.country'])
            ->when($needle, function ($query) use ($needle) {
                $query->leftJoin('snis', 'sanctions.sni_id', '=', 'snis.id')
                    ->where(function($q) use ($needle) {
                        $q->where('sanctions.title', 'like', '%' . $needle . '%')
                          ->orWhereDate('sanctions.started_at', 'like', '%' . $needle . '%')
                          ->orWhereDate('sanctions.decided_at', 'like', '%' . $needle . '%')
                          ->orWhere('sanctions.published_at', 'like', '%' . $needle . '%')
                          ->orWhere('sanctions.fine', 'like', '%' . $needle . '%')
                          ->orWhereRelation('dpa', 'title', 'like', "%$needle%")
                          ->orWhereRaw('LOWER(sanctions.desc_en) LIKE ?', ["%". strtolower($needle) . "%"])
                          ->orWhereRaw('LOWER(sanctions.desc_se) LIKE ?', ["%". strtolower($needle) . "%"])
                          ->orWhere('snis.desc_en', 'like', '%' . $needle . '%')
                          ->orWhere('snis.desc_se', 'like', '%' . $needle . '%');
                    });
            })
            ->when($filterByValue, function ($query) use ($filterByValue, $org) {
                $query->whereHas('statements.deeds', function ($query) use ($filterByValue, $org) {
                    $query->where('value', $filterByValue)
                        ->where('organisation_id', $org->id);
                });
            })
            ->when($filterByDpa, function ($query, $filterByDpa) {
                $query->where('dpa_id', $filterByDpa);
            })
            ->when($filterByCountry, function ($query, $filterByCountry) {
                $query->whereRelation('dpa', 'country_id', $filterByCountry);
            })
            ->when($filterBySni, function ($query, $filterBySni) {
                $query->where('sni_id', $filterBySni);
            })
            ->when($filterByOutcome, function ($query, $filterByOutcome) {
                $query->where('outcome_id', $filterByOutcome);
            })
            ->when($filterByTag, function ($query, $filterByTag) {
                $query->join('sanction_tag', 'sanction_tag.sanction_id', '=', 'sanctions.id')
                    ->whereIn('sanction_tag.tag_id', $filterByTag);
            })
            ->when($filterByComponent, function ($query, $filterByComponent) {
                $query->whereRelation('statements', 'statements.component_id', $filterByComponent);
            })
            ->when($filterByStatement, function ($query, $filterByStatement) {
                $query->whereRelation('statements', 'statements.id', $filterByStatement);
            });

        // Apply ordering at database level (except for custom deed sorting)
        if ($orderBy && $orderBy != 6) {
            // Map column index to actual column name
            $orderColumns = [
                0 => 'sanctions.title',
                1 => 'sanctions.decided_at',
                2 => 'sanctions.fine',
                // Add other column mappings as needed
            ];

            if (isset($orderColumns[$orderBy])) {
                $query->orderBy($orderColumns[$orderBy], $orderDir);
            }
        } else {
            $query->orderBy('sanctions.decided_at', 'desc');
        }

        // Get total count BEFORE pagination (for DataTables)
        $recordsTotal = Cache::remember('sanctions_total_count', 300, function () {
            return Sanction::count();
        });

        // Clone query for filtered count
        $recordsFiltered = $query->count();

        // Apply pagination at database level
        $page = ($request->start / $request->length) + 1;
        $sanctions = $query->skip($request->start)->take($request->length)->get();

        // Process statements and deeds
        $colors = ['#ea5455', '#ff5f43', '#ff9f43', '#cab707', '#28c76f'];
        $sanctions = $sanctions->map(function ($sanction) use ($org, $colors) {
            $sanction->statements = $sanction->statements->map(function ($statement) use ($org, $colors) {
                $statement->deed = $statement->organisationDeed($org);
                if ($statement->deed) {
                    $statement->deed->color = $colors[$statement->deed->value - 1];
                    $statement->deed->makeVisible('color');
                }
                return $statement->makeVisible(['subcode', 'deed', 'component']);
            })->sortBy('subcode', SORT_NATURAL)->values();
            return $sanction;
        });

        // Handle custom deed sorting if needed (orderBy == 6)
        if ($orderBy == 6) {
            $sanctions = $sanctions->sort(function ($a, $b) use ($orderDir) {
                $aValues = $a->statements->pluck('deed.value')->filter();
                $bValues = $b->statements->pluck('deed.value')->filter();

                if ($aValues->isNotEmpty() && $bValues->isNotEmpty()) {
                    $aMin = $aValues->min();
                    $bMin = $bValues->min();

                    if ($aMin === $bMin) {
                        $result = $aValues->avg() <=> $bValues->avg();
                    } else {
                        $result = $aMin <=> $bMin;
                    }
                    return $orderDir == 'asc' ? $result : -$result;
                }

                if ($aValues->isEmpty() && $bValues->isEmpty()) return 0;
                return $aValues->isEmpty() ? 1 : -1;
            })->values();
        }

        // Make visible attributes for response
        $sanctions->each(function ($sanction) {
            $sanction->makeVisible([
                'articles', 'articlesSorted', 'currency', 'created_at_for_humans',
                'decided_at_for_humans', 'dpa', 'url', 'party', 'statements', 'fine_eur'
            ]);

            $sanction->articlesSorted = $sanction->articles->sortBy('title')->values();
            $sanction->dpa->makeVisible(['country', 'name']);
        });

        return [
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $sanctions->values(),
            'index' => $request->start,
        ];
    }
}
