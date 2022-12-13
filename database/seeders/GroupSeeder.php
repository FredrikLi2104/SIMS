<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Sanction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GroupSeeder extends Seeder
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

        $oldGroups = $oldSanctions->pluck('Gruppering')->filter()->unique()->sort()->values()->all();

        $newGroups = [];

        foreach ($oldGroups as $oldGroup) {
            $newGroups[] = [
                'desc_en' => $oldGroup,
                'desc_se' => null
            ];
        }

        Group::insertOrIgnore($newGroups);

        $newSanctions = Sanction::all();

        $oldSanctions->each(function ($oldSanction) use ($newSanctions) {
            $oldSanctionTitle = $oldSanction->sanktion_id_namn;
            $newSanction = $newSanctions->where('title', $oldSanctionTitle)->first();

            if ($newSanction) {
                $oldSanctionGroup = $oldSanction->Gruppering;
                $newGroup = Group::where('desc_en', $oldSanctionGroup)->first();

                if ($newGroup) {
                    $newSanction->update(['group_id' => $newGroup['id']]);
                }
            }
        });
    }
}
