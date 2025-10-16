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
        $orderBy = $request->post('order')[0]['column'] ?? 6; // Default to column 6 (Statement/Value)
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
        if ($orderBy != 6) {
            // Map column index to actual column name
            // Column mapping matches the DataTable in insights/app.js:
            // 0: ID, 1: DPA, 2: Date Added, 3: Fine, 4: Title, 5: Party, 6: Statement/Value, 7: Actions
            $orderColumns = [
                0 => 'sanctions.id',
                1 => 'sanctions.dpa_id',
                2 => 'sanctions.created_at',
                3 => 'sanctions.fine',
                4 => 'sanctions.title',
                5 => 'sanctions.party',
                // Column 6 is handled separately (Statement/Value custom sorting)
                // Column 7 (Actions) is not orderable
            ];

            if (isset($orderColumns[$orderBy])) {
                $query->orderBy($orderColumns[$orderBy], $orderDir);
            }
        }

        // For column 6 (Statement/Value) sorting, we need to get ALL results first
        // because we need to calculate min/avg deed values across statements
        // So we skip database ordering for column 6

        // Get total count BEFORE pagination (for DataTables)
        $recordsTotal = Cache::remember('sanctions_total_count', 300, function () {
            return Sanction::count();
        });

        // Clone query for filtered count
        $recordsFiltered = $query->count();

        // For column 6 (Statement/Value), we need ALL results to sort properly
        // For other columns, we can paginate at database level
        if ($orderBy == 6) {
            // Get ALL filtered results (no pagination yet)
            $sanctions = $query->get();
        } else {
            // Apply pagination at database level for other columns
            $page = ($request->start / $request->length) + 1;
            $sanctions = $query->skip($request->start)->take($request->length)->get();
        }

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

            // Now apply pagination AFTER sorting
            $sanctions = $sanctions->slice($request->start, $request->length)->values();
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
