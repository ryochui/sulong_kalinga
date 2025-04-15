<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Beneficiary;
use App\Models\WeeklyCarePlan;
use App\Models\CareCategory;
use App\Models\Intervention;
use App\Models\WeeklyCarePlanInterventions;
use App\Models\VitalSigns;


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
        
        // Check user role and return appropriate view
        if (Auth::user()->role_id == 1) { // Admin user
            return view('admin.weeklyCareplan', compact('beneficiaries', 'careCategories'));
        } else { // Care worker or other roles
            return view('careWorker.weeklyCareplan', compact('beneficiaries', 'careCategories'));
        }
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
        Log::info('Store method called with request data:', $request->all());

        // Validate the request
        $validated = $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'assessment' => 'required|string|min:20|max:5000',
            'blood_pressure' => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'body_temperature' => 'required|numeric|between:35,42',
            'pulse_rate' => 'required|integer|between:40,200',
            'respiratory_rate' => 'required|integer|between:8,40',
            'evaluation_recommendations' => 'required|string|min:20|max:5000',
            'selected_interventions' => 'required|array',
            'duration_minutes' => 'required|array',
            'duration_minutes.*' => 'required|numeric|min:0.01|max:999.99',

            // Custom interventions validations
            'custom_category.*' => 'sometimes|nullable|exists:care_categories,care_category_id',
            'custom_description.*' => 'sometimes|nullable|required_with:custom_category.*|string|min:5|max:255|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s,.!?;:()\-\'\"]+$/',
            'custom_duration.*' => 'sometimes|nullable|required_with:custom_category.*|numeric|min:0.01|max:999.99',
        ], [
            'beneficiary_id.required' => 'Please select a beneficiary',
            'beneficiary_id.exists' => 'The selected beneficiary is invalid',

            'assessment.required' => 'Please provide an assessment',
            'assessment.min' => 'Assessment must be at least 20 characters',
            'assessment.max' => 'Assessment cannot exceed 5000 characters',
            'assessment.regex' => 'Assessment must contain some text and cannot consist of only numbers or symbols',

            'blood_pressure.required' => 'Blood pressure is required',
            'blood_pressure.regex' => 'Blood pressure must be in format 120/80',

            'body_temperature.required' => 'Body temperature is required',
            'body_temperature.between' => 'Body temperature must be between 35°C and 42°C',

            'pulse_rate.required' => 'Pulse rate is required',
            'pulse_rate.between' => 'Pulse rate must be between 40 and 200 bpm',

            'respiratory_rate.required' => 'Respiratory rate is required',
            'respiratory_rate.between' => 'Respiratory rate must be between 8 and 40 bpm',

            'evaluation_recommendations.required' => 'Please provide evaluation recommendations',
            'evaluation_recommendations.min' => 'Evaluation recommendations must be at least 20 characters',
            'evaluation_recommendations.max' => 'Evaluation recommendations cannot exceed 5000 characters',
            'evaluation_recommendations.regex' => 'Evaluation recommendations must contain some text and cannot consist of only numbers or symbols',

            'selected_interventions.required' => 'Please select at least one intervention',

            'duration_minutes.*.required' => 'Please specify the duration for all selected interventions',
            'duration_minutes.*.numeric' => 'Duration must be a number',
            'duration_minutes.*.min' => 'Duration must be greater than 0',
            'duration_minutes.*.max' => 'Duration cannot exceed 999.99 minutes',

            'custom_category.*.exists' => 'Invalid care category selected',

            'custom_description.*.required_with' => 'Please provide a description for custom interventions',
            'custom_description.*.min' => 'Custom intervention description must be at least 5 characters',
            'custom_description.*.max' => 'Custom intervention description cannot exceed 255 characters',
            'custom_description.*.regex' => 'Custom intervention description must contain text and can only include letters, numbers, and basic punctuation',

            'custom_duration.*.required_with' => 'Please provide a duration for custom interventions',
            'custom_duration.*.numeric' => 'Custom intervention duration must be a number',
            'custom_duration.*.min' => 'Custom intervention duration must be greater than 0',
            'custom_duration.*.max' => 'Custom intervention duration cannot exceed 999.99 minutes',
        ]);
        
        Log::info('Validation passed');

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
            Log::info('Vital signs created: ' . $vitalSigns->id);

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
            Log::info('Weekly care plan created: ' . $weeklyCarePlan->id);

            // 3. Save ONLY selected interventions with valid durations
            if ($request->has('selected_interventions') && is_array($request->selected_interventions)) {
                foreach ($request->selected_interventions as $interventionId) {
                    // Only save if there's a valid duration for this intervention
                    if (isset($request->duration_minutes[$interventionId]) && 
                        $request->duration_minutes[$interventionId] > 0) {
                        
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
                            Log::info('Intervention saved: ' . $interventionId . ' with duration: ' . $request->duration_minutes[$interventionId]);
                        } else {
                            Log::warning('Intervention not found: ' . $interventionId);
                        }
                    } else {
                        Log::info('Skipping intervention ' . $interventionId . ' - no valid duration provided');
                    }
                }
            }

            // 4. Save ONLY custom interventions with valid descriptions AND durations
            if ($request->has('custom_category') && is_array($request->custom_category)) {
                foreach ($request->custom_category as $index => $categoryId) {
                    // Only save if there's both a description AND a valid duration
                    if (!empty($categoryId) && 
                        !empty($request->custom_description[$index]) &&
                        isset($request->custom_duration[$index]) && 
                        $request->custom_duration[$index] > 0) {
                        
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => null, // Custom intervention doesn't have an ID
                            'care_category_id' => $categoryId,
                            'intervention_description' => $request->custom_description[$index],
                            'duration_minutes' => $request->custom_duration[$index],
                            'implemented' => true
                        ]);
                        Log::info('Custom intervention saved: ' . $categoryId . ' with duration: ' . $request->custom_duration[$index]);
                    } else {
                        Log::info('Skipping custom intervention for category ' . $categoryId . ' - missing description or valid duration');
                    }
                }
            }

            DB::commit();

            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.weeklycareplans.create')
                    ->with('success', 'Weekly care plan created successfully!');
            } else {
                return redirect()->route('careworker.weeklycareplans.create') 
                    ->with('success', 'Weekly care plan created successfully!');
            }
                
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
            
            if (Auth::user()->role_id == 1) {
                return view('admin.weeklyCarePlansList', compact('weeklyCarePlans'));
            } else {
                return view('careWorker.weeklyCarePlansList', compact('weeklyCarePlans'));
            }
    }

    public function customInterventions()
    {
        return $this->hasMany(WeeklyCarePlanInterventions::class, 'weekly_care_plan_id')
                    ->whereNull('intervention_id');
    }

    public function show($id)
    {
        // Get the weekly care plan with beneficiary
        $weeklyCareplan = WeeklyCarePlan::with('beneficiary', 
        'beneficiary.generalCarePlan.healthHistory', 
        'vitalSigns',
        'acknowledgedByBeneficiary',
        'acknowledgedByFamily')
            ->findOrFail($id);
        
        // Explicitly fetch standard interventions with correct relationship structure
        $standardInterventions = DB::table('weekly_care_plan_interventions as wpi')
            ->join('interventions as i', 'wpi.intervention_id', '=', 'i.intervention_id')
            ->where('wpi.weekly_care_plan_id', $id)
            ->whereNotNull('wpi.intervention_id')
            ->select('i.intervention_description', 'wpi.duration_minutes', 'i.care_category_id')
            ->get();
        
        // Group standard interventions by category
        $interventionsByCategory = $standardInterventions->groupBy('care_category_id');
        
        // Get custom interventions (with null intervention_id)
        $customInterventions = DB::table('weekly_care_plan_interventions')
            ->where('weekly_care_plan_id', $id)
            ->whereNull('intervention_id')
            ->get();
        
        // Get all care categories
        $categories = CareCategory::all();
        
        // Check user role and return appropriate view
        if (Auth::user()->role_id == 1) { // Admin user
            return view('admin.viewWeeklyCareplan', compact(
                'weeklyCareplan',
                'interventionsByCategory',
                'customInterventions',
                'categories'
            ));
        } else { // Care worker or other roles
            return view('careWorker.viewWeeklyCareplan', compact(
                'weeklyCareplan',
                'interventionsByCategory',
                'customInterventions',
                'categories'
            ));
        }
    }

    /*  public function edit($id)
        {
            $weeklyCarePlan = WeeklyCarePlan::findOrFail($id);
            
            // Only allow editing if the user is an admin, care manager or the original author
            if (Auth::user()->role_id > 2 && $weeklyCarePlan->created_by != Auth::id()) {
                return redirect()->back()->with('error', 'You are not authorized to edit this care plan');
            }
            
            // Proceed with editing
            return view('weeklycareplans.edit', compact('weeklyCarePlan'));
        } 
    */

    public function edit($id)
    {
        // Get the weekly care plan with all related data
        $weeklyCarePlan = WeeklyCarePlan::with([
            'beneficiary', 
            'beneficiary.generalCarePlan.healthHistory',
            'vitalSigns',
            'interventions'
        ])->findOrFail($id);
        
        // Check authorization - only allow admins or the creator to edit
        if (Auth::user()->role_id > 2 && $weeklyCarePlan->care_worker_id != Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to edit this care plan');
        }
        
        // Get all beneficiaries for dropdown
        $beneficiaries = Beneficiary::orderBy('last_name')->orderBy('first_name')->get();
        
        // Get care categories with interventions
        $careCategories = CareCategory::with('interventions')->get();
        
        // Group interventions by category and format for the form
        $selectedInterventions = [];
        $interventionDurations = [];
        
        foreach ($weeklyCarePlan->interventions as $intervention) {
            if ($intervention->intervention_id) {
                // Standard intervention
                $selectedInterventions[] = $intervention->intervention_id;
                $interventionDurations[$intervention->intervention_id] = $intervention->duration_minutes;
            }
        }
        
        // Get custom interventions
        $customInterventions = $weeklyCarePlan->interventions()
            ->whereNull('intervention_id')
            ->get();

        $customInterventionsByCategory = [];
        foreach ($customInterventions as $intervention) {
            if (!isset($customInterventionsByCategory[$intervention->care_category_id])) {
                $customInterventionsByCategory[$intervention->care_category_id] = [];
            }
            $customInterventionsByCategory[$intervention->care_category_id][] = $intervention;
        }


        
        // Check user role and return appropriate view
        if (Auth::user()->role_id == 1) { // Admin
            return view('admin.editWeeklyCareplan', compact(
                'weeklyCarePlan',
                'beneficiaries',
                'careCategories',
                'selectedInterventions',
                'interventionDurations',
                'customInterventions',
                'customInterventionsByCategory'
            ));
        } else { // Care worker
            return view('careWorker.editWeeklyCareplan', compact(
                'weeklyCarePlan',
                'beneficiaries',
                'careCategories',
                'selectedInterventions',
                'interventionDurations',
                'customInterventions',
                'customInterventionsByCategory'
            ));
        }
    }

    public function update(Request $request, $id)
    {
        // Find the weekly care plan
        $weeklyCarePlan = WeeklyCarePlan::findOrFail($id);
        
        // Check authorization - only allow admins or the creator to update
        if (Auth::user()->role_id > 2 && $weeklyCarePlan->care_worker_id != Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to update this care plan');
        }

        // Validate the request (same validation as store method)
        $validated = $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,beneficiary_id',
            'assessment' => 'required|string|min:20|max:5000',
            'blood_pressure' => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'body_temperature' => 'required|numeric|between:35,42',
            'pulse_rate' => 'required|integer|between:40,200',
            'respiratory_rate' => 'required|integer|between:8,40',
            'evaluation_recommendations' => 'required|string|min:20|max:5000',
            'selected_interventions' => 'required|array',
            'duration_minutes' => 'required|array',
            'duration_minutes.*' => 'required|numeric|min:0.01|max:999.99',
            'custom_category.*' => 'sometimes|nullable|exists:care_categories,care_category_id',
            'custom_description.*' => 'sometimes|nullable|required_with:custom_category.*|string|min:5|max:255|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9\s,.!?;:()\-\'\"]+$/',
            'custom_duration.*' => 'sometimes|nullable|required_with:custom_category.*|numeric|min:0.01|max:999.99',
        ]);

        try {
            DB::beginTransaction();
            
            // 1. Update vital signs
            $vitalSigns = VitalSigns::findOrFail($weeklyCarePlan->vital_signs_id);
            $vitalSigns->update([
                'blood_pressure' => $request->blood_pressure,
                'body_temperature' => $request->body_temperature,
                'pulse_rate' => $request->pulse_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'updated_by' => Auth::id(),
            ]);
            
            // 2. Update weekly care plan
            $weeklyCarePlan->update([
                'beneficiary_id' => $request->beneficiary_id,
                'assessment' => $request->assessment,
                'evaluation_recommendations' => $request->evaluation_recommendations,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
            
            // 3. Delete all existing interventions
            $weeklyCarePlan->interventions()->delete();
            
            // 4. Add selected interventions
            if ($request->has('selected_interventions') && is_array($request->selected_interventions)) {
                foreach ($request->selected_interventions as $interventionId) {
                    if (isset($request->duration_minutes[$interventionId]) && 
                        $request->duration_minutes[$interventionId] > 0) {
                        
                        $intervention = Intervention::find($interventionId);
                        
                        if ($intervention) {
                            WeeklyCarePlanInterventions::create([
                                'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                                'intervention_id' => $interventionId,
                                'care_category_id' => $intervention->care_category_id,
                                'intervention_description' => null,
                                'duration_minutes' => $request->duration_minutes[$interventionId],
                                'implemented' => true
                            ]);
                        }
                    }
                }
            }
            
            // 5. Add custom interventions
            if ($request->has('custom_category') && is_array($request->custom_category)) {
                foreach ($request->custom_category as $index => $categoryId) {
                    if (!empty($categoryId) && 
                        !empty($request->custom_description[$index]) &&
                        isset($request->custom_duration[$index]) && 
                        $request->custom_duration[$index] > 0) {
                        
                        WeeklyCarePlanInterventions::create([
                            'weekly_care_plan_id' => $weeklyCarePlan->weekly_care_plan_id,
                            'intervention_id' => null,
                            'care_category_id' => $categoryId,
                            'intervention_description' => $request->custom_description[$index],
                            'duration_minutes' => $request->custom_duration[$index],
                            'implemented' => true
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.weeklycareplans.show', $weeklyCarePlan->weekly_care_plan_id)
                    ->with('success', 'Weekly care plan updated successfully!');
            } else {
                return redirect()->route('careworker.weeklycareplans.show', $weeklyCarePlan->weekly_care_plan_id)
                    ->with('success', 'Weekly care plan updated successfully!');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating weekly care plan: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Database error: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Log incoming request for debugging
            Log::info('Delete request received for weekly care plan ID: ' . $id);
            
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is required to confirm deletion.'
                ]);
            }

            // Verify the password
            if (!Hash::check($request->password, auth()->user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The password you entered is incorrect.'
                ]);
            }

            // Find the weekly care plan
            $weeklyCarePlan = WeeklyCarePlan::findOrFail($id);
            
            // Begin database transaction
            DB::beginTransaction();
            
            try {
                // 1. Delete interventions first
                $weeklyCarePlan->interventions()->delete();
                Log::info('Deleted interventions for weekly care plan: ' . $id);
                
                // 2. Get the vital signs ID before deleting the care plan
                $vitalSignsId = $weeklyCarePlan->vital_signs_id;
                
                // 3. Delete the weekly care plan
                $weeklyCarePlan->delete();
                Log::info('Deleted weekly care plan: ' . $id);
                
                // 4. Delete vital signs
                if ($vitalSignsId) {
                    VitalSigns::where('vital_signs_id', $vitalSignsId)->delete();
                    Log::info('Deleted vital signs: ' . $vitalSignsId);
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Weekly care plan deleted successfully.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Transaction error: ' . $e->getMessage());
                throw $e; // Re-throw to be caught by outer catch
            }
        } 
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Weekly care plan not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Weekly care plan not found.'
            ], 404);
        }
        catch (\Exception $e) {
            // Log detailed error information
            Log::error('Weekly care plan deletion error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request: ' . $e->getMessage()
            ], 500);
        }
    }
}