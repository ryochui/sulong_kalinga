<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Medications;

class MedicationsFactory extends Factory
{
    protected $model = Medications::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'medication' => $this->faker->word,
            'dosage' => $this->faker->word,
            'frequency' => $this->faker->word,
            'administration_instructions' => $this->faker->sentence
        ];
    }
}