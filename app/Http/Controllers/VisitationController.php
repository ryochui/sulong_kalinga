<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Http\Controllers\VisitationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitation;
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
        
        $visitations = Visitation::with(['beneficiary', 'careWorker', 'recurringPattern'])
            ->whereBetween('visitation_date', [$startDate, $endDate]);
        
        // Filter by role
        if ($user->isCareWorker()) {
            $visitations->where('care_worker_id', $user->id);
        } elseif ($user->isCareManager()) {
            // Care managers see visitations for care workers they manage
            $careWorkerIds = User::where('assigned_care_manager_id', $user->id)->pluck('id');
            $visitations->whereIn('care_worker_id', $careWorkerIds);
        }
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            
            $visitations->whereHas('beneficiary', function($query) use ($search) {
                $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%");
            })->orWhereHas('careWorker', function($query) use ($search) {
                $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%");
            });
        }
        
        // Apply care worker filter if provided
        if ($request->has('care_worker_id') && !empty($request->care_worker_id)) {
            $visitations->where('care_worker_id', $request->care_worker_id);
        }
        
        // Apply status filter if provided
        if ($request->has('status') && !empty($request->status)) {
            $visitations->where('status', $request->status);
        }
        
        $visitations = $visitations->get();
        
        // Format visitations for fullcalendar
        $events = [];
        
        foreach ($visitations as $visitation) {
            $color = $this->getStatusColor($visitation->status);
            
            $title = $visitation->beneficiary->first_name . ' ' . $visitation->beneficiary->last_name;
            if ($user->role_id <= 2) {
                $title .= ' (Care Worker: ' . $visitation->careWorker->first_name . ' ' . $visitation->careWorker->last_name . ')';
            }
            
            $event = [
                'id' => $visitation->visitation_id,
                'title' => $title,
                'start' => $visitation->visitation_date . ($visitation->is_flexible_time ? '' : 'T' . $visitation->start_time),
                'end' => $visitation->visitation_date . ($visitation->is_flexible_time ? '' : 'T' . $visitation->end_time),
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
                    'phone' => $visitation->beneficiary->mobile ?  $visitation->beneficiary->mobile : 'Not Available',
                    'address' => $visitation->beneficiary->street_address,
                    'recurring' => $visitation->recurringPattern ? true : false,
                    'recurring_pattern' => $visitation->recurringPattern ? [
                        'type' => $visitation->recurringPattern->pattern_type,
                        'day_of_week' => $visitation->recurringPattern->day_of_week,
                        'end_date' => $visitation->recurringPattern->recurrence_end
                    ] : null
                ]
            ];
            
            $events[] = $event;
        }
        
        return response()->json($events);
    }
    
    /**
     * Get care worker assigned to a specific beneficiary
     */
    public function getBeneficiaryDetails(Request $request, $id)
    {
        $beneficiary = Beneficiary::with(['barangay', 'municipality', 'generalCarePlan.careWorker'])->find($id);
        
        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found'], 404);
        }
        
        // Format the phone number with +63 prefix
        $phone = $beneficiary->mobile ? '+63' . ltrim($beneficiary->mobile, '0') : null;
        
        $assignedCareWorker = null;
        
        // Get care worker from general care plan if available
        if ($beneficiary->general_care_plan_id) {
            $generalCarePlan = GeneralCarePlan::with('careWorker')->find($beneficiary->general_care_plan_id);
            if ($generalCarePlan && $generalCarePlan->careWorker) {
                $assignedCareWorker = [
                    'id' => $generalCarePlan->careWorker->id,
                    'name' => $generalCarePlan->careWorker->first_name . ' ' . $generalCarePlan->careWorker->last_name
                ];
            }
        }
        
        // Full address with barangay and municipality
        $fullAddress = $beneficiary->street_address;
        
        if ($beneficiary->barangay) {
            $fullAddress .= ', ' . $beneficiary->barangay->barangay_name;
        }
        
        if ($beneficiary->municipality) {
            $fullAddress .= ', ' . $beneficiary->municipality->municipality_name;
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
}