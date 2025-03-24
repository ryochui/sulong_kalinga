<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Municipality;

use App\Services\UserManagementService;

class CareManagerController extends Controller
{
    protected $userManagementService;
    
    public function __construct(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    public function index(Request $request)
    {

        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch careworkers based on the search query and filters
        $caremanagers = User::where('role_id', 2)
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

        // Pass the care managers to the view
        return view('admin.careManagerProfile', compact('caremanagers'));
    }

    public function viewCaremanagerDetails(Request $request)
    {
        $caremanager_id = $request->input('caremanager_id');
        $caremanager = User::where('role_id', 2)
        ->with('municipality')->find($caremanager_id);

        if (!$caremanager) {
            return redirect()->route('careManagerProfile')->with('error', 'Care manager not found.');
        }

        return view('admin.viewCaremanagerDetails', compact('caremanager'));
    }

    public function editCaremanagerProfile(Request $request)
    {
        $caremanager_id = $request->input('caremanager_id');
        $caremanager = User::where('role_id', 2)->where('id', $caremanager_id)->first();

        if (!$caremanager) {
            return redirect()->route('careManagerProfile')->with('error', 'Care manager not found.');
        }

        // Update care worker details here
        // ...

        return view('admin.editCaremanagerProfile', compact('caremanager'));    
    }

    // To revise so that dropdown will be dynamic
    public function create()
    {
        // Fetch all municipalities from the database
        $municipalities = Municipality::all();

        // Pass the municipalities to the view
        return view('admin.addCareManager', compact('municipalities'));
    }

    public function storeCareManager(Request $request)
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
            'caremanager_photo' => 'required|image|mimes:jpeg,png|max:2048',
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

       $caremanagerPhotoPath = $request->file('caremanager_photo')->storeAs(
           'uploads/caremanager_photos', 
           $firstName . '' . $lastName . '_photo' . $uniqueIdentifier . '.' . $request->file('caremanager_photo')->getClientOriginalExtension(),
           'public'
       );

       $governmentIDPath = $request->file('government_ID')->storeAs(
           'uploads/caremanager_government_ids', 
           $firstName . '' . $lastName . '_government_id' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
           'public'
       );

       $resumePath = $request->file('resume')->storeAs(
        'uploads/caremanager_resumes', 
        $firstName . '' . $lastName . '_resume' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
        'public'
    );

        // Save the administrator to the database
        $caremanager = new User();
        $caremanager->first_name = $request->input('first_name');
        $caremanager->last_name = $request->input('last_name');
        // $caremanager->name = $request->input('name') . ' ' . $request->input('last_name'); // Combine first and last name
        $caremanager->birthday = $request->input('birth_date');
        $caremanager->gender = $request->input('gender');
        $caremanager->civil_status = $request->input('civil_status');
        $caremanager->religion = $request->input('religion');
        $caremanager->nationality = $request->input('nationality');
        $caremanager->educational_background = $request->input('educational_background');
        $caremanager->address = $request->input('address_details');
        $caremanager->email = $request->input('account.email'); // Work email
        $caremanager->personal_email = $request->input('personal_email'); // Personal email
        $caremanager->mobile = '+63' . $request->input('mobile_number');
        $caremanager->landline = $request->input('landline_number');
        $caremanager->password = bcrypt($request->input('account.password'));
        // $caremanager->organization_role_id = $request->input('Organization_Roles');
        $caremanager->role_id = 2; // 2 is the role ID for care managers
        $caremanager->volunteer_status = 'Active'; // Status in COSE
        $caremanager->status = 'Active'; // Status for access to the system
        $caremanager->status_start_date = now();
        $caremanager->assigned_municipality_id = $request->input('municipality');

        // Save file paths and IDs
        $caremanager->photo = $caremanagerPhotoPath;
        $caremanager->government_issued_id = $governmentIDPath;
        $caremanager->cv_resume = $resumePath;
        $caremanager->sss_id_number = $request->input('sss_ID');
        $caremanager->philhealth_id_number = $request->input('philhealth_ID');
        $caremanager->pagibig_id_number = $request->input('pagibig_ID');

        // Generate and save the remember_token
        $caremanager->remember_token = Str::random(60);


        $caremanager->save();

        // Redirect with success message
        return redirect()->route('admin.addCareManager')->with('success', 'Care Manager has been successfully added!');
    }

    public function updateStatus(Request $request, $id)
    {
        $caremanager = User::where('role_id', 2)->find($id);

        if (!$caremanager) {
            return redirect()->route('admin.careManagerProfile')->with('error', 'Care manager not found.');
        }

        $status = $request->input('status');
        $caremanager->volunteer_status = $status;
        $caremanager->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
        $caremanager->updated_at = now(); // Set the updated_at column to the current timestamp

        if ($status == 'Inactive') {
            $caremanager->status_end_date = now();
        } else {
            $caremanager->status_end_date = null;
        }

        $caremanager->save();

        return response()->json(['success' => true, 'message' => 'Care manager status updated successfully.']);
    }

    public function deleteCareworker(Request $request)
    {
        $result = $this->userManagementService->deleteCareworker(
            $request->input('careworker_id'),
            Auth::user()
        );
        
        return response()->json($result);
    }

    public function deleteFamilyMember(Request $request)
    {
        $result = $this->userManagementService->deleteFamilyMember(
            $request->input('family_member_id'),
            Auth::user()
        );
        
        return response()->json($result);
    }
}