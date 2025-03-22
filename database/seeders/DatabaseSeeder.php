<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\FamilyMember;
use App\Models\PortalAccount;
use App\Models\GeneralCarePlan;
use App\Models\HealthHistory;
use App\Models\EmotionalWellbeing;
use App\Models\CognitiveFunction;
use App\Models\Mobility;
use App\Models\CareNeed;
use App\Models\Medication;
use App\Models\VitalSigns;
use App\Models\WeeklyCarePlan;
use App\Models\WeeklyCarePlanInterventions;
use App\Models\Intervention;
use App\Models\CareCategory;
use App\Models\CareWorkerResponsibility;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate dummy data for portal_accounts
        PortalAccount::factory()->count(10)->create();

        // Generate dummy data for cose_users with role_id 2
        User::factory()->count(5)->create(['role_id' => 1]);

        // Generate dummy data for cose_users with role_id 2
        User::factory()->count(5)->create(['role_id' => 2]);

        // Generate dummy data for cose_users with role_id 3
        User::factory()->count(5)->create(['role_id' => 3]);


        // Generate dummy data for general_care_plans
        GeneralCarePlan::factory()->count(10)->create();

        // Generate dummy data for emotional_wellbeing and health_histories
        foreach (range(1, 10) as $generalCarePlanId) {
            EmotionalWellbeing::factory()->create([
                'general_care_plan_id' => $generalCarePlanId,
            ]);
            HealthHistory::factory()->create([
                'general_care_plan_id' => $generalCarePlanId,
            ]);
        }

        // Generate dummy data for cognitive_function
        foreach (range(1, 10) as $generalCarePlanId) {
            CognitiveFunction::factory()->create([
                'general_care_plan_id' => $generalCarePlanId,
            ]);
        }

        // Generate dummy data for mobility
        foreach (range(1, 10) as $generalCarePlanId) {
            Mobility::factory()->create([
                'general_care_plan_id' => $generalCarePlanId,
            ]);
        }

        // Generate dummy data for medications
        foreach (range(1, 10) as $generalCarePlanId) {
            foreach (range(1, 3) as $medicationIndex) {
                Medication::factory()->create([
                    'general_care_plan_id' => $generalCarePlanId,
                ]);
            }
        }

        // Generate dummy data for care_needs
        foreach (range(1, 10) as $generalCarePlanId) {
            foreach (range(1, 7) as $careCategoryId) {
                foreach (range(1, 2) as $index) {
                    CareNeed::factory()->create([
                        'general_care_plan_id' => $generalCarePlanId,
                        'care_category_id' => $careCategoryId,
                    ]);
                }
            }
        }

        // Generate dummy data for beneficiaries
        Beneficiary::factory()->count(10)->create();

        // Generate dummy data for family_members
        FamilyMember::factory()->count(10)->create();

        // Generate dummy data for vital_signs
        VitalSigns::factory()->count(10)->create();

        // Generate dummy data for weekly_care_plans
        WeeklyCarePlan::factory()->count(10)->create();

        // Generate dummy data for weekly_care_plan_interventions
        foreach (range(1, 10) as $weeklyCarePlanId) {
            foreach (range(1, 7) as $careCategoryId) {
                WeeklyCarePlanInterventions::factory()->create([
                    'weekly_care_plan_id' => $weeklyCarePlanId,
                    'intervention_id' => $careCategoryId,
                ]);
            }
        }

        // Generate dummy data for care_worker_responsibilities
        foreach (range(1, 10) as $generalCarePlanId) {
            // Select a random care worker for this general care plan
            $careWorkerId = User::where('role_id', 3)->inRandomOrder()->first()->id;

            foreach (range(1, 5) as $index) {
                CareWorkerResponsibility::factory()->create([
                    'general_care_plan_id' => $generalCarePlanId,
                    'care_worker_id' => $careWorkerId,
                ]);
            }
        }
        
    }
}