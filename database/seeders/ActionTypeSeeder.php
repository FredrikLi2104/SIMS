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
        /*
        $actionTypes = [
            [
                'name_en' => 'Plan Components',
                'name_se' => 'Planerakomponenter',
                'role' => 'user',
                'url' => 'plan/components',
                'model' => 'component',
            ],
            [
                'name_en' => 'Do Components',
                'name_se' => 'Gör Komponenter',
                'role' => 'user',
                'url' => 'do/components',
                'model' => 'component',
            ],
            [
                'name_en' => 'Plan Statements',
                'name_se' => 'Planutlåtanden',
                'role' => 'user',
                'url' => 'plan/statements',
                'model' => 'statement',
            ],
            [
                'name_en' => 'Plan Statements',
                'name_se' => 'Planutlåtanden',
                'role' => 'auditor',
                'url' => 'auditor/plan',
                'model' => 'statement',
            ],
            [
                'name_en' => 'Do Statements',
                'name_se' => 'Gör Uttalanden',
                'role' => 'user',
                'url' => 'do/statements',
                'model' => 'statement',
            ],
            [
                'name_en' => 'Generate Plan Report',
                'name_se' => 'Generera Planrapport',
                'role' => 'user',
                'url' => 'report',
                'model' => null,
            ],
        ];

        foreach ($actionTypes as $actionType) {
            $actionTypeExists = ActionType::where('name_en', $actionType['name_en'])
                ->where('role', $actionType['role'])
                ->exists();

            if ($actionTypeExists) {
                ActionType::where('name_en', $actionType['name_en'])
                    ->where('role', $actionType['role'])
                    ->update([
                        'url' => $actionType['url'],
                        'model' => $actionType['model'],
                    ]);
            } else {
                ActionType::insert($actionType);
            }
        }
        */
    }
}
