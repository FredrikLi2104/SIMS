<?php

namespace Database\Seeders;

use App\Models\Deed;
use App\Models\DeedHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeedHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        $deeds = Deed::all();
        $deeds->each(function ($deed) {
            DeedHistory::create([
                'deed_id' => $deed['id'],
                'value' => $deed['value'],
                'user_id' => $deed['user_id'],
                'created_at' => $deed['created_at'],
                'updated_at' => $deed['updated_at'],
            ]);
        });
        */
    }
}
