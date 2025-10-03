<?php

namespace Database\Seeders;

use App\Models\IssueCategory;
use App\Models\Sanction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class IssueCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        $oldSanctions = File::get(base_path('/resources/SANKTION.json'));
        $oldSanctions = collect(json_decode($oldSanctions));

        $oldTypes = $oldSanctions->pluck('Type');

        $oldTypes = $oldTypes->map(function ($type) {
            return strtolower(trim($type));
        });

        $oldTypes = $oldTypes->filter()->unique()->sort()->values()->map(function ($oldType) {
            return ucwords($oldType);
        })->all();

        $newTypes = [];

        foreach ($oldTypes as $oldType) {
            $newTypes[] = [
                'desc_en' => $oldType,
                'desc_se' => null
            ];
        }

        IssueCategory::insertOrIgnore($newTypes);

        $newSanctions = Sanction::all();

        $oldSanctions->each(function ($oldSanction) use ($newSanctions) {
            $oldSanctionTitle = $oldSanction->sanktion_id_namn;
            $newSanction = $newSanctions->where('title', $oldSanctionTitle)->first();

            if ($newSanction) {
                $oldSanctionType = ucwords(trim($oldSanction->Type));
                $newType = IssueCategory::where('desc_en', $oldSanctionType)->first();

                if ($newType) {
                    $newSanction->update(['issue_category_id' => $newType['id']]);
                }
            }
        });
        */
    }
}
