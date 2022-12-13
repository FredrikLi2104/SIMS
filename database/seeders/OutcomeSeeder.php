<?php

namespace Database\Seeders;

use App\Models\Outcome;
use App\Models\Sanction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class OutcomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oldSanctions = File::get(base_path('/resources/SANKTION.json'));
        $oldSanctions = collect(json_decode($oldSanctions));

        $oldOutcomes = $oldSanctions->pluck('Outcome')->unique();

        $oldOutcomes = $oldOutcomes->filter(function ($outcome) {
            return $outcome !== '00:00:00.000' && $outcome !== null;
        });

        $oldOutcomes = $oldOutcomes->map(function ($outcome) {
            return trim($outcome);
        });

        $oldOutcomes = $oldOutcomes->sort()->values()->all();
        $newOutcomes = [];

        foreach ($oldOutcomes as $oldOutcome) {
            $newOutcomes[] = [
                'desc_en' => $oldOutcome,
                'desc_se' => null
            ];
        }

        Outcome::insertOrIgnore($newOutcomes);

        $newSanctions = Sanction::all();

        $oldSanctions->each(function ($oldSanction) use ($newSanctions) {
            $oldSanctionTitle = $oldSanction->sanktion_id_namn;
            $newSanction = $newSanctions->where('title', $oldSanctionTitle)->first();

            if ($newSanction) {
                $oldSanctionOutcome = trim($oldSanction->Outcome);
                $newOutcome = Outcome::where('desc_en', $oldSanctionOutcome)->first();

                if ($newOutcome) {
                    $newSanction->update(['outcome_id' => $newOutcome['id']]);
                }
            }
        });
    }
}
