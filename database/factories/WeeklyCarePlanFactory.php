<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WeeklyCarePlan;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\VitalSigns;

class WeeklyCarePlanFactory extends Factory
{
    protected $model = WeeklyCarePlan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random beneficiary ID
        $beneficiaryId = Beneficiary::inRandomOrder()->first()->beneficiary_id;

        // Get a random care worker ID with role_id 3
        $careWorkerId = User::where('role_id', 3)->inRandomOrder()->first()->id;

        // Get a random care manager ID with role_id 2
        $careManagerId = User::where('role_id', 2)->inRandomOrder()->first()->id;

        // Get a random vital signs ID
        $vitalSignsId = VitalSigns::inRandomOrder()->first()->vital_signs_id;

        // Get a random user ID with role_id 2 for created_by and updated_by
        $userIdWithRole2 = User::where('role_id', 2)->inRandomOrder()->first()->id;

        return [
            'beneficiary_id' => $beneficiaryId,
            'care_worker_id' => $careWorkerId,
            'care_manager_id' => $careManagerId,
            'vital_signs_id' => $vitalSignsId,
            'date' => $this->faker->date(),
            'assessment' => $this->faker->paragraph,
            'evaluation_recommendations' => $this->faker->paragraph,
            'assessment_summary_draft' => $this->faker->paragraph,
            'assessment_translation_draft' => $this->faker->paragraph,
            'evaluation_summary_draft' => $this->faker->paragraph,
            'evaluation_translation_draft' => $this->faker->paragraph,
            'created_by' => $userIdWithRole2,
            'updated_by' => $userIdWithRole2,
        ];
    }
}