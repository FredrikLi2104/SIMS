<?php

namespace Database\Seeders;

use App\Models\ActionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ActionStatus::insertOrIgnore([
            [
                'name_en' => 'Pending',
                'name_se' => 'Väntar',
                'color' => '#6E6B7B',
                'sort_order' => 0
            ],
            [
                'name_en' => 'In Progress',
                'name_se' => 'Pågår',
                'color' => '#FF9F43',
                'sort_order' => 1
            ],
            [
                'name_en' => 'Done',
                'name_se' => 'Klart',
                'color' => '#28C76F',
                'sort_order' => 2
            ]
        ]);
    }
}
