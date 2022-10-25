<?php

namespace Database\Seeders;

use App\Models\Sanction;
use App\Models\Sni;
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
        /*
        // update my local snis to server snis
        $serverSnis = File::get(base_path('/resources/snis.json'));
        $serverSnis = json_decode($serverSnis);
        foreach ($serverSnis as $serverSni) {
            try {
                Sni::create(['code' => $serverSni->code, 'desc_en' => $serverSni->desc_en, 'desc_se' => $serverSni->desc_se, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            } catch (\Throwable $th) {
                //throw $th;
                var_dump('Sni ' . $serverSni->code . ' Failed. Reason: ' . $th->getMessage());
            }

            # code...
        }
        */
        // first load the file as object
        $oldSanctions = File::get(base_path('/resources/SANKTION.json'));
        $oldSnis = File::get(base_path('/resources/ADM_SEKTOR.json'));
        $oldSanctions = json_decode($oldSanctions);
        $oldSnis = json_decode($oldSnis);
        $oldSnis = collect($oldSnis);
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
                    // descs
                    $en = str_replace(array(
                        '\'', '"',
                        ',', ';', '<', '>'
                    ), ' ', $oldSanction->Beskrivning_eng);
                    $se = str_replace(array(
                        '\'', '"',
                        ',', ';', '<', '>'
                    ), ' ', $oldSanction->Beskrivning_sve);
                    $desc_en = '{"ops":[{"insert":"' .$en. '"}]}';
                    $desc_se = '{"ops":[{"insert":"' .$se. '"}]}';
                    $sni_id = null;
                    // sni
                    $oldSniId = $oldSanction->Sektor;
                    $oldSni = $oldSnis->filter(function ($j) use ($oldSniId) {
                        return $j->sektor_id == $oldSniId;
                    })->first();
                    if ($oldSni) {
                        $oldSniCode = $oldSni->Avdelning;
                        $sni = Sni::where('code', $oldSniCode)->first();
                        $sni_id = $sni->id;
                    }
                    $match->update(['desc_en' => $desc_en, 'desc_se' => $desc_se, 'sni_id' => $sni_id]);
                }
            } catch (\Throwable $th) {
                //throw $th;
                var_dump('Sanction ' . $oldSanction->sanktion_id_namn . ' Failed. Reason: ' . $th->getMessage());
            }
        }
    }
}
