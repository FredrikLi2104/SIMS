<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountryGroupSeeder extends Seeder
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
        $oldSanctions = collect(json_decode($oldSanctions))->groupBy('Gruppering');

        $oldSanctions->each(function ($countryGroup, $key) use ($oldSanctions) {
            $group = Group::where('desc_en', $key)->first();

            if ($group) {
                $countries = Country::whereIn('name', $countryGroup->pluck('Jurisdiction')->all())->get();
                $group->countries()->sync($countries);
            }
        });
        */
    }
}
