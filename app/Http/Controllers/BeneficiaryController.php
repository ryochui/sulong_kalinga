<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\Medication;
use App\Models\GeneralCarePlan;
use App\Models\CareNeed;
use App\Models\BeneficiaryCategory;
use App\Models\BeneficiaryStatus;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\CareWorkerResponsibility;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

        // Pass the municipalities to the view
        return view('admin.addBeneficiary', compact('municipalities', 'barangays'));
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
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(), // Must be older than 14 years
            'gender' => 'required|string|in:Male,Female,Other', // Must match dropdown options
            'civil_status' => 'required|string|in:Single,Married,Widowed,Divorced', // Must match dropdown options
            'religion' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            // Medical History  
            'medical_conditions' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()]+$/', // Allows letters, numbers, spaces, commas, periods, hyphens, and parentheses
                'max:500', // Optional: Limit the length to 500 characters
            ],
            'medications' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()]+$/',
                'max:500',
            ],
            'allergies' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()]+$/',
                'max:500',
            ],
            'immunizations' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z0-9\s.,\-()]+$/',
                'max:500',
            ],
        
            // Address
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/', // Allows alphanumeric characters, spaces, commas, periods, and hyphens
            ],
        
            
            // Email fields
            'account.email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,email',
            ],
            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,personal_email',
            ],
            // Contact Information
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/', //  10 or 11 digits, +63 preceeding
                'unique:cose_users,mobile',
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/', // Between 7 and 10 digits
            ],
        
            // Account Registration
            'account.password' => 'required|string|min:8|confirmed',
        
            // // Organization Roles
            // 'Organization_Roles' => 'required|integer|exists:organization_roles,organization_role_id',
            
            // Municipality
            'municipality' => 'required|integer|exists:municipalities,municipality_id',

            // Barangay
            'barangay' => 'required|integer|exists:barangays,barangay_id',
        
            // Documents
            'careworker_photo' => 'required|image|mimes:jpeg,png|max:2048',
            'government_ID' => 'required|image|mimes:jpeg,png|max:2048',
            'resume' => 'required|mimes:pdf,doc,docx|max:2048',
        
            // IDs
            'sss_ID' => [
                'required',
                'string',
                'regex:/^[0-9]{10}$/', // 10 digits
            ],
            'philhealth_ID' => [
                'required',
                'string',
                'regex:/^[0-9]{12}$/', // 12 digits
            ],
            'pagibig_ID' => [
                'required',
                'string',
                'regex:/^[0-9]{12}$/', // 12 digits
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

         // Handle file uploads and rename files(removed the handling of file uploads for now)
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $uniqueIdentifier = time() . '_' . Str::random(5);

        // Save the administrator to the database
        $careworker = new Beneficiary();
        $careworker->first_name = $request->input('first_name');
        $careworker->last_name = $request->input('last_name');
        // $careworker->name = $request->input('name') . ' ' . $request->input('last_name'); // Combine first and last name
        $careworker->birthday = $request->input('birth_date');
        $careworker->gender = $request->input('gender');
        $careworker->civil_status = $request->input('civil_status');
        $careworker->religion = $request->input('religion');
        $careworker->nationality = $request->input('nationality');
        $careworker->educational_background = $request->input('educational_background');
        $careworker->address = $request->input('address_details');
        $careworker->email = $request->input('account.email'); // Work email
        $careworker->personal_email = $request->input('personal_email'); // Personal email
        $careworker->mobile = '+63' . $request->input('mobile_number');
        $careworker->landline = $request->input('landline_number');
        $careworker->password = bcrypt($request->input('account.password'));
        // $careworker->organization_role_id = $request->input('Organization_Roles');
        $careworker->role_id = 3; // 3 is the role ID for care workers
        $careworker->volunteer_status = 'Active'; // Status in COSE
        $careworker->status = 'Active'; // Status for access to the system
        $careworker->status_start_date = now();
        $careworker->assigned_municipality_id = $request->input('municipality');

        // Save file paths and IDs
        $careworker->photo = $careworkerPhotoPath;
        $careworker->government_issued_id = $governmentIDPath;
        $careworker->cv_resume = $resumePath;
        $careworker->sss_id_number = $request->input('sss_ID');
        $careworker->philhealth_id_number = $request->input('philhealth_ID');
        $careworker->pagibig_id_number = $request->input('pagibig_ID');

        // Generate and save the remember_token
        $careworker->remember_token = Str::random(60);


        $careworker->save();

        // Redirect with success message
        return redirect()->route('admin.addBeneficiary')->with('success', 'Beneficiary has been successfully added!');
    }
}