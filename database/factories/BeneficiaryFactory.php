<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BeneficiaryFactory extends Factory
{
    protected $model = Beneficiary::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $generalCarePlanId = 1;

        $municipalityId = $this->faker->randomElement([1, 2]);
        $barangayId = $municipalityId == 1 ? $this->faker->numberBetween(1, 24) : $this->faker->numberBetween(1, 16);

        // Get a random user ID with role_id 2
        $userIdWithRole2 = User::where('role_id', 2)->inRandomOrder()->first()->id;

        // Generate a birthday for someone 60-100 years old
        $birthday = $this->faker->dateTimeBetween('-100 years', '-60 years')->format('Y-m-d');

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed']),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'birthday' => $birthday,
            'primary_caregiver' => $this->faker->name,
            'mobile' => '+63' . $this->faker->numerify('##########'),            'landline' => $this->faker->numerify('#######'),
            'street_address' => $this->faker->address,
            'barangay_id' => $barangayId,
            'municipality_id' => $municipalityId,
            'category_id' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8]),
            'emergency_contact_name' => $this->faker->name,
            'emergency_contact_relation' => $this->faker->randomElement(['Sister', 'Brother', 'Parent']),
            'emergency_contact_mobile' => '+63' . $this->faker->numerify('##########'), 
            'emergency_contact_email' => $this->faker->unique()->safeEmail,
            'emergency_procedure' => $this->faker->sentence,
            'beneficiary_status_id' => 1,
            'status_reason' => 'N/A',
            'general_care_plan_id' => $generalCarePlanId++,
            'portal_account_id' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
            'beneficiary_signature' => null,
            'care_worker_signature' => null,
            'created_by' => $userIdWithRole2,
            'updated_by' => $userIdWithRole2,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}