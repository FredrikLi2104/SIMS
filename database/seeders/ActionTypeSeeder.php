<?php

namespace Database\Seeders;

use App\Models\ActionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ActionType::insertOrIgnore([
            [
                'name_en' => 'Plan Components',
                'name_se' => 'Planerakomponenter',
                'role' => 'user',
                'url' => 'plan/components'
            ],
            [
                'name_en' => 'Plan Statements',
                'name_se' => 'Planutlåtanden',
                'role' => 'user',
                'url' => 'plan/statements'
            ],
            [
                'name_en' => 'Plan Statements',
                'name_se' => 'Planutlåtanden',
                'role' => 'auditor',
                'url' => 'plan/statements'
            ],
            [
                'name_en' => 'Do Statements',
                'name_se' => 'Gör Uttalanden',
                'role' => 'user',
                'url' => 'do/statements'
            ],
            [
                'name_en' => 'Generate Plan Report',
                'name_se' => 'Generera Planrapport',
                'role' => 'user',
                'url' => 'report'
            ],
            [
                'name_en' => 'Generate Review Report',
                'name_se' => 'Generera Granskningsrapport',
                'role' => 'auditor',
                'url' => 'report'
            ],
            [
                'name_en' => 'View Risks',
                'name_se' => 'Visa Risker',
                'role' => 'user',
                'url' => 'risks'
            ],
            [
                'name_en' => 'View Risks',
                'name_se' => 'Visa Risker',
                'role' => 'auditor',
                'url' => 'risks'
            ],
            [
                'name_en' => 'View KPIs',
                'name_se' => 'Visa KPIer',
                'role' => 'user',
                'url' => 'kpis'
            ],
            [
                'name_en' => 'View KPIs',
                'name_se' => 'Visa KPIer',
                'role' => 'auditor',
                'url' => 'kpis'
            ]
        ]);
    }
}
