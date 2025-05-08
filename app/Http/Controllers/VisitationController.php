<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Http\Controllers\VisitationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitation;
use App\Models\VisitationException;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use App\Models\RecurringPattern;
use App\Models\Notification;
use App\Models\GeneralCarePlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitationController extends Controller
{
    /**
     * Display the care worker appointments page with appropriate view based on user role
     */
    public function index(Request $request)
    {
        // Run status update whenever the page loads
        $this->updatePastAppointmentsStatus();

        $user = Auth::user();
        $viewPath = $this->getViewPathForRole($user);
        
        // Process query filters for beneficiary search
        $query = Beneficiary::query();
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }
        
        // Get care workers for admin/care manager views
        $careWorkers = null;
        if ($user->role_id <= 2) { // Admin or Care Manager
            $careWorkers = User::where('role_id', 3)->get(); // Care Workers
        }
        
        return view($viewPath, [
            'careWorkers' => $careWorkers,
        ]);
    }
    
    /**
     * Get visitations for the calendar view
     */
    public function getVisitations(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start');
        $endDate = $request->input('end');
        $today = Carbon::today();
        
        $visitations = Visitation::with(['beneficiary', 'careWorker', 'recurringPattern']);
        
        // Apply role-based filters
        if ($user->isAdministrator() || $user->isExecutiveDirector()) {
            // Admin sees everything - no additional filters needed
        } elseif ($user->isCareManager()) {
            // Care managers see:
            // 1. Appointments they assigned
            // 2. Appointments with care workers they manage
            $managedCareWorkerIds = User::where('assigned_care_manager_id', $user->id)->pluck('id')->toArray();
            
            $visitations->where(function($query) use ($user, $managedCareWorkerIds) {
                $query->whereIn('care_worker_id', $managedCareWorkerIds)  // Workers they manage
                    ->orWhere('assigned_by', $user->id);               // Appointments they assigned
            });
        } elseif ($user->isCareWorker()) {
            // Care workers see:
            // 1. Their own appointments
            // 2. Appointments for beneficiaries assigned to them
            $assignedBeneficiaryIds = GeneralCarePlan::where('care_worker_id', $user->id)
                                        ->pluck('general_care_plan_id')->toArray();
                                        
            $beneficiaryIds = Beneficiary::whereIn('general_care_plan_id', $assignedBeneficiaryIds)
                                        ->pluck('beneficiary_id')->toArray();
                                        
            $visitations->where(function($query) use ($user, $beneficiaryIds) {
                $query->where('care_worker_id', $user->id)           // Their own appointments
                    ->orWhereIn('beneficiary_id', $beneficiaryIds); // Appointments for their beneficiaries
            });
        }
        
        // Date range filtering - MODIFIED to include ALL recurring patterns that might show in the range
        $visitations->where(function($query) use ($startDate, $endDate) {
            // Include non-recurring visitations within the date range
            $query->whereBetween('visitation_date', [$startDate, $endDate])
                ->whereDoesntHave('recurringPattern');
            
            // Include ALL recurring visitations that could generate occurrences in the range
            $query->orWhere(function($q) use ($startDate, $endDate) {
                $q->whereHas('recurringPattern')
                ->where(function($dateQ) use ($startDate, $endDate) {
                    // Initial date before end date (so it could generate occurrences in range)
                    $dateQ->where('visitation_date', '<=', $endDate);
                });
            });
        });
        
        // Apply additional filters (search, care worker, status)
        if ($request->has('search') && !empty($request->search)) {
            // Search logic - unchanged
        }
        
        if ($request->has('care_worker_id') && !empty($request->care_worker_id)) {
            // Care worker filter - unchanged
        }
        
        if ($request->has('status') && !empty($request->status)) {
            // Status filter - unchanged
        }
        
        // Rest of the method is unchanged...
        $this->updatePastAppointmentsStatus();
        
        $visitations = $visitations->get();
        
        // Format visitations for fullcalendar
        $events = [];
        
        foreach ($visitations as $visitation) {
            $color = $this->getStatusColor($visitation->status);
            
            $title = $visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name;
            if ($user->role_id <= 2) {
                $title .= ' (' . $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name . ')';
            }
            
            // Create the base event
            $baseEvent = [
                'id' => $visitation->visitation_id,
                'title' => $title,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#fff',
                'allDay' => $visitation->is_flexible_time,
                'extendedProps' => [
                    'visitation_id' => $visitation->visitation_id,
                    'care_worker' => $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name,
                    'care_worker_id' => $visitation->care_worker_id,
                    'beneficiary' => $visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name,
                    'beneficiary_id' => $visitation->beneficiary_id,
                    'visit_type' => ucwords(str_replace('_', ' ', $visitation->visit_type)),
                    'status' => ucfirst($visitation->status),
                    'is_flexible_time' => $visitation->is_flexible_time,
                    'notes' => $visitation->notes,
                    'phone' => $visitation->beneficiary->mobile ?? 'Not Available',
                    'address' => $visitation->beneficiary->street_address,
                    'recurring' => $visitation->recurringPattern ? true : false,
                    'recurring_pattern' => $visitation->recurringPattern ? [
                        'type' => $visitation->recurringPattern->pattern_type,
                        'day_of_week' => $visitation->recurringPattern->day_of_week,
                        'end_date' => $visitation->recurringPattern->recurrence_end
                    ] : null
                ]
            ];
            
            // Handle recurring events
            if ($visitation->recurringPattern) {
                // Get recurring pattern
                $pattern = $visitation->recurringPattern;
                $originalDate = Carbon::parse($visitation->visitation_date);
                $calendarEndDate = Carbon::parse($endDate);
                $recurrenceEnd = $pattern->recurrence_end ? Carbon::parse($pattern->recurrence_end) : $calendarEndDate;
                
                // Use the earlier of the two end dates
                $effectiveEndDate = $recurrenceEnd->min($calendarEndDate);
                
                // Skip if the pattern would never generate events in our range
                // (Original date is after calendar end or recurrence ended before calendar start)
                if ($originalDate->gt($calendarEndDate) || 
                    ($pattern->recurrence_end && Carbon::parse($pattern->recurrence_end)->lt(Carbon::parse($startDate)))) {
                    continue;
                }
                
                // Generate occurrences based on pattern type
                $currentDate = $originalDate->copy();
                
                while ($currentDate <= $effectiveEndDate) {
                    // Check if the current date is within the requested range
                    if ($currentDate >= Carbon::parse($startDate)) {
                        $eventDate = $currentDate->format('Y-m-d');
                        
                        // Determine status based on date
                        $eventStatus = $visitation->status;
                        if ($currentDate->lt($today) && $eventStatus === 'scheduled') {
                            $eventStatus = 'completed';
                        } else if ($currentDate->gte($today) && $eventStatus === 'completed') {
                            // FIX: Ensure future events are always scheduled, not completed
                            $eventStatus = 'scheduled';
                        }
                        
                        $eventStart = $eventDate;
                        $eventEnd = $eventDate;
                        
                        if (!$visitation->is_flexible_time) {
                            $eventStart .= 'T' . $visitation->start_time;
                            $eventEnd .= 'T' . $visitation->end_time;
                        }
                        
                        // Clone the base event and set the new dates
                        $event = array_merge([], $baseEvent);
                        $event['start'] = $eventStart;
                        $event['end'] = $eventEnd;
                        
                        // Override status for this specific occurrence
                        if ($eventStatus !== $visitation->status) {
                            $event['backgroundColor'] = $this->getStatusColor($eventStatus);
                            $event['borderColor'] = $this->getStatusColor($eventStatus);
                            $event['extendedProps']['status'] = ucfirst($eventStatus);
                        }
                        
                        // Use a unique ID for each occurrence
                        $event['id'] = $visitation->visitation_id . '-' . $currentDate->format('Ymd');
                        
                        $events[] = $event;
                    }
                    
                    // Move to next occurrence based on pattern
                    if ($pattern->pattern_type === 'daily') {
                        $currentDate->addDay();
                    } elseif ($pattern->pattern_type === 'weekly') {
                        // Weekly pattern with specific days
                        if ($pattern->day_of_week) {
                            // Get next occurrence based on day_of_week
                            // Convert string day_of_week to array of integers
                            $dayArray = is_string($pattern->day_of_week) ? 
                                array_map('intval', explode(',', $pattern->day_of_week)) : 
                                [intval($pattern->day_of_week)];
                            
                            // Sort days to find the next one after current day
                            sort($dayArray);
                            
                            // Find the next day in the sequence
                            $currentDayOfWeek = $currentDate->dayOfWeek;
                            $nextDay = null;
                            
                            foreach ($dayArray as $day) {
                                if ($day > $currentDayOfWeek) {
                                    $nextDay = $day;
                                    break;
                                }
                            }
                            
                            if ($nextDay === null) {
                                // No days left in this week, move to first day of next week
                                $nextDay = $dayArray[0];
                                $daysToAdd = 7 - $currentDayOfWeek + $nextDay;
                                $currentDate->addDays($daysToAdd);
                            } else {
                                // Move to next day in the same week
                                $daysToAdd = $nextDay - $currentDayOfWeek;
                                $currentDate->addDays($daysToAdd);
                            }
                        } else {
                            // Simple weekly pattern (same day each week)
                            $currentDate->addWeek();
                        }
                    } elseif ($pattern->pattern_type === 'monthly') {
                        $currentDate->addMonth();
                    }
                }
            } else {
                // Non-recurring event
                $baseEvent['start'] = $visitation->visitation_date . ($visitation->is_flexible_time ? '' : 'T' . $visitation->start_time);
                $baseEvent['end'] = $visitation->visitation_date . ($visitation->is_flexible_time ? '' : 'T' . $visitation->end_time);
                
                // Override status for past events
                if (Carbon::parse($visitation->visitation_date) < $today && $visitation->status === 'scheduled') {
                    $baseEvent['backgroundColor'] = $this->getStatusColor('completed');
                    $baseEvent['borderColor'] = $this->getStatusColor('completed');
                    $baseEvent['extendedProps']['status'] = 'Completed';
                }
                
                $events[] = $baseEvent;
            }
        }
        
        return response()->json($events);
    }

    /**
     * Update status of past appointments to "completed"
     * This will run during key user interactions instead of as a scheduled task
     */
    private function updatePastAppointmentsStatus()
    {
        // Update all scheduled appointments that are in the past
        $updated = Visitation::where('status', 'scheduled')
            ->where('visitation_date', '<', Carbon::today())
            ->update(['status' => 'completed']);
        
        if ($updated > 0) {
            \Log::info("Automatically marked $updated past appointments as completed");
        }
        
        return $updated;
    }

    /**
     * Get beneficiary details for a specific beneficiary
     */
    public function getBeneficiaryDetails($id)
    {
        try {
            $beneficiary = Beneficiary::find($id);
            
            if (!$beneficiary) {
                return response()->json(['success' => false, 'message' => 'Beneficiary not found'], 404);
            }
            
            // Format the phone number safely
            $phone = $beneficiary->mobile ?? 'Not Available';
            
            // Default address if relationships are missing
            $fullAddress = $beneficiary->street_address ?? 'Not Available';
            
            // Safely load relationships
            try {
                if ($beneficiary->barangay) {
                    $fullAddress .= ', ' . $beneficiary->barangay->barangay_name;
                }
                
                if ($beneficiary->municipality) {
                    $fullAddress .= ', ' . $beneficiary->municipality->municipality_name;
                }
            } catch (\Exception $e) {
                // If there's an issue with the relationships, just use the street address
                $fullAddress = $beneficiary->street_address ?? 'Not Available';
            }
            
            // Safely get care worker information
            $assignedCareWorker = null;
            try {
                if ($beneficiary->general_care_plan_id) {
                    $generalCarePlan = GeneralCarePlan::find($beneficiary->general_care_plan_id);
                    if ($generalCarePlan && $generalCarePlan->care_worker_id) {
                        $careWorker = User::find($generalCarePlan->care_worker_id);
                        if ($careWorker) {
                            $assignedCareWorker = [
                                'id' => $careWorker->id,
                                'name' => $careWorker->first_name . ' ' . $careWorker->last_name
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // If there's an issue getting the care worker, leave it as null
            }
            
            return response()->json([
                'success' => true,
                'beneficiary' => [
                    'id' => $beneficiary->beneficiary_id,
                    'name' => $beneficiary->first_name . ' ' . $beneficiary->last_name,
                    'phone' => $phone,
                    'address' => $fullAddress,
                    'care_worker' => $assignedCareWorker
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading beneficiary details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all beneficiaries for dropdown
     */
    public function getBeneficiaries(Request $request)
    {
        $user = Auth::user();
        $query = Beneficiary::query();
        
        // Filter by search term if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%")
                  ->orWhere('beneficiary_id', 'LIKE', "%{$search}%");
            });
        }
        
        // For care workers, only show assigned beneficiaries
        if ($user->isCareWorker()) {
            $beneficiaryIds = GeneralCarePlan::where('care_worker_id', $user->id)
                                           ->pluck('general_care_plan_id');
            $query->whereIn('general_care_plan_id', $beneficiaryIds);
        }
        
        $beneficiaries = $query->select('beneficiary_id', 'first_name', 'last_name')
                              ->orderBy('last_name')
                              ->get()
                              ->map(function($beneficiary) {
                                  return [
                                      'id' => $beneficiary->beneficiary_id,
                                      'name' => $beneficiary->first_name . ' ' . $beneficiary->last_name
                                  ];
                              });
        
        return response()->json(['success' => true, 'beneficiaries' => $beneficiaries]);
    }
    
    /**
     * Helper function to determine the correct view path based on user role
     */
    private function getViewPathForRole($user)
    {
        if ($user->isAdministrator() || $user->isExecutiveDirector()) {
            return 'admin.adminCareworkerAppointments';
        } elseif ($user->isCareManager()) {
            return 'careManager.careManagerCareworkerAppointments';
        } elseif ($user->isCareWorker()) {
            return 'careWorker.careWorkerAppointments';
        }
        
        // Default fallback
        return 'admin.adminCareworkerAppointments';
    }
    
    /**
     * Helper function to get color based on visitation status
     */
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'scheduled':
                return '#4e73df'; // Blue
            case 'completed':
                return '#1cc88a'; // Green
            case 'canceled':
                return '#e74a3b'; // Red
            default:
                return '#6c757d'; // Gray
        }
    }

    /**
     * Store a new appointment
     */
    public function storeAppointment(Request $request)
    {
        // Verify that either times are specified or is_flexible_time is checked
        if (!$request->has('is_flexible_time') && (!$request->has('start_time') || !$request->has('end_time') || 
            empty($request->start_time) || empty($request->end_time))) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'time' => ['Either specific times or flexible time must be selected.']
                ]
            ], 422);
        }
        
        $validator = Validator::make($request->all(), [
            'care_worker_id' => 'required|exists:cose_users,id',
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'visitation_date' => 'required|date|after_or_equal:today',
            'visit_type' => 'required|in:routine_care_visit,service_request,emergency_visit',
            'start_time' => 'required_if:is_flexible_time,0,null|nullable|date_format:H:i',
            'end_time' => 'required_if:is_flexible_time,0,null|nullable|date_format:H:i|after:start_time',
            'is_flexible_time' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ], [
            'start_time.required_if' => 'Start time is required when flexible time is not checked.',
            'end_time.required_if' => 'End time is required when flexible time is not checked.',
            'end_time.after' => 'End time must be after start time.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            $isRecurring = $request->input('is_recurring', false);
            
            $visitation = Visitation::create([
                'care_worker_id' => $request->care_worker_id,
                'beneficiary_id' => $request->beneficiary_id,
                'visitation_date' => $request->visitation_date,
                'visit_type' => $request->visit_type,
                'is_flexible_time' => filter_var($request->is_flexible_time, FILTER_VALIDATE_BOOLEAN),
                'start_time' => !$request->has('is_flexible_time') ? $request->start_time : null,
                'end_time' => !$request->has('is_flexible_time') ? $request->end_time : null,
                'notes' => $request->notes,
                'date_assigned' => now(),
                'assigned_by' => Auth::id(),
                'status' => 'scheduled'
            ]);
            
            // Handle recurring patterns if needed
            if ($isRecurring) {
                $patternType = $request->input('pattern_type', 'weekly');
                $dayOfWeek = null;
                
                if ($patternType === 'weekly' && $request->has('day_of_week')) {
                    // Join the array of days into a comma-separated string
                    $dayOfWeek = implode(',', (array)$request->input('day_of_week'));
                }
                
                RecurringPattern::create([
                    'visitation_id' => $visitation->visitation_id,
                    'pattern_type' => $patternType,
                    'day_of_week' => $dayOfWeek,
                    'recurrence_end' => $request->input('recurrence_end')
                ]);
            }
            
            // Send notifications
            $this->sendAppointmentNotifications($visitation, 'created');
            
            DB::commit();

            // Update statuses after creating a new appointment
             $this->updatePastAppointmentsStatus();
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment scheduled successfully',
                'visitation' => $visitation
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing appointment
     */
    public function updateAppointment(Request $request)
    {
        // Verify that either times are specified or is_flexible_time is checked
        if (!$request->has('is_flexible_time') && (!$request->has('start_time') || !$request->has('end_time') || 
            empty($request->start_time) || empty($request->end_time))) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'time' => ['Either specific times or flexible time must be selected.']
                ]
            ], 422);
        }
        
        $validator = Validator::make($request->all(), [
            'visitation_id' => 'required|exists:visitations,visitation_id',
            'care_worker_id' => 'required|exists:cose_users,id',
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'visitation_date' => 'required|date',
            'visit_type' => 'required|in:routine_care_visit,service_request,emergency_visit',
            'start_time' => 'required_if:is_flexible_time,0,null|nullable|date_format:H:i',
            'end_time' => 'required_if:is_flexible_time,0,null|nullable|date_format:H:i|after:start_time',
            'is_flexible_time' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ], [
            'start_time.required_if' => 'Start time is required when flexible time is not checked.',
            'end_time.required_if' => 'End time is required when flexible time is not checked.',
            'end_time.after' => 'End time must be after start time.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $visitation = Visitation::findOrFail($request->visitation_id);
    
        // Check if anything actually changed
        $isFlexibleTime = filter_var($request->is_flexible_time, FILTER_VALIDATE_BOOLEAN);
        $noChanges = (
            $visitation->care_worker_id == $request->care_worker_id &&
            $visitation->beneficiary_id == $request->beneficiary_id &&
            $visitation->visitation_date == $request->visitation_date &&
            $visitation->visit_type == $request->visit_type &&
            $visitation->is_flexible_time == $isFlexibleTime &&
            (!$isFlexibleTime ? $visitation->start_time == $request->start_time : true) &&
            (!$isFlexibleTime ? $visitation->end_time == $request->end_time : true) &&
            $visitation->notes == $request->notes
        );
        
        // Also check recurring pattern if applicable
        $isRecurring = $request->input('is_recurring', false);
        $currentPattern = $visitation->recurringPattern;
        
        if ($currentPattern && $isRecurring) {
            $patternType = $request->input('pattern_type', 'weekly');
            $dayOfWeek = null;
            
            if ($patternType === 'weekly' && $request->has('day_of_week')) {
                $dayOfWeek = implode(',', (array)$request->input('day_of_week'));
            }
            
            $noChanges = $noChanges && (
                $currentPattern->pattern_type == $patternType &&
                $currentPattern->day_of_week == $dayOfWeek &&
                $currentPattern->recurrence_end == $request->input('recurrence_end')
            );
        } else if (($currentPattern && !$isRecurring) || (!$currentPattern && $isRecurring)) {
            // Pattern presence changed
            $noChanges = false;
        }
        
        if ($noChanges) {
            return response()->json([
                'success' => false,
                'message' => 'No changes were made to the appointment.'
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            $isRecurring = $request->input('is_recurring', false);
            $visitation = Visitation::findOrFail($request->visitation_id);
            $today = Carbon::today();
            
            // Check if this is a recurring event and if the visitation date is in the past
            if ($visitation->recurringPattern && Carbon::parse($visitation->visitation_date)->lt($today)) {
                // Create a new appointment for future occurrences
                $newVisitation = Visitation::create([
                    'care_worker_id' => $request->care_worker_id,
                    'beneficiary_id' => $request->beneficiary_id,
                    'visitation_date' => $request->visitation_date,
                    'visit_type' => $request->visit_type,
                    'is_flexible_time' => filter_var($request->is_flexible_time, FILTER_VALIDATE_BOOLEAN),
                    'start_time' => filter_var($request->is_flexible_time, FILTER_VALIDATE_BOOLEAN) ? null : $request->start_time,
                    'end_time' => filter_var($request->is_flexible_time, FILTER_VALIDATE_BOOLEAN) ? null : $request->end_time,
                    'notes' => $request->notes,
                    'date_assigned' => now(),
                    'assigned_by' => Auth::id(),
                    'status' => 'scheduled'
                ]);
                
                // Update the recurrence end date of the old pattern to yesterday
                $oldPattern = $visitation->recurringPattern;
                $oldPattern->recurrence_end = $today->copy()->subDay()->format('Y-m-d');
                $oldPattern->save();
                
                // Create new recurring pattern
                if ($isRecurring) {
                    $patternType = $request->input('pattern_type', 'weekly');
                    $dayOfWeek = null;
                    
                    if ($patternType === 'weekly' && $request->has('day_of_week')) {
                        $dayOfWeek = implode(',', (array)$request->input('day_of_week'));
                    }
                    
                    RecurringPattern::create([
                        'visitation_id' => $newVisitation->visitation_id,
                        'pattern_type' => $patternType,
                        'day_of_week' => $dayOfWeek,
                        'recurrence_end' => $request->input('recurrence_end')
                    ]);
                }
                
                // Send notifications about the new appointment
                $this->sendAppointmentNotifications($newVisitation, 'created');
                
                DB::commit();
                
                // Update statuses after updating an appointment
                $this->updatePastAppointmentsStatus();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Future appointments updated successfully. Past appointments preserved.',
                    'visitation' => $newVisitation,
                    'split_occurred' => true
                ]);
            } else {
                // For non-recurring events or recurring events with first date in the future
                // Store old values for notification
                $oldCareWorkerId = $visitation->care_worker_id;
                $oldDate = $visitation->visitation_date;
                
                // Update visitation details
                $visitation->update([
                    'care_worker_id' => $request->care_worker_id,
                    'beneficiary_id' => $request->beneficiary_id,
                    'visitation_date' => $request->visitation_date,
                    'visit_type' => $request->visit_type,
                    'is_flexible_time' => filter_var($request->is_flexible_time, FILTER_VALIDATE_BOOLEAN),
                    'start_time' => $request->is_flexible_time == '1' ? null : $request->start_time,
                    'end_time' => $request->is_flexible_time == '1' ? null : $request->end_time,
                    'notes' => $request->notes
                ]);
                
                // Handle recurring patterns
                if ($isRecurring) {
                    $patternType = $request->input('pattern_type', 'weekly');
                    $dayOfWeek = null;
                    
                    if ($patternType === 'weekly' && $request->has('day_of_week')) {
                        $dayOfWeek = implode(',', (array)$request->input('day_of_week'));
                    }
                    
                    // Update or create recurring pattern
                    RecurringPattern::updateOrCreate(
                        ['visitation_id' => $visitation->visitation_id],
                        [
                            'pattern_type' => $patternType,
                            'day_of_week' => $dayOfWeek,
                            'recurrence_end' => $request->input('recurrence_end')
                        ]
                    );
                } else {
                    // Remove recurring pattern if it exists
                    RecurringPattern::where('visitation_id', $visitation->visitation_id)->delete();
                }
                
                // Send notifications about the update
                if ($oldCareWorkerId != $request->care_worker_id || $oldDate != $request->visitation_date) {
                    $this->sendAppointmentNotifications($visitation, 'updated');
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment updated successfully',
                    'visitation' => $visitation,
                    'split_occurred' => false
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an appointment with enhanced options for recurring events
     */
    public function cancelAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visitation_id' => 'required|exists:visitations,visitation_id',
            'reason' => 'required|string|max:500',
            'password' => 'required|string',
            'cancel_option' => 'sometimes|in:this,future',
            'occurrence_date' => 'sometimes|date'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verify user password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'passwordError' => 'The password is incorrect.'
            ], 401);
        }
        
        DB::beginTransaction();
        
        try {
            $visitation = Visitation::findOrFail($request->visitation_id);
            $isRecurring = $visitation->recurringPattern ? true : false;
            $today = Carbon::today();
            $occurrenceDate = $request->has('occurrence_date') ? Carbon::parse($request->occurrence_date) : null;
            
            // Different handling based on recurring status and cancel option
            if ($isRecurring) {
                if ($request->cancel_option === 'this') {
                    // Handle single occurrence cancellation
                    $this->cancelSingleOccurrence($visitation, $occurrenceDate, $request->reason);
                    $message = 'This occurrence has been cancelled.';
                } else {
                    // Handle this and future occurrences cancellation
                    $this->cancelFutureOccurrences($visitation, $occurrenceDate, $request->reason);
                    $message = 'This and all future occurrences have been cancelled.';
                }
            } else {
                // Non-recurring appointment - simple cancellation
                $visitation->status = 'canceled';
                $visitation->notes = ($visitation->notes ? $visitation->notes . "\n\n" : '') . 
                                    "Cancellation reason: " . $request->reason . 
                                    "\nCancelled by: " . Auth::user()->first_name . ' ' . Auth::user()->last_name . 
                                    "\nCancelled on: " . now()->format('Y-m-d H:i:s');
                $visitation->save();
                $message = 'Appointment has been cancelled.';
            }
            
            // Send cancellation notifications
            $this->sendAppointmentNotifications($visitation, 'canceled', $request->reason);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a single occurrence of a recurring event by creating a cancelled exception
     */
    private function cancelSingleOccurrence(Visitation $visitation, $occurrenceDate, $reason)
    {
        if (!$occurrenceDate) {
            throw new \Exception("Occurrence date is required for cancelling a single occurrence");
        }
        
        // Create a "cancelled exception" entry
        $exception = new VisitationException([
            'visitation_id' => $visitation->visitation_id,
            'exception_date' => $occurrenceDate,
            'status' => 'canceled',
            'reason' => $reason,
            'created_by' => Auth::id(),
            'created_at' => now()
        ]);
        
        $exception->save();
    }

    /**
     * Cancel this and all future occurrences of a recurring event
     */
    private function cancelFutureOccurrences(Visitation $visitation, $occurrenceDate, $reason)
    {
        if (!$occurrenceDate) {
            throw new \Exception("Occurrence date is required for cancelling future occurrences");
        }
        
        $recurringPattern = $visitation->recurringPattern;
        
        if (!$recurringPattern) {
            throw new \Exception("No recurring pattern found for this visitation");
        }
        
        // If occurrence date is the original date of the visitation, cancel the whole series
        if ($visitation->visitation_date == $occurrenceDate->format('Y-m-d')) {
            $visitation->status = 'canceled';
            $visitation->notes = ($visitation->notes ? $visitation->notes . "\n\n" : '') . 
                                "Cancellation reason: " . $reason . 
                                "\nCancelled by: " . Auth::user()->first_name . ' ' . Auth::user()->last_name . 
                                "\nCancelled on: " . now()->format('Y-m-d H:i:s');
            $visitation->save();
        } else {
            // Otherwise, adjust the recurring pattern to end before this occurrence
            $dayBefore = $occurrenceDate->copy()->subDay();
            $recurringPattern->recurrence_end = $dayBefore->format('Y-m-d');
            $recurringPattern->save();
            
            // Create a "cancelled exception" entry for this specific occurrence
            $exception = new VisitationException([
                'visitation_id' => $visitation->visitation_id,
                'exception_date' => $occurrenceDate,
                'status' => 'canceled',
                'reason' => $reason,
                'created_by' => Auth::id(),
                'created_at' => now()
            ]);
            
            $exception->save();
        }
    }

    /**
     * Send notifications to relevant stakeholders about appointment changes
     */
    private function sendAppointmentNotifications(Visitation $visitation, string $action, string $reason = null)
    {
        $beneficiary = Beneficiary::find($visitation->beneficiary_id);
        $careWorker = User::find($visitation->care_worker_id);
        $familyMembers = FamilyMember::where('related_beneficiary_id', $visitation->beneficiary_id)->get();
        
        $dateFormatted = Carbon::parse($visitation->visitation_date)->format('l, F j, Y');
        $timeInfo = $visitation->is_flexible_time ? 
            'flexible time (to be determined)' : 
            'from ' . Carbon::parse($visitation->start_time)->format('g:i A') . ' to ' . 
            Carbon::parse($visitation->end_time)->format('g:i A');
        
        $visitType = ucwords(str_replace('_', ' ', $visitation->visit_type));
        
        switch ($action) {
            case 'created':
                $title = "New Appointment Scheduled";
                $message = "A new appointment has been scheduled for {$beneficiary->first_name} {$beneficiary->last_name} " . 
                        "with care worker {$careWorker->first_name} {$careWorker->last_name} on {$dateFormatted} at {$timeInfo}. " .
                        "Visit type: {$visitType}.";
                break;
                
            case 'updated':
                $title = "Appointment Updated";
                $message = "The appointment for {$beneficiary->first_name} {$beneficiary->last_name} " . 
                        "with care worker {$careWorker->first_name} {$careWorker->last_name} has been updated. " .
                        "New schedule: {$dateFormatted} at {$timeInfo}. " .
                        "Visit type: {$visitType}.";
                break;
                
            case 'canceled':
                $title = "Appointment Canceled";
                $message = "The appointment for {$beneficiary->first_name} {$beneficiary->last_name} " . 
                        "with care worker {$careWorker->first_name} {$careWorker->last_name} on {$dateFormatted} " .
                        "has been canceled. Reason: {$reason}";
                break;
                
            default:
                return;
        }
        
        // Notify the care worker
        Notification::create([
            'user_id' => $careWorker->id,
            'user_type' => 'cose_staff',
            'message_title' => $title,
            'message' => $message,
            'date_created' => now(),
            'is_read' => false
        ]);
        
        // Notify beneficiary if they have portal access
        if ($beneficiary->portal_account_id) {
            Notification::create([
                'user_id' => $beneficiary->beneficiary_id,
                'user_type' => 'beneficiary',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
        
        // Notify all family members
        foreach ($familyMembers as $familyMember) {
            if ($familyMember->portal_account_id) {
                Notification::create([
                    'user_id' => $familyMember->family_member_id,
                    'user_type' => 'family_member',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now(),
                    'is_read' => false
                ]);
            }
        }
    }

    

}