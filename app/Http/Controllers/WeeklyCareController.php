<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\WeeklyCarePlan;
use App\Models\CareCategory;
use App\Models\Intervention;
use App\Models\WeeklyCarePlanInterventions;
use App\Models\VitalSigns;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeeklyCareController extends Controller
{
    /**
     * Show the form for creating a new weekly care plan
     */
    public function create()
    {
        // Fetch all beneficiaries, regardless of who creates the WCP
        $beneficiaries = Beneficiary::orderBy('last_name')->orderBy('first_name')->get();
        
        // Get all care categories with their interventions
        $careCategories = CareCategory::with('interventions')->get();
        
        return view('careWorker.weeklyCareplan', compact('beneficiaries', 'careCategories'));
    }
    
    /**
     * Get beneficiary details via AJAX
     */
    public function getBeneficiaryDetails($id)
    {
        try {
            $beneficiary = Beneficiary::with('generalCarePlan.healthHistory')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $beneficiary
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching beneficiary details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve beneficiary details'
            ], 500);
        }
    }
    
    /**
     * Store a new weekly care plan
     */
    public function store(Request $request)
    {
        // Validate the request - this will automatically redirect back with errors
        $validated = $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'assessment' => 'required|string',
            'blood_pressure' => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'body_temperature' => 'required|numeric|between:35,42',
            'pulse_rate' => 'required|integer|between:40,200',
            'respiratory_rate' => 'required|integer|between:8,40',
            'evaluation_recommendations' => 'required|string',
            'selected_interventions' => 'required|array',
            'duration_minutes' => 'required|array',
            'duration_minutes.*' => 'required|numeric|min:0.01|max:999.99',
        ], [
            'beneficiary_id.required' => 'Please select a beneficiary',
            'assessment.required' => 'Please provide an assessment',
            'blood_pressure.required' => 'Blood pressure is required',
            'body_temperature.required' => 'Body temperature is required',
            'pulse_rate.required' => 'Pulse rate is required',
            'respiratory_rate.required' => 'Respiratory rate is required',
            'evaluation_recommendations.required' => 'Please provide evaluation recommendations',
            'selected_interventions.required' => 'Please select at least one intervention',
            'duration_minutes.*.required' => 'Please specify the duration for all selected interventions',
            'duration_minutes.*.numeric' => 'Duration must be a number',
            'duration_minutes.*.min' => 'Duration must be greater than 0',
        ]);
        
        try {
            DB::beginTransaction();
            
            // 1. Create vital signs record
            $vitalSigns = VitalSigns::create([
                'blood_pressure' => $request->blood_pressure,
                'body_temperature' => $request->body_temperature,
                'pulse_rate' => $request->pulse_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'created_by' => Auth::id(),
            ]);
            
            // 2. Create weekly care plan
            $weeklyCarePlan = WeeklyCarePlan::create([
                'beneficiary_id' => $request->beneficiary_id,
                'care_worker_id' => Auth::id(),
                'vital_signs_id' => $vitalSigns->vital_signs_id,
                'date' => now(),
                'assessment' => $request->assessment,
                'evaluation_recommendations' => $request->evaluation_recommendations,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            
            // 3. Save selected interventions
            foreach ($request->selected_interventions as $index => $interventionId) {
                // Get intervention record to determine care category
                $intervention = Intervention::find($interventionId);
                
                if ($intervention) {
                    WeeklyCarePlanInterventions::create([
                        'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                        'intervention_id' => $interventionId,
                        'care_category_id' => $intervention->care_category_id,
                        'intervention_description' => null, // This is a pre-defined intervention
                        'duration_minutes' => $request->duration_minutes[$interventionId],
                        'implemented' => true
                    ]);
                }
            }
            
            // 4. Save custom interventions if any
            if ($request->has('custom_category') && is_array($request->custom_category)) {
                foreach ($request->custom_category as $index => $categoryId) {
                    if (!empty($categoryId) && !empty($request->custom_description[$index])) {
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => null, // Custom intervention doesn't have an ID
                            'care_category_id' => $categoryId,
                            'intervention_description' => $request->custom_description[$index],
                            'duration_minutes' => $request->custom_duration[$index] ?? 0,
                            'implemented' => true
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('weeklycareplans.create')
                ->with('success', 'Weekly care plan created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating weekly care plan: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        }
    }
    
    /**
     * Display a listing of the weekly care plans
     */
    public function index()
    {
        $weeklyCarePlans = WeeklyCarePlan::with(['beneficiary', 'vitalSigns', 'interventions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('careWorker.weeklyCarePlansList', compact('weeklyCarePlans'));
    }
}