<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Municipality;
use App\Models\GeneralCarePlan;
use App\Models\Beneficiary;
 

class CareWorkerController extends Controller
{

    public function index(Request $request)
    {

        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch careworkers based on the search query and filters
        $careworkers = User::where('role_id', 3)
        ->with('municipality')
        ->when($search, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        })
        ->when($filter, function ($query, $filter) {
            if ($filter == 'status') {
                return $query->orderBy('volunteer_status');
            } elseif ($filter == 'municipality') {
                return $query->orderBy('assigned_municipality_id');
            }
        })
        ->orderBy('first_name') // Order by first name alphabetically by default
        ->get();

        // Debugging: Check the data
        //dd($careworkers);

        // Pass the data to the Blade template
        return view('admin.careWorkerProfile', compact('careworkers'));
    }

    public function viewCareworkerDetails(Request $request)
    {
        $careworker_id = $request->input('careworker_id');
        $careworker = User::where('role_id', 3)
        ->with('municipality')->find($careworker_id);

        if (!$careworker) {
            return redirect()->route('careWorkerProfile')->with('error', 'Care worker not found.');
        }

        // Fetch all general care plans associated with this care worker
        $generalCarePlans = GeneralCarePlan::where('care_worker_id', $careworker_id)->get();

        // Fetch all beneficiaries associated with these general care plans
        $beneficiaries = Beneficiary::whereIn('general_care_plan_id', $generalCarePlans->pluck('general_care_plan_id'))->get();

        return view('admin.viewCareworkerDetails', compact('careworker', 'beneficiaries'));
    }

    public function editCareworkerProfile(Request $request)
    {
        $careworker_id = $request->input('careworker_id');
        $careworker = User::where('role_id', 3)->where('id', $careworker_id)->first();

        if (!$careworker) {
            return redirect()->route('careWorkerProfile')->with('error', 'Care worker not found.');
        }

        // Update care worker details here
        // ...

        return view('admin.editCareworkerProfile', compact('careworker'));    
    }

    // To revise so that dropdown will be dynamic
    public function create()
    {
        // Fetch all municipalities from the database
        $municipalities = Municipality::all();

        // Pass the municipalities to the view
        return view('admin.addCareWorker', compact('municipalities'));
    }

    public function storeCareWorker(Request $request)
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
            'nationality' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'educational_background' => 'required|string|in:College,Highschool,Doctorate', // Must match dropdown options
        
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

         // Handle file uploads and rename files
       $firstName = $request->input('first_name');
       $lastName = $request->input('last_name');
       $uniqueIdentifier = time() . '_' . Str::random(5);

       $careworkerPhotoPath = $request->file('careworker_photo')->storeAs(
           'uploads/careworker_photos', 
           $firstName . '' . $lastName . '_photo' . $uniqueIdentifier . '.' . $request->file('careworker_photo')->getClientOriginalExtension(),
           'public'
       );

       $governmentIDPath = $request->file('government_ID')->storeAs(
           'uploads/careworker_government_ids', 
           $firstName . '' . $lastName . '_government_id' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
           'public'
       );

       $resumePath = $request->file('resume')->storeAs(
        'uploads/careworker_resumes', 
        $firstName . '' . $lastName . '_resume' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
        'public'
    );

        // Save the administrator to the database
        $careworker = new User();
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
        return redirect()->route('admin.addCareWorker')->with('success', 'Care worker added successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $careworker = User::where('role_id', 3)->find($id);

        if (!$careworker) {
            return redirect()->route('admin.careWorkerProfile')->with('error', 'Care worker not found.');
        }

        $status = $request->input('status');
        $careworker->volunteer_status = $status;
        $careworker->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
        $careworker->updated_at = now(); // Set the updated_at column to the current timestamp

        if ($status == 'Inactive') {
            $careworker->status_end_date = now();
        } else {
            $careworker->status_end_date = null;
        }

        $careworker->save();

        return response()->json(['success' => true, 'message' => 'Care worker status updated successfully.']);
    }
}