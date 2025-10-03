<?php

namespace Database\Factories;

use App\Models\Component;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Component>
 */
class ComponentFactory extends Factory
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
            'name_en' => $this->faker->sentence(),
            'name_se' => $this->faker->sentence(),
            'desc_en' => $this->faker->paragraph(),
            'desc_se' => $this->faker->paragraph(),
            'code' => strtoupper($this->faker->randomLetter()).$this->faker->randomDigit(),
            'sort_order' => null,
            'period_id' => $this->faker->numberBetween(1,4),
        ];
    }
}
