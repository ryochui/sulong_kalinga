<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VitalSigns;

class VitalSignsFactory extends Factory
{
    protected $model = VitalSigns::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'blood_pressure' => $this->faker->numerify('###/##'),
            'body_temperature' => $this->faker->randomFloat(2, 35, 40),
            'pulse_rate' => $this->faker->numberBetween(60, 100),
            'respiratory_rate' => $this->faker->numberBetween(12, 20),
        ];
    }
}