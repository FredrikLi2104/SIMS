<?php

namespace Database\Seeders;

use App\Models\Sanction;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SanctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // first load the file as object
        $oldSanctions = File::get(base_path('/resources/SANKTION.json'));
        $oldSanctions = json_decode($oldSanctions);
        $sanctions = Sanction::all()->load(['dpa'])->sortBy('id');
        foreach ($oldSanctions as $oldSanction) {
            try {
                // find an existing sanction that matches this old sanction
            $match = $sanctions->filter(function ($item) use ($oldSanction) {
                $filtered = false;
                if (strtolower($item->title) == strtolower($oldSanction->sanktion_id_namn)) {
                    $filtered = true;
                }
                return $filtered;
            })->first();
            if ($match) {
                $desc_en = '{"ops":[{"insert":"'.$oldSanction->Beskrivning_eng.'"}]}';
                $desc_se = '{"ops":[{"insert":"'.$oldSanction->Beskrivning_sve.'"}]}';
                $match->update(['desc_en' => $desc_en, 'desc_se' => $desc_se]);
                //var_dump([$oldSanction->Sanktion_db_id, $match->id, $oldSanction->DPA_Abbrevation, $match->dpa->title, $oldSanction->sanktion_id_namn, $match->title]);
            }
            } catch (\Throwable $th) {
                //throw $th;
                var_dump('Sanction '.$oldSanction->sanktion_id_namn.' Failed');
            }
        }
    }
}
