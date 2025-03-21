<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FamilyMemberFactory extends Factory
{
    protected $model = FamilyMember::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'birthday' => $this->faker->date(),
            'mobile' => $this->faker->unique()->numerify('+63##########'),
            'landline' => $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail,
            'access' => $this->faker->boolean(80), // 80% chance of being true
            'street_address' => $this->faker->address,
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'related_beneficiary_id' => $this->faker->numberBetween(1, 10),
            'relation_to_beneficiary' => $this->faker->randomElement(['Sister', 'Brother', 'Parent']),
            'is_primary_caregiver' => $this->faker->boolean(20), // 20% chance of being true
            'portal_account_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}