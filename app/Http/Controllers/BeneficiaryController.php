<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Beneficiary;
use App\Models\Medication;
use App\Models\GeneralCarePlan;
use App\Models\CareNeed;
use App\Models\BeneficiaryCategory;
use App\Models\BeneficiaryStatus;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\CareWorkerResponsibility;
use App\Models\PortalAccount;
use App\Models\User;
use App\Models\EmotionalWellbeing;
use App\Models\CognitiveFunction;
use App\Models\Mobility;
use App\Models\HealthHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch all categories and statuses
        $categories = BeneficiaryCategory::all();
        $statuses = BeneficiaryStatus::all();

        // Fetch beneficiaries based on the search query and filters
        $beneficiaries = Beneficiary::with('category', 'status', 'municipality')
            ->when($search, function ($query, $search) {
                return $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                             ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->when($filter, function ($query, $filter) {
                if ($filter == 'category') {
                    return $query->orderBy('category_id');
                } elseif ($filter == 'status') {
                    return $query->orderBy('beneficiary_status_id');
                } elseif ($filter == 'municipality') {
                    return $query->orderBy('municipality_id');
                }
            })
            ->orderBy('first_name') // Order by first name alphabetically by default
            ->get();

        // Pass the data to the Blade template
        return view('admin.beneficiaryProfile', compact('beneficiaries', 'search', 'filter', 'categories', 'statuses'));
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|string',
            'reason' => 'required|string'
        ]);

        try {
            $beneficiary = Beneficiary::findOrFail($id);
            $status = BeneficiaryStatus::where('status_name', $request->input('status'))->firstOrFail();
            $beneficiary->beneficiary_status_id = $status->beneficiary_status_id;
            $beneficiary->status_reason = $request->input('reason');
            $beneficiary->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
            $beneficiary->updated_at = now(); // Set the updated_at column to the current timestamp
            $beneficiary->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error updating beneficiary status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function activate($id, Request $request)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $beneficiary->beneficiary_status_id = 1;
        $beneficiary->status_reason = null;
        $beneficiary->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
        $beneficiary->updated_at = now(); // Set the updated_at column to the current timestamp
        $beneficiary->save();

        return response()->json(['success' => true]);
    }

    public function viewProfileDetails(Request $request)
    {
        // Fetch care needs with care_category_id = 1
        $categories = BeneficiaryCategory::all();
        $beneficiary_id = $request->input('beneficiary_id');
        $beneficiary = Beneficiary::with([
            'category', 
            'barangay', 
            'municipality', 
            'status',
            'generalCarePlan.mobility', 
            'generalCarePlan.cognitiveFunction', 
            'generalCarePlan.emotionalWellbeing', 
            'generalCarePlan.mobility',
            'generalCarePlan.medications',
            'generalCarePlan.healthHistory',
            'generalCarePlan.careWorkerResponsibility',
            ])->find($beneficiary_id);
        if (!$beneficiary) {
            return redirect()->route('beneficiaryProfile')->with('error', 'Beneficiary not found.');
        }

        $careNeeds1 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 1);
        $careNeeds2 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 2);
        $careNeeds3 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 3);
        $careNeeds4 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 4);
        $careNeeds5 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 5);
        $careNeeds6 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 6);
        $careNeeds7 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 7);
        
        // Get the first care worker responsibility for each general care plan
        $careWorkerResponsibility = $beneficiary->generalCarePlan->careWorkerResponsibility->first();
        $careWorker = $careWorkerResponsibility ? $careWorkerResponsibility->careWorker : null;

        return view('admin.viewProfileDetails', compact('beneficiary', 'careNeeds1', 'careNeeds2', 'careNeeds3', 'careNeeds4', 'careNeeds5', 'careNeeds6', 'careNeeds7', 'careWorker')); 
    }

    public function editProfile(Request $request)
    {
        $beneficiary_id = $request->input('beneficiary_id');
        $beneficiary = Beneficiary::with(['category', 'barangay', 'municipality', 'status'])->find($beneficiary_id);
        $beneficiary = Beneficiary::with(['generalCarePlan.mobility', 'generalCarePlan.cognitiveFunction', 'generalCarePlan.emotionalWellbeing', 'generalCarePlan.medications'])->find($request->beneficiary_id);


        if (!$beneficiary) {
            return redirect()->route('beneficiaryProfile')->with('error', 'Beneficiary not found.');
        }

        return view('admin.editProfile', compact('beneficiary')); 
    }

    public function create()
    {
        // Fetch all municipalities from the database
        $municipalities = Municipality::all();
        $barangays = Barangay::all();
        $careWorkers = User::where('role_id', 3)
                        ->where('status', 'Active')
                        ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) AS name"))
                        ->get(); 
        $categories = DB::table('beneficiary_categories')->get(); // Fetch categories

        // Pass the municipalities to the view
        return view('admin.addBeneficiary', compact('municipalities', 'barangays', 'careWorkers', 'categories'));
    }

    public function storeBeneficiary(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
            'first_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$/', // First letter uppercase, allows hyphens and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$/', // First letter uppercase, allows hyphens and spaces
            ],
            'civil_status' => [
                'required',
                'string',
                'in:Single,Married,Widowed,Divorced', // Must match one of the predefined options
            ],
            'gender' => [
                'required',
                'string',
                'in:Male,Female,Other', // Must match one of the predefined options
            ],
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(14)->toDateString(), // Must be at least 14 years old
            ],
            'primary_caregiver' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$/', // First letter uppercase, allows hyphens and spaces
            ],
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/', // Must be 10 or 11 digits
            ],
            'landline_number' => [
                'nullable', // Optional field
                'string',
                'regex:/^[0-9]{7,10}$/', // Must be between 7 and 10 digits
            ],

            // Current Address
            'address_details' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s,.-]+$/', // Allows alphanumeric characters, spaces, commas, periods, and hyphens
            ],
            'municipality' => [
                'required',
                'exists:municipalities,municipality_id', // Must exist in the municipalities table
            ],
            'barangay' => [
                'required',
                'exists:barangays,barangay_id', // Must exist in the barangays table
            ],
            // Medical History  
            'medical_conditions' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/', // Allows letters, numbers, spaces, commas, periods, hyphens, and parentheses
                'max:500', // Optional: Limit the length to 500 characters
            ],
            'medications' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
                'max:500',
            ],
            'allergies' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
                'max:500',
            ],
            'immunizations' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
                'max:500',
            ],
            'category' => 'required|exists:beneficiary_categories,category_id', 

            // Care Needs: Mobility
            'frequency.mobility' => [
                'nullable', // Optional field
                'string',
                'max:255', // Maximum length of 255 characters
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/', // Allows letters, numbers, spaces, commas, periods, hyphens, and parentheses
            ],
            'assistance.mobility' => [
                'nullable', // Optional field
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],

            // Care Needs: Cognitive / Communication
            'frequency.cognitive' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'assistance.cognitive' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],

            // Care Needs: Self-sustainability
            'frequency.self_sustainability' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'assistance.self_sustainability' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],

            // Care Needs: Disease / Therapy Handling
            'frequency.disease' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'assistance.disease' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],

            // Care Needs: Daily Life / Social Contact
            'frequency.daily_life' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'assistance.daily_life' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],

            // Care Needs: Outdoor Activities
            'frequency.outdoor' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'assistance.outdoor' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],

            // Care Needs: Household Keeping
            'frequency.household' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'assistance.household' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],

            // Medications Management
            'medication_name' => 'nullable|array',
            'medication_name.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'dosage' => 'nullable|array',
            'dosage.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'frequency' => 'nullable|array',
            'frequency.*' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],
            'administration_instructions' => 'nullable|array',
            'administration_instructions.*' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            ],

            // Mobility
            'mobility.walking_ability' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            'mobility.assistive_devices' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            'mobility.transportation_needs' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',

            // Cognitive Function
            'cognitive.memory' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            'cognitive.thinking_skills' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            'cognitive.orientation' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            'cognitive.behavior' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',

            // Emotional Well-being
            'emotional.mood' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            'emotional.social_interactions' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
            'emotional.emotional_support' => 'nullable|string|max:500|regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/',
        
            // Address
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/', // Allows alphanumeric characters, spaces, commas, periods, and hyphens
            ],

            // Emergency Contact
            'emergency_contact.name' => [
                'required',
                'string',
                'regex:/^[A-Z][a-zA-Z]*(?: [A-Z][a-zA-Z]*)+$/', // Valid full name
                'max:100',
            ],
            'emergency_contact.relation' => 'required|string|in:Parent,Sibling,Spouse,Child,Relative,Friend',
            'emergency_contact.mobile' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/', // 10 or 11 digits
            ],
            'emergency_contact.email' => 'required|email|max:100',

            // Emergency Plan
            'emergency_plan.procedures' => [
                'required',
                'string',
                'max:1000', // Optional: Limit the length to 1000 characters
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/', // Allows letters, numbers, spaces, and specific special characters
            ],

            // Care Worker
            'care_worker.careworker_id' => 'required|exists:cose_users,id', // Ensure the selected care worker exists
            'care_worker.tasks' => 'required|array|min:1', // Ensure at least one task is provided
            'care_worker.tasks.*' => [
                'required',
                'string',
                'max:255', // Limit to 255 characters
                'regex:/^[A-Za-z0-9\s.,\-()\'\"!?]+$/', // Allow letters, numbers, spaces, and specific special characters
            ],

            // Beneficiary Picture
            'beneficiaryProfilePic' => 'required|file|mimes:jpeg,png|max:2048', // Max size: 2MB

            // Review Date
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addYear()->format('Y-m-d'),

            // Care Service Agreement
            'care_service_agreement' => 'required|file|mimes:pdf,doc,docx|max:5120', // Max size: 5MB

            // General Careplan
            'general_careplan' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // Max size: 5MB
        
            // Beneficiary Signature
                'beneficiary_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048', // Max size: 2MB
                'beneficiary_signature_canvas' => 'nullable|string', // Base64 string for the canvas signature

                // Care Worker Signature
                'care_worker_signature_upload' => 'nullable|file|mimes:jpeg,png|max:2048', // Max size: 2MB
                'care_worker_signature_canvas' => 'nullable|string', // Base64 string for the canvas signature
            
            // Email
            'account.email' => 'required|email|unique:portal_accounts,portal_email|max:255', // Unique in the portal_accounts table

            // Password
            'account.password' => 'required|string|min:8|confirmed', // 'confirmed' ensures it matches the confirmation field


            
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (
            !$request->hasFile('beneficiary_signature_upload') &&
            empty($request->input('beneficiary_signature_canvas'))
        ) {
            return redirect()->back()->withErrors(['beneficiary_signature' => 'Please provide a beneficiary signature either by drawing on the canvas or uploading a file.'])->withInput();
        }

        if (
            !$request->hasFile('care_worker_signature_upload') &&
            empty($request->input('care_worker_signature_canvas'))
        ) {
            return redirect()->back()->withErrors(['care_worker_signature' => 'Please provide a care worker signature either by drawing on the canvas or uploading a file.'])->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        

        // DB::beginTransaction();
        // try
        try {
            DB::beginTransaction();

            // Generate unique identifier for file naming
            $uniqueIdentifier = Str::random(10);

            // Store beneficiary profile picture
            if ($request->hasFile('beneficiaryProfilePic')) {
                $beneficiaryPhotoPath = $request->file('beneficiaryProfilePic')->storeAs(
                    'uploads/beneficiary_photos',
                    $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . $request->file('beneficiaryProfilePic')->getClientOriginalExtension(),
                    'public'
                );
                
                // Add debug logging
                \Log::info('Storing beneficiary photo with path: ' . $beneficiaryPhotoPath);
            } else {
                throw new \Exception('Beneficiary profile picture is required.');
            }

            // Handle Care Service Agreement
            if ($request->hasFile('care_service_agreement')) {
                $careServiceAgreementPath = $request->file('care_service_agreement')->storeAs(
                    'uploads/care_service_agreements',
                    $request->input('first_name') . '_' . $request->input('last_name') . '_care_service_agreement_' . $uniqueIdentifier . '.' . $request->file('care_service_agreement')->getClientOriginalExtension(),
                    'public'
                );
            } else {
                $careServiceAgreementPath = null; // Set to null if no file is uploaded
            }

            // Handle Beneficiary Signature
            if ($request->hasFile('beneficiary_signature_upload')) {
                $directory = public_path('storage/uploads/beneficiary_signatures');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
            
                $beneficiarySignaturePath = 'uploads/beneficiary_signatures/' . 
                    $request->input('first_name') . '_' . 
                    $request->input('last_name') . '_signature_' . 
                    $uniqueIdentifier . '.' . 
                    $request->file('beneficiary_signature_upload')->getClientOriginalExtension();
            
                // Store the file
                $request->file('beneficiary_signature_upload')->storeAs(
                    'public/' . dirname($beneficiarySignaturePath),
                    basename($beneficiarySignaturePath)
                );
            } elseif ($request->input('beneficiary_signature_canvas')) {
                $beneficiarySignaturePath = 'uploads/beneficiary_signatures/' . 
                    $request->input('first_name') . '_' . 
                    $request->input('last_name') . '_signature_' . 
                    $uniqueIdentifier . '.png';
            
                // Ensure the directory exists
                $directory = public_path('storage/uploads/beneficiary_signatures');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
            
                // Decode and save the canvas signature
                $beneficiarySignatureData = $request->input('beneficiary_signature_canvas');
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $beneficiarySignatureData));
                
                // Save the file using proper path
                $absolutePath = storage_path('app/public/' . $beneficiarySignaturePath);
                $directory = dirname($absolutePath);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                file_put_contents($absolutePath, $decodedImage);
            }
            
            // Handle Care Worker Signature - similar fix as beneficiary signature
            if ($request->hasFile('care_worker_signature_upload')) {
                $directory = public_path('storage/uploads/care_worker_signatures');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
            
                $careWorkerSignaturePath = 'uploads/care_worker_signatures/' . 
                    $request->input('first_name') . '_' . 
                    $request->input('last_name') . '_care_worker_signature_' . 
                    $uniqueIdentifier . '.' . 
                    $request->file('care_worker_signature_upload')->getClientOriginalExtension();
            
                // Store the file
                $request->file('care_worker_signature_upload')->storeAs(
                    'public/' . dirname($careWorkerSignaturePath),
                    basename($careWorkerSignaturePath)
                );
            } elseif ($request->input('care_worker_signature_canvas')) {
                $careWorkerSignaturePath = 'uploads/care_worker_signatures/' . 
                    $request->input('first_name') . '_' . 
                    $request->input('last_name') . '_care_worker_signature_' . 
                    $uniqueIdentifier . '.png';
            
                // Ensure the directory exists
                $directory = storage_path('app/public/uploads/care_worker_signatures');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
            
                // Decode and save the canvas signature
                $careWorkerSignatureData = $request->input('care_worker_signature_canvas');
                $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $careWorkerSignatureData));
                file_put_contents($directory . '/' . basename($careWorkerSignaturePath), $decodedImage);
            }   

            // Insert into portal_accounts table
            $portalAccount = DB::table('portal_accounts')->insertGetId([
                'portal_email' => $request->input('account.email'),
                'portal_password' => Hash::make($request->input('account.password')),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
            \Log::info('Portal Account ID: ' . $portalAccount);
        
            // Insert into gcp (General Care Plan) table
            $generalCarePlan = GeneralCarePlan::create([
                'care_worker_id' => $request->input('care_worker.careworker_id'),
                'emergency_plan' => $request->input('emergency_plan.procedures'),
                'review_date' => $request->input('date'),
                'created_at' => now(),
            ]);
        
            $generalCarePlanId = $generalCarePlan->general_care_plan_id ?? null;
        
            \Log::info('General Care Plan Created: ' . json_encode($generalCarePlan));
        
            if (!$generalCarePlanId) {
                throw new \Exception('General Care Plan ID is null.');
            }

            $remember_token = Str::random(60);

            $mobileNumber = $request->input('mobile_number');
            if (!str_starts_with($mobileNumber, '+63')) {
                $mobileNumber = '+63' . $mobileNumber;
            }

            $emergencyContactMobile = $request->input('emergency_contact.mobile');
            if (!str_starts_with($emergencyContactMobile, '+63')) {
                $emergencyContactMobile = '+63' . $emergencyContactMobile;
            }

            // Insert into beneficiaries table
            $beneficiary = Beneficiary::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'birthday' => $request->input('birth_date'),
                'gender' => $request->input('gender'),
                'civil_status' => $request->input('civil_status'),
                'religion' => $request->input('religion'),
                'nationality' => $request->input('nationality'),
                'educational_background' => $request->input('educational_background'),
                'street_address' => $request->input('address_details'),
                'barangay_id' => $request->input('barangay'),
                'municipality_id' => $request->input('municipality'),
                'category_id' => $request->input('category'),
                'mobile' => $mobileNumber,
                'landline' => $request->input('landline_number'),
                'emergency_contact_name' => $request->input('emergency_contact.name'),
                'emergency_contact_relation' => $request->input('emergency_contact.relation'),
                'emergency_contact_mobile' => $emergencyContactMobile,
                'emergency_contact_email' => $request->input('emergency_contact.email'),
                'emergency_procedure' => $request->input('emergency_plan.procedures'),
                'primary_caregiver' => $request->input('primary_caregiver'),
                'care_service_agreement' => $careServiceAgreementPath,
                'photo' => $beneficiaryPhotoPath,
                'beneficiary_signature' => $beneficiarySignaturePath,
                'care_worker_signature' => $careWorkerSignaturePath,
                'general_care_plan_id' => $generalCarePlanId,
                'portal_account_id' => $portalAccount,
                'beneficiary_status_id' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
                'remember_token' => $remember_token,
            ]);
        
            // Insert into emotional_wellbeing table
            EmotionalWellbeing::create([
                'general_care_plan_id' => $generalCarePlanId,
                'mood' => $request->input('emotional.mood'),
                'social_interactions' => $request->input('emotional.social_interactions'),
                'emotional_support_needs' => $request->input('emotional.emotional_support'),
            ]);
        
            // Insert into cognitive_function table
            CognitiveFunction::create([
                'general_care_plan_id' => $generalCarePlanId,
                'memory' => $request->input('cognitive.memory'),
                'thinking_skills' => $request->input('cognitive.thinking_skills'),
                'orientation' => $request->input('cognitive.orientation'),
                'behavior' => $request->input('cognitive.behavior'),
            ]);
        
            // Insert into mobility table
            Mobility::create([
                'general_care_plan_id' => $generalCarePlanId,
                'walking_ability' => $request->input('mobility.walking_ability'),
                'assistive_devices' => $request->input('mobility.assistive_devices'),
                'transportation_needs' => $request->input('mobility.transportation_needs'),
            ]);
        
            // Insert into health_histories table
            HealthHistory::create([
                'general_care_plan_id' => $generalCarePlanId,
                'medical_conditions' => $request->input('medical_conditions'),
                'medications' => $request->input('medications'),
                'allergies' => $request->input('allergies'),
                'immunizations' => $request->input('immunizations'),
            ]);

            // Categories 1 to 7 represent different care categories
            $careCategories = [
                1 => ['frequency' => $request->input('frequency.mobility'), 'assistance' => $request->input('assistance.mobility')],
                2 => ['frequency' => $request->input('frequency.cognitive'), 'assistance' => $request->input('assistance.cognitive')],
                3 => ['frequency' => $request->input('frequency.self_sustainability'), 'assistance' => $request->input('assistance.self_sustainability')],
                4 => ['frequency' => $request->input('frequency.disease'), 'assistance' => $request->input('assistance.disease')],
                5 => ['frequency' => $request->input('frequency.daily_life'), 'assistance' => $request->input('assistance.daily_life')],
                6 => ['frequency' => $request->input('frequency.outdoor'), 'assistance' => $request->input('assistance.outdoor')],
                7 => ['frequency' => $request->input('frequency.household'), 'assistance' => $request->input('assistance.household')]
            ];

            foreach ($careCategories as $categoryId => $data) {
                if (!empty($data['frequency']) || !empty($data['assistance'])) {
                    CareNeed::create([
                        'general_care_plan_id' => $generalCarePlanId,
                        'care_category_id' => $categoryId,
                        'frequency' => $data['frequency'],
                        'assistance_required' => $data['assistance']
                    ]);
                }
            }
        
            DB::commit();
        
            // Redirect with success message
            return redirect()->route('admin.addBeneficiary')->with('success', 'Beneficiary has been successfully added!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving beneficiary: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while saving the beneficiary.'])->withInput();
        }
    }
}