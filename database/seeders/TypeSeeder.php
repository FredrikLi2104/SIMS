<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        $oldTypes = File::get(base_path('/resources/ADM_ET.json'));
        $oldTypes = collect(json_decode($oldTypes));
        $newTypes = [];

        foreach ($oldTypes as $oldType) {
            $newTypes[] = [
                'id' => $oldType->Enforcement_id + 1,
                'text_en' => $oldType->Typ,
                'text_se' => null
            ];
        }

        Type::insertOrIgnore($newTypes);
        */
    }
}
