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
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Generate portal accounts first (these will be linked to beneficiaries)
        PortalAccount::factory()->count(20)->create();

        // 2. Generate users with different roles
        User::factory()->count(5)->create(['role_id' => 1]); // Admins
        User::factory()->count(5)->create(['role_id' => 2]); // Care Managers  
        User::factory()->count(5)->create(['role_id' => 3]); // Care Workers

        // 3. IMPORTANT: Create general care plans FIRST with all related data
        $generalCarePlans = [];
        for ($i = 1; $i <= 15; $i++) {
            // Get a random care worker
            $careWorkerId = User::where('role_id', 3)->inRandomOrder()->first()->id;
            
            // Create the general care plan with a specific ID
            $generalCarePlan = GeneralCarePlan::create([
                'general_care_plan_id' => $i,
                'review_date' => Carbon::now()->addMonths(6),
                'emergency_plan' => 'Standard emergency procedures',
                'care_worker_id' => $careWorkerId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Create emotional wellbeing for this general care plan
            EmotionalWellbeing::factory()->create([
                'general_care_plan_id' => $i,
            ]);
            
            // Create health history for this general care plan
            HealthHistory::factory()->create([
                'general_care_plan_id' => $i,
            ]);
            
            // Create cognitive function for this general care plan
            CognitiveFunction::factory()->create([
                'general_care_plan_id' => $i,
            ]);
            
            // Create mobility for this general care plan
            Mobility::factory()->create([
                'general_care_plan_id' => $i,
            ]);
            
            // Create medications for this general care plan
            foreach (range(1, 3) as $medicationIndex) {
                Medication::factory()->create([
                    'general_care_plan_id' => $i,
                ]);
            }
            
            // Create care needs for this general care plan
            foreach (range(1, 7) as $careCategoryId) {
                CareNeed::factory()->create([
                    'general_care_plan_id' => $i,
                    'care_category_id' => $careCategoryId,
                ]);
            }
            
            // Create care worker responsibilities for this general care plan
            foreach (range(1, 5) as $index) {
                CareWorkerResponsibility::factory()->create([
                    'general_care_plan_id' => $i,
                    'care_worker_id' => $careWorkerId,
                ]);
            }
            
            $generalCarePlans[] = $generalCarePlan;
        }

        // 4. Now create beneficiaries with references to existing general care plans
        $beneficiaries = [];
        for ($i = 0; $i < 10; $i++) {
            $beneficiary = Beneficiary::factory()->create([
                'general_care_plan_id' => $i + 1  // Reference the existing GCP
            ]);
            
            // Create family members for each beneficiary (1-3 members)
            $familyMemberCount = rand(1, 3);
            FamilyMember::factory($familyMemberCount)
                ->forBeneficiary($beneficiary->beneficiary_id)
                ->create();
                
            $beneficiaries[] = $beneficiary;
        }
        
        // 5. Create additional beneficiaries with fully established care plans
        $additionalBeneficiaries = [];
        for ($i = 0; $i < 5; $i++) {
            $beneficiary = Beneficiary::factory()->create([
                'general_care_plan_id' => $i + 11  // Start from 11 since we used 1-10 above
            ]);
            
            $familyMemberCount = rand(1, 3);
            FamilyMember::factory($familyMemberCount)
                ->forBeneficiary($beneficiary->beneficiary_id)
                ->create();
                
            $additionalBeneficiaries[] = $beneficiary;
        }

        // 6. Generate vital signs and weekly care plans
        foreach (range(1, 10) as $index) {
            // Get a random care worker to be the creator of both records
            $careWorkerId = User::where('role_id', 3)->inRandomOrder()->first()->id;
            
            // Create vital signs with the care worker as creator
            $vitalSigns = VitalSigns::factory()->create([
                'created_by' => $careWorkerId,
            ]);
            
            // Create weekly care plan with the same care worker as creator and reference the vital signs
            $weeklyCarePlan = WeeklyCarePlan::factory()->create([
                'care_worker_id' => $careWorkerId,
                'vital_signs_id' => $vitalSigns->vital_signs_id,
                'created_by' => $careWorkerId,
            ]);
            
            // Create weekly care plan interventions for each category
            foreach (range(1, 7) as $careCategoryId) {
                WeeklyCarePlanInterventions::factory()->create([
                    'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                    'intervention_id' => $careCategoryId,
                ]);
            }
        }
    }
}