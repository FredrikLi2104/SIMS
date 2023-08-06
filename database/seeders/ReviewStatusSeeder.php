<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        ReviewStatus::insertOrIgnore([
            [
                'name_en' => 'Pending',
                'name_se' => 'Väntar',
                'sort_order' => 0,
            ],
            [
                'name_en' => 'Accepted',
                'name_se' => 'Accepterad',
                'sort_order' => 1,
            ],
            [
                'name_en' => 'Rejected',
                'name_se' => 'Avvisad',
                'sort_order' => 2,
            ],
        ]);

        Review::where('accepted', 1)->update(['review_status_id' => 2]);
        Review::where('accepted', 0)->update(['review_status_id' => 3]);
        */
    }
}
