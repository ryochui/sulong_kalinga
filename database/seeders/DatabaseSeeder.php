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
use App\Models\Notification;
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
        
        // Generate care managers first so we can assign care workers to them
        $careManagers = [];
        for ($i = 0; $i < 5; $i++) {
            $careManagers[] = User::factory()->create(['role_id' => 2]);
        }
        
        // Create care workers with assigned care managers
        $careWorkers = [];
        for ($i = 0; $i < 5; $i++) {
            // Assign each care worker to a random care manager
            $randomCareManager = $careManagers[array_rand($careManagers)];
            
            $careWorkers[] = User::factory()->create([
                'role_id' => 3,
                'assigned_care_manager_id' => $randomCareManager->id
            ]);
        }

        // 3. Create general care plans with all related data
        $generalCarePlans = [];
        for ($i = 1; $i <= 15; $i++) {
            // Get a random care worker
            $careWorkerId = $careWorkers[array_rand($careWorkers)]->id;
            
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

        // Rest of your seeder remains the same...
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
            $careWorkerId = $careWorkers[array_rand($careWorkers)]->id;
            
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

        // 7. Generate notifications
        $this->generateNotifications();
    }

    private function generateNotifications()
    {
        // Generate notifications for beneficiaries - keeping this part
        $beneficiaries = Beneficiary::take(5)->get();
        foreach ($beneficiaries as $beneficiary) {
            Notification::factory()
                ->count(rand(2, 5))
                ->forBeneficiary($beneficiary->beneficiary_id)
                ->create();
        }
        
        // Generate notifications for family members - keeping this part
        $familyMembers = FamilyMember::take(5)->get();
        foreach ($familyMembers as $familyMember) {
            Notification::factory()
                ->count(rand(2, 4))
                ->forFamilyMember($familyMember->family_member_id)
                ->create();
        }
        
        // UPDATED: Generate notifications for ALL COSE staff instead of just taking 5
        $staffMembers = User::where('role_id', '<=', 3)->get(); // Get ALL COSE staff
        
        $notificationTypes = [
            // Admin notifications (role_id = 1)
            1 => [
                'System Update' => 'The system has been updated with new features.',
                'Security Alert' => 'A new security patch has been applied.',
                'New User Registration' => 'A new user has registered in the system.',
                'Data Backup Complete' => 'Automatic data backup has completed successfully.',
                'Performance Report' => 'Monthly performance report is now available.'
            ],
            // Care Manager notifications (role_id = 2)
            2 => [
                'New Case Assigned' => 'You have been assigned a new case to manage.',
                'Care Plan Review' => 'A care plan is due for review this week.',
                'Staff Schedule Update' => 'There are changes to the staff schedule.',
                'Patient Status Alert' => 'A patient status has been updated.',
                'Weekly Report Due' => 'Your weekly report is due in 2 days.'
            ],
            // Care Worker notifications (role_id = 3)
            3 => [
                'Visit Reminder' => 'You have a scheduled visit tomorrow.',
                'Medication Update' => 'Medication schedule has been updated for a patient.',
                'Training Available' => 'New training modules are available for you.',
                'Shift Change Request' => 'A shift change has been requested.',
                'Documentation Reminder' => 'Please complete your visit documentation.'
            ]
        ];
        
        // For each staff member, create role-specific notifications
        foreach ($staffMembers as $staff) {
            $roleSpecificMessages = $notificationTypes[$staff->role_id] ?? $notificationTypes[1];
            $count = rand(3, 7); // Create 3-7 notifications per user
            
            // Create some read and some unread notifications
            for ($i = 0; $i < $count; $i++) {
                $title = array_rand($roleSpecificMessages);
                $message = $roleSpecificMessages[$title];
                
                Notification::create([
                    'user_id' => $staff->id,
                    'user_type' => 'cose_staff',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now()->subHours(rand(1, 72)), // Random time within last 3 days
                    'is_read' => rand(0, 100) < 30, // 30% chance of being read
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}