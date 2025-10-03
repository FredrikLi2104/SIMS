<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Statement>
 */
class StatementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
            'content_en' => $this->faker->sentence(),
            'content_se' => $this->faker->sentence(),
            'desc_en' => $this->faker->paragraph(),
            'desc_se' => $this->faker->paragraph(),
            'k1_en' => $this->faker->sentence(),
            'k2_en' => $this->faker->sentence(),
            'k3_en' => $this->faker->sentence(),
            'k4_en' => $this->faker->sentence(),
            'k5_en' => $this->faker->sentence(),
            'k1_se' => $this->faker->sentence(),
            'k2_se' => $this->faker->sentence(),
            'k3_se' => $this->faker->sentence(),
            'k4_se' => $this->faker->sentence(),
            'k5_se' => $this->faker->sentence(),
            'implementation_en' => $this->faker->sentence(),
            'implementation_se' => $this->faker->sentence(),
            'guide_en' => $this->faker->sentence(),
            'guide_se' => $this->faker->sentence(),
            'component_id' => rand(1,30),
            'statement_type_id' => 1,
            'sort_order' => null,
        ];
    }
}
