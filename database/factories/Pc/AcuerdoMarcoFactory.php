<?php

namespace Database\Factories\Pc;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pc\AcuerdoMarco>
 */
class AcuerdoMarcoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'AM-' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->sentence(3, false),
            'isActive' => $this->faker->boolean(80), // 80% de probabilidad de estar activo
        ];
    }

    /**
     * Indicate that the acuerdo marco is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'isActive' => true,
        ]);
    }

    /**
     * Indicate that the acuerdo marco is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'isActive' => false,
        ]);
    }
}
