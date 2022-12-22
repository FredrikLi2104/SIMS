<?php

namespace Database\Seeders;

use App\Models\Sanction;
use App\Models\SanctionStatement;
use App\Models\Statement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SanctionStatementSeeder extends Seeder
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
        $oldComponents = File::get(base_path('/resources/SANKTION_KOMPONENT.json'));
        $oldComponents = collect(json_decode($oldComponents));
        $newSanctions = Sanction::all();

        $oldComponents->each(function ($component) use ($oldSanctions, $newSanctions) {
            list($code, $subCode) = explode('.', $component->Pastaende_total);

            $statement = Statement::whereRelation('component', 'code', $code)->where('code', $subCode)->first();

            if ($statement) {
                $oldSanction = $oldSanctions->where('Sanktion_timestamp', $component->Sanktion_timestamp)->first();

                if ($oldSanction) {
                    $oldSanctionTitle = $oldSanction->sanktion_id_namn;
                    $newSanction = $newSanctions->where('title', $oldSanctionTitle)->first();

                    if ($newSanction) {
                        $newSanction->statements()->sync($statement->id);
                    }
                }
            }
        });
    }
}
