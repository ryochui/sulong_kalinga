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
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageReadStatus;
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
        
         // 6. Generate weekly care plans with realistic intervention data
         $this->generateRealisticWeeklyCarePlans($careWorkers, $beneficiaries);

        // 7. Generate notifications
        $this->generateNotifications();

        // 8. Generate conversations and messages
        $this->generateConversations();

    }

    /**
     * Generate realistic weekly care plans with diverse interventions
     * Using existing interventions from the database
     */
    private function generateRealisticWeeklyCarePlans($careWorkers, $beneficiaries)
    {
        // Fetch all care categories
        $careCategories = CareCategory::all();
        $interventionsByCategoryId = []; // Initialize the array

        // Get all interventions by category
        foreach ($careCategories as $category) {
            $interventions = Intervention::where('care_category_id', $category->care_category_id)->get();
            if ($interventions->count() > 0) {
                $interventionsByCategoryId[$category->care_category_id] = $interventions->pluck('intervention_id')->toArray();
            }
        }
            
        // FIXED DATE GENERATION - Use explicit arrays of valid dates instead of Carbon calculations
        $validYears = [2024, 2025];
        $validMonths = range(1, 12);
        $validDates = [];
        
        // Generate an array of valid dates in 2024-2025 format
        foreach ($validYears as $year) {
            foreach ($validMonths as $month) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $validDates[] = sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
        }
        
        // Create 50 weekly care plans
        for ($i = 0; $i < 50; $i++) {
            // Select a random care worker and beneficiary
            $careWorker = $careWorkers[array_rand($careWorkers)];
            $beneficiary = $beneficiaries[array_rand($beneficiaries)];
            
            // Create vital signs
            $vitalSigns = VitalSigns::factory()->create([
                'created_by' => $careWorker->id,
            ]);
            
            // Pick a random date from our valid dates array
            $planDate = $validDates[array_rand($validDates)];
            
            // Log the date we're using to verify
            \Log::info("Creating weekly care plan with date: {$planDate}");
            
            // Create the weekly care plan without using factory
            $weeklyCarePlan = new WeeklyCarePlan();
            $weeklyCarePlan->beneficiary_id = $beneficiary->beneficiary_id;
            $weeklyCarePlan->care_worker_id = $careWorker->id;
            $weeklyCarePlan->vital_signs_id = $vitalSigns->vital_signs_id;
            $weeklyCarePlan->date = $planDate; // Set the date directly as a string
            $weeklyCarePlan->assessment = $this->getRandomAssessment();
            $weeklyCarePlan->evaluation_recommendations = $this->getRandomRecommendation();
            $weeklyCarePlan->created_by = $careWorker->id;
            $weeklyCarePlan->updated_by = $careWorker->id;
            $weeklyCarePlan->save();
            
            // Add 3-8 interventions from different categories
            $numInterventions = rand(3, 8);
            
            // Ensure we pick interventions from different categories
            $usedCategoryIds = [];
            
            for ($j = 0; $j < $numInterventions; $j++) {
                // Pick a category (prioritize unused ones)
                $availableCategoryIds = array_diff(array_keys($interventionsByCategoryId), $usedCategoryIds);
                
                if (empty($availableCategoryIds)) {
                    // If we've used all categories, reset and pick randomly
                    $categoryId = array_rand($interventionsByCategoryId);
                } else {
                    // Pick from unused categories
                    $categoryId = $availableCategoryIds[array_rand($availableCategoryIds)];
                    $usedCategoryIds[] = $categoryId;
                }
                
                // Get interventions for this category
                $categoryInterventions = $interventionsByCategoryId[$categoryId];
                
                if (!empty($categoryInterventions)) {
                    // Pick a random intervention from this category
                    $interventionId = $categoryInterventions[array_rand($categoryInterventions)];
                    
                    // Determine if this should be a custom intervention (20% chance)
                    $isCustom = (rand(1, 5) === 1);
                    
                    if ($isCustom) {
                        // Custom intervention - no intervention_id, only category_id and description
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'care_category_id' => $categoryId,
                            'intervention_description' => 'Custom: ' . $this->getRandomCustomIntervention($categoryId),
                            'duration_minutes' => rand(15, 120),
                            'implemented' => (rand(1, 10) > 2) // 80% chance of being implemented
                        ]);
                    } else {
                        // Standard intervention
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => $interventionId,
                            'duration_minutes' => rand(15, 120),
                            'implemented' => (rand(1, 10) > 2) // 80% chance of being implemented
                        ]);
                    }
                }
            }
        }
        
        // Create overlapping plans with fixed dates
        $overlappingDates = [
            '2024-03-15',
            '2024-04-15',
            '2024-05-15'
        ];
        
        foreach ($overlappingDates as $date) {
            // Create 3 plans on the same date by different care workers
            for ($i = 0; $i < 3; $i++) {
                $careWorker = $careWorkers[$i]; // Use first 3 care workers
                $beneficiary = $beneficiaries[array_rand($beneficiaries)];
                
                // Create vital signs
                $vitalSigns = VitalSigns::factory()->create([
                    'created_by' => $careWorker->id,
                ]);
                
                // Log the overlapping date we're using
                \Log::info("Creating overlapping weekly care plan with date: {$date}");
                
                $weeklyCarePlan = new WeeklyCarePlan();
                $weeklyCarePlan->beneficiary_id = $beneficiary->beneficiary_id;
                $weeklyCarePlan->care_worker_id = $careWorker->id;
                $weeklyCarePlan->vital_signs_id = $vitalSigns->vital_signs_id;
                $weeklyCarePlan->date = $date; // String date format
                $weeklyCarePlan->assessment = $this->getRandomAssessment();
                $weeklyCarePlan->evaluation_recommendations = $this->getRandomRecommendation();
                $weeklyCarePlan->created_by = $careWorker->id;
                $weeklyCarePlan->updated_by = $careWorker->id;
                $weeklyCarePlan->save();
                                
                // Add interventions from all categories for these overlapping plans
                foreach ($careCategories as $category) {
                    $categoryId = $category->care_category_id;
                    if (isset($interventionsByCategoryId[$categoryId]) && !empty($interventionsByCategoryId[$categoryId])) {
                        $interventionId = $interventionsByCategoryId[$categoryId][array_rand($interventionsByCategoryId[$categoryId])];
                        
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => $interventionId,
                            'duration_minutes' => rand(15, 120),
                            'implemented' => true
                        ]);
                    }
                }
            }
        }
    }
    
    /**
     * Get a random custom intervention description based on category
     */
    private function getRandomCustomIntervention($categoryId)
    {
        $customInterventions = [
            1 => [ // Mobility
                'Specialized wheelchair transfer technique',
                'Custom mobility exercise program',
                'Beach walk assistance',
                'Garden pathway navigation',
                'Stair climbing with modified technique'
            ],
            2 => [ // Cognitive/Communication
                'Personalized memory card games',
                'Digital communication device training',
                'Native language practice sessions',
                'Custom flash card exercises',
                'Family photo recognition practice'
            ],
            3 => [ // Self-Sustainability
                'Modified clothing fastener technique',
                'Customized eating utensil training',
                'Specialized shower chair instruction',
                'Personal hygiene adapted routine',
                'Medication organization system training'
            ],
            4 => [ // Daily life/Social contact
                'Virtual family reunion setup',
                'Religious service accompaniment',
                'Community garden participation',
                'Senior center special event attendance',
                'Neighborhood walking group participation'
            ],
            5 => [ // Disease/Therapy Handling
                'Specialized diabetic foot care',
                'Custom cardiac rehabilitation exercises',
                'Modified stroke recovery techniques',
                'Personalized pain management approach',
                'Adaptive arthritis management'
            ],
            6 => [ // Outdoor Activities
                'Modified outdoor exercise routine',
                'Nature observation activity',
                'Community garden participation',
                'Outdoor social interaction support',
                'Supervised neighborhood walking'
            ],
            7 => [ // Household Keeping
                'Modified kitchen organization system',
                'Adaptive cooking technique instruction',
                'Energy-conserving housework approach',
                'Specialized laundry management',
                'Safety-focused home organization'
            ]
        ];
        
        // Default to first category if the requested one doesn't exist
        if (!isset($customInterventions[$categoryId])) {
            $categoryId = 1;
        }
        
        return $customInterventions[$categoryId][array_rand($customInterventions[$categoryId])];
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

    /**
     * Generate conversations and messages between users following role hierarchy rules
     */
    private function generateConversations()
    {
        // Get users by role
        $admins = User::where('role_id', 1)->get();
        $careManagers = User::where('role_id', 2)->get();
        $careWorkers = User::where('role_id', 3)->get();
        
        // Get some beneficiaries and family members for conversations
        $beneficiaries = Beneficiary::take(5)->get();
        $familyMembers = FamilyMember::take(5)->get();
        
        // ================ PRIVATE CONVERSATIONS ================
        
        // 1. Create conversations for Admins (can only talk to Care Managers)
        foreach ($admins as $admin) {
            // Create 2 private conversations with random Care Managers
            for ($i = 0; $i < 2; $i++) {
                if ($careManagers->count() > 0) {
                    $randomCareManager = $careManagers->random();
                    $this->createPrivateConversation($admin, $randomCareManager);
                }
            }
        }
        
        // 2. Create conversations for Care Managers (can talk to Admins, other Care Managers, and Care Workers)
        foreach ($careManagers as $careManager) {
            // Create 3 private conversations - with Admin, other Care Manager, and Care Worker
            
            // With Admin
            if ($admins->count() > 0) {
                $randomAdmin = $admins->random();
                // Skip if conversation already exists from admin's loop
                if (!$this->conversationExistsBetween($careManager->id, 'cose_staff', $randomAdmin->id, 'cose_staff')) {
                    $this->createPrivateConversation($careManager, $randomAdmin);
                }
            }
            
            // With another Care Manager
            $otherCareManagers = $careManagers->where('id', '!=', $careManager->id);
            if ($otherCareManagers->count() > 0) {
                $randomOtherCareManager = $otherCareManagers->random();
                $this->createPrivateConversation($careManager, $randomOtherCareManager);
            }
            
            // With Care Worker
            if ($careWorkers->count() > 0) {
                $randomCareWorker = $careWorkers->random();
                $this->createPrivateConversation($careManager, $randomCareWorker);
            }
        }
        
        // 3. Create conversations for Care Workers (can only talk to Care Managers)
        foreach ($careWorkers as $careWorker) {
            // Create 1 conversation with a Care Manager if not already created
            if ($careManagers->count() > 0) {
                $randomCareManager = $careManagers->random();
                // Skip if conversation already exists from care manager's loop
                if (!$this->conversationExistsBetween($careWorker->id, 'cose_staff', $randomCareManager->id, 'cose_staff')) {
                    $this->createPrivateConversation($careWorker, $randomCareManager);
                }
            }
            
            // Create 1-2 conversations with beneficiaries and family members
            if ($beneficiaries->count() > 0) {
                $randomBeneficiary = $beneficiaries->random();
                $this->createPrivateConversation($careWorker, $randomBeneficiary, 'beneficiary');
            }
            
            if ($familyMembers->count() > 0) {
                $randomFamilyMember = $familyMembers->random();
                $this->createPrivateConversation($careWorker, $randomFamilyMember, 'family_member');
            }
        }
        
        // ================ GROUP CONVERSATIONS ================
        
        // 1. Create group chats for Admins (with only Care Managers)
        foreach ($admins as $admin) {
            if ($careManagers->count() >= 2) {
                $this->createGroupChat($admin, $careManagers->random(rand(2, min(4, $careManagers->count())))->all());
            }
        }
        
        // 2. Create group chats for Care Managers:
        // a) With Admins only
        // b) With other Care Managers only
        // c) With Care Workers only (to avoid mixing admins and care workers)
        foreach ($careManagers as $careManager) {
            // Group with Admins (if enough admins exist)
            if ($admins->count() >= 1) {
                $groupParticipants = $admins->random(min(2, $admins->count()))->all();
                $otherCareManagers = $careManagers->where('id', '!=', $careManager->id)->random(min(2, $careManagers->count() - 1))->all();
                $this->createGroupChat($careManager, array_merge($groupParticipants, $otherCareManagers));
            }
            
            // Group with Care Workers only
            if ($careWorkers->count() >= 2) {
                $this->createGroupChat($careManager, $careWorkers->random(rand(2, min(4, $careWorkers->count())))->all());
            }
        }
        
        // 3. Create group chats for Care Workers (with Care Managers and clients)
        foreach ($careWorkers as $careWorker) {
            // One group with Care Manager, beneficiary and family member
            if ($careManagers->count() > 0 && $beneficiaries->count() > 0 && $familyMembers->count() > 0) {
                $participants = [
                    ['object' => $careManagers->random(), 'type' => 'cose_staff'],
                    ['object' => $beneficiaries->random(), 'type' => 'beneficiary'],
                    ['object' => $familyMembers->random(), 'type' => 'family_member']
                ];
                
                $this->createGroupChatWithMixedParticipants($careWorker, $participants);
            }
        }
        
        // Log how many conversations were created
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();
        $totalAttachments = MessageAttachment::count();
        
        \Log::info("Generated {$totalConversations} conversations with {$totalMessages} messages and {$totalAttachments} attachments.");
    }

    /**
     * Check if a conversation already exists between two participants
     */
    private function conversationExistsBetween($userId1, $userType1, $userId2, $userType2)
    {
        // Get conversations where user1 is a participant
        $user1ConversationIds = ConversationParticipant::where('participant_id', $userId1)
            ->where('participant_type', $userType1)
            ->pluck('conversation_id');
        
        // Find if any of those conversations have user2 as participant
        return ConversationParticipant::whereIn('conversation_id', $user1ConversationIds)
            ->where('participant_id', $userId2)
            ->where('participant_type', $userType2)
            ->exists();
    }

    /**
     * Create a private conversation between two users with messages
     */
    private function createPrivateConversation($user1, $user2, $user2Type = 'cose_staff')
    {
        // Create a private conversation
        $conversation = Conversation::factory()->privateChat()->create();
        
        // Add the first user as a participant
        ConversationParticipant::create([
            'conversation_id' => $conversation->conversation_id,
            'participant_id' => $user1->id,
            'participant_type' => 'cose_staff',
            'joined_at' => now()->subDays(rand(1, 30)),
        ]);
        
        // Add the second user as a participant
        ConversationParticipant::create([
            'conversation_id' => $conversation->conversation_id,
            'participant_id' => ($user2Type === 'cose_staff') ? $user2->id : $user2->{$user2Type === 'beneficiary' ? 'beneficiary_id' : 'family_member_id'},
            'participant_type' => $user2Type,
            'joined_at' => now()->subDays(rand(1, 30)),
        ]);
        
        // Create messages in this conversation from both participants
        $messageCount = rand(3, 10);
        
        $lastMessage = null;
        for ($j = 0; $j < $messageCount; $j++) {
            // Alternate between the two participants
            if ($j % 2 == 0) {
                // First user sends message
                $senderId = $user1->id;
                $senderType = 'cose_staff';
            } else {
                // Second user sends message
                $senderId = ($user2Type === 'cose_staff') ? $user2->id : $user2->{$user2Type === 'beneficiary' ? 'beneficiary_id' : 'family_member_id'};
                $senderType = $user2Type;
            }
            
            $isUnsent = (rand(1, 20) === 1); // 5% chance of being unsent
            $message = Message::create([
                'conversation_id' => $conversation->conversation_id,
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'content' => \Faker\Factory::create()->sentence(rand(3, 15)),
                'is_unsent' => $isUnsent,
                'message_timestamp' => now()->subDays(5)->addMinutes($j * 30),
            ]);
            
            $lastMessage = $message;
            
            // Randomly add attachments and read statuses
            $this->addAttachmentAndReadStatuses($message, [
                ['id' => $user1->id, 'type' => 'cose_staff'],
                ['id' => ($user2Type === 'cose_staff') ? $user2->id : $user2->{$user2Type === 'beneficiary' ? 'beneficiary_id' : 'family_member_id'}, 'type' => $user2Type]
            ]);
        }
        
        // Update the conversation with the last message ID
        if ($lastMessage) {
            $conversation->last_message_id = $lastMessage->message_id;
            $conversation->save();
        }
        
        return $conversation;
    }

    /**
     * Create a group chat with staff users of the same type
     */
    private function createGroupChat($creator, $participants)
    {
        // Create a group chat
        $groupChat = Conversation::factory()->groupChat()->create([
            'name' => 'Team ' . \Faker\Factory::create()->word . ' Chat',
        ]);
        
        // Add the creator as a participant
        ConversationParticipant::create([
            'conversation_id' => $groupChat->conversation_id,
            'participant_id' => $creator->id,
            'participant_type' => 'cose_staff',
            'joined_at' => now()->subDays(rand(1, 30)),
        ]);
        
        // Add other participants
        foreach ($participants as $participant) {
            ConversationParticipant::create([
                'conversation_id' => $groupChat->conversation_id,
                'participant_id' => $participant->id,
                'participant_type' => 'cose_staff',
                'joined_at' => now()->subDays(rand(1, 30)),
            ]);
        }
        
        // Convert collection to array and merge with creator for messages
        $allParticipants = [$creator];
        if ($participants instanceof \Illuminate\Database\Eloquent\Collection) {
            $participantsArray = $participants->all(); // Convert Collection to array
        } else {
            $participantsArray = $participants; // Already an array
        }
        
        // Generate messages
        $this->generateGroupMessages($groupChat, array_merge($allParticipants, $participantsArray), []);
        
        return $groupChat;
    }

    /**
     * Create a group chat with mixed participant types
     */
    private function createGroupChatWithMixedParticipants($creator, $participants)
    {
        // Create a group chat
        $groupChat = Conversation::factory()->groupChat()->create([
            'name' => 'Team ' . \Faker\Factory::create()->word . ' Support',
        ]);
        
        // Add the creator as a participant
        ConversationParticipant::create([
            'conversation_id' => $groupChat->conversation_id,
            'participant_id' => $creator->id,
            'participant_type' => 'cose_staff',
            'joined_at' => now()->subDays(rand(1, 30)),
        ]);
        
        // Convert participants to a format we can use
        $allParticipants = [
            ['object' => $creator, 'type' => 'cose_staff']
        ];
        
        // Add other participants
        foreach ($participants as $participant) {
            $participantId = ($participant['type'] === 'cose_staff') 
                ? $participant['object']->id 
                : ($participant['type'] === 'beneficiary' 
                    ? $participant['object']->beneficiary_id 
                    : $participant['object']->family_member_id);
            
            ConversationParticipant::create([
                'conversation_id' => $groupChat->conversation_id,
                'participant_id' => $participantId,
                'participant_type' => $participant['type'],
                'joined_at' => now()->subDays(rand(1, 30)),
            ]);
            
            $allParticipants[] = $participant;
        }
        
        // Generate messages
        $this->generateGroupMessages($groupChat, [], $allParticipants);
        
        return $groupChat;
    }

    /**
     * Generate messages for a group chat
     */
    private function generateGroupMessages($groupChat, $staffParticipants, $mixedParticipants)
    {
        // Determine which participants array to use
        $useParticipants = !empty($mixedParticipants) ? $mixedParticipants : $staffParticipants;
        
        // Generate 5-15 messages in the group chat from various participants
        $messageCount = rand(5, 15);
        
        $lastMessage = null;
        for ($j = 0; $j < $messageCount; $j++) {
            // Choose a random participant to send the message
            $randomIndex = array_rand($useParticipants);
            $randomParticipant = $useParticipants[$randomIndex];
            
            // Get the sender ID and type
            if (!empty($mixedParticipants)) {
                $senderId = ($randomParticipant['type'] === 'cose_staff') 
                    ? $randomParticipant['object']->id 
                    : ($randomParticipant['type'] === 'beneficiary' 
                        ? $randomParticipant['object']->beneficiary_id 
                        : $randomParticipant['object']->family_member_id);
                $senderType = $randomParticipant['type'];
            } else {
                $senderId = $randomParticipant->id;
                $senderType = 'cose_staff';
            }
            
            $message = Message::create([
                'conversation_id' => $groupChat->conversation_id,
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'content' => \Faker\Factory::create()->sentence(rand(3, 15)),
                'message_timestamp' => now()->subDays(5)->addMinutes($j * 30),
            ]);
            
            $lastMessage = $message;
            
            // Create a list of all participants for read statuses
            $allParticipantIds = [];
            if (!empty($mixedParticipants)) {
                foreach ($mixedParticipants as $p) {
                    $pId = ($p['type'] === 'cose_staff') 
                        ? $p['object']->id 
                        : ($p['type'] === 'beneficiary' 
                            ? $p['object']->beneficiary_id 
                            : $p['object']->family_member_id);
                    
                    $allParticipantIds[] = ['id' => $pId, 'type' => $p['type']];
                }
            } else {
                foreach ($staffParticipants as $p) {
                    $allParticipantIds[] = ['id' => $p->id, 'type' => 'cose_staff'];
                }
            }
            
            // Add attachments and read statuses
            $this->addAttachmentAndReadStatuses($message, $allParticipantIds);
        }
        
        // Update the conversation with the last message ID
        if ($lastMessage) {
            $groupChat->last_message_id = $lastMessage->message_id;
            $groupChat->save();
        }
    }

    /**
     * Add attachment and read statuses to a message
     */
    private function addAttachmentAndReadStatuses($message, $participants)
    {
        // Randomly add attachments to some messages
        if (rand(1, 5) == 1) { // 20% chance
            $isImage = rand(0, 1) == 1;
            
            if ($isImage) {
                $fileName = \Faker\Factory::create()->word . '.jpg';
                $filePath = 'message_attachments/images/' . $fileName;
                $fileType = 'image/jpeg';
            } else {
                $fileExtension = ['pdf', 'doc', 'docx'][rand(0, 2)];
                $fileName = \Faker\Factory::create()->word . '.' . $fileExtension;
                $filePath = 'message_attachments/documents/' . $fileName;
                
                if ($fileExtension === 'pdf') {
                    $fileType = 'application/pdf';
                } elseif ($fileExtension === 'doc') {
                    $fileType = 'application/msword';
                } else {
                    $fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                }
            }
            
            MessageAttachment::create([
                'message_id' => $message->message_id,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => rand(10000, 5000000), // 10KB to 5MB
                'is_image' => $isImage,
            ]);
        }
        
        // Mark messages as read by recipients
        foreach ($participants as $participant) {
            // Skip the sender (they've already seen their own message)
            if ($participant['id'] == $message->sender_id && $participant['type'] == $message->sender_type) {
                continue;
            }
            
            // 70% chance this participant has read the message
            if (rand(1, 10) <= 7) {
                MessageReadStatus::create([
                    'message_id' => $message->message_id,
                    'reader_id' => $participant['id'],
                    'reader_type' => $participant['type'],
                    'read_at' => now()->subMinutes(rand(1, 60)),
                ]);
            }
        }
    }

    private function getRandomAssessment()
    {
        $assessments = [
            "Beneficiary shows improved mobility compared to last week. Maintains good spirits and is engaging well with care activities.",
            "Cognitive function stable; some memory issues persist but responds well to memory exercises. Appetite has improved.",
            "Sleep patterns remain disrupted. Requires additional assistance with ADLs. Pain levels manageable with current medication.",
            "Mood fluctuations noted this week. Physical strength improving gradually with exercise regimen. Social engagement increased.",
            "Beneficiary experienced mild respiratory difficulties but recovered well. Hydration and nutrition intake adequate.",
            "Notable progress in self-care abilities. Beneficiary participated actively in all therapy sessions. Family reports satisfaction with care.",
            "Some anxiety observed when discussing medical appointments. Mobility has improved with the new assistive device.",
            "Beneficiary appears more energetic this week. Completed all recommended exercises. Medication compliance has improved.",
            "Blood pressure readings slightly elevated. Will monitor closely. Otherwise, beneficiary is engaging well in daily activities.",
            "Beneficiary expressed interest in community activities. Physical condition stable. Requires ongoing support with meal preparation."
        ];
        
        return $assessments[array_rand($assessments)];
    }

    private function getRandomRecommendation()
    {
        $recommendations = [
            "Continue current mobility exercises and gradually increase intensity. Follow up on referral to physical therapy.",
            "Maintain memory exercises daily. Consider adding new cognitive activities to prevent boredom. Review medication schedule with doctor.",
            "Implement suggested sleep hygiene practices. Consider adjusting evening routine to improve sleep quality. Follow up on pain management.",
            "Encourage participation in social group activities twice weekly. Continue monitoring mood and report significant changes.",
            "Monitor respiratory function closely. Ensure proper hydration and nutrition intake. Follow up with pulmonary specialist as scheduled.",
            "Continue current self-care regimen. Celebrate progress with beneficiary. Schedule follow-up with family to discuss ongoing support.",
            "Provide additional emotional support before medical appointments. Continue with current mobility assistance devices.",
            "Maintain current exercise regimen. Provide positive reinforcement for medication compliance. Consider adding new activities.",
            "Schedule follow-up to monitor blood pressure. Review dietary recommendations. Continue with current social activities.",
            "Support interest in community activities by providing transportation options. Continue meal preparation support while encouraging participation."
        ];
        
        return $recommendations[array_rand($recommendations)];
    }

}