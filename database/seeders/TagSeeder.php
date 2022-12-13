<?php

namespace Database\Seeders;

use App\Models\Sanction;
use App\Models\SanctionTag;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oldTags = File::get(base_path('/resources/ADM_INDEX.json'));
        $oldTags = collect(json_decode($oldTags))->sortBy('Ord');

        $newTags = [];

        $oldTags->each(function ($oldTag) use (&$newTags) {
            $newTags[] = ['tag_se' => $oldTag->Ord, 'desc_se' => $oldTag->Beskrivning];
        });

        Tag::insertOrIgnore($newTags);

        $oldSanctionTags = File::get(base_path('/resources/SANKTION_INDEX.json'));
        $oldSanctionTags = collect(json_decode($oldSanctionTags))->groupBy('Sanktion_timestamp');
        $oldSanctions = File::get(base_path('/resources/SANKTION.json'));
        $oldSanctions = collect(json_decode($oldSanctions));
        $newSanctions = Sanction::all();


        $oldSanctionTags->each(function ($group, $key) use ($oldSanctions, $newSanctions, $oldTags) {
            $oldSanction = $oldSanctions->where('Sanktion_timestamp', $key)->first();

            if ($oldSanction) {
                $oldSanctionTitle = $oldSanction->sanktion_id_namn;
                $newSanction = $newSanctions->where('title', $oldSanctionTitle)->first();

                if ($newSanction) {
                    $tags = Tag::whereIn('tag_se', $oldTags->whereIn('Index_id', $group->pluck('Index_id'))->pluck('Ord')->all())
                        ->orderBy('tag_se')
                        ->get();

                    $newSanction->tags()->sync($tags);
                }
            }
        });
    }
}
