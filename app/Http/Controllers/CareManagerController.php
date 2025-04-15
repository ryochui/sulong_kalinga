<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Municipality;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

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
            return redirect()->route('admin.caremanagers.index')->with('error', 'Care manager not found.');
        }

        return view('admin.viewCareManagerDetails', compact('caremanager'));
    }


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
            'gender' => 'nullable|string|in:Male,Female,Other', // Must match dropdown options
            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced', // Must match dropdown options
            'religion' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'nationality' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'educational_background' => 'nullable|string|in:College,Highschool,Doctorate', // Must match dropdown options
        
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
            'caremanager_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'government_ID' => 'nullable|image|mimes:jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
        
            // IDs
            'sss_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/', // 10 digits
            ],
            'philhealth_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{12}$/', // 12 digits
            ],
            'pagibig_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{12}$/', // 12 digits
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Handle file uploads and rename files
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $uniqueIdentifier = time() . '_' . Str::random(5);

            if ($request->hasFile('caremanager_photo')) {
                $administratorPhotoPath = $request->file('caremanager_photo')->storeAs(
                    'uploads/caremanager_photos', 
                    $firstName . '' . $lastName . '_photo' . $uniqueIdentifier . '.' . $request->file('caremanager_photo')->getClientOriginalExtension(),
                    'public'
                );
            }
    
            if ($request->hasFile('government_ID')) {
                $governmentIDPath = $request->file('government_ID')->storeAs(
                    'uploads/caremanager_government_ids', 
                    $firstName . '' . $lastName . '_government_id' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
                    'public'
                );
            }
    
            if ($request->hasFile('resume')) {
                $resumePath = $request->file('resume')->storeAs(
                    'uploads/caremanager_resumes', 
                    $firstName . '' . $lastName . '_resume' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
                    'public'
                );
            }

        // Save the care manager to the database
        $caremanager = new User();

        // All other existing fields
        $caremanager->first_name = $request->input('first_name');
        $caremanager->last_name = $request->input('last_name');
        // $caremanager->name = $request->input('name') . ' ' . $request->input('last_name'); // Combine first and last name
        $caremanager->birthday = $request->input('birth_date');
        $caremanager->gender = $request->input('gender') ?? null;
        $caremanager->civil_status = $request->input('civil_status') ?? null;
        $caremanager->religion = $request->input('religion') ?? null;
        $caremanager->nationality = $request->input('nationality') ?? null;
        $caremanager->educational_background = $request->input('educational_background') ?? null;
        $caremanager->address = $request->input('address_details');
        $caremanager->email = $request->input('account.email'); // Work email
        $caremanager->personal_email = $request->input('personal_email'); // Personal email
        $caremanager->mobile = '+63' . $request->input('mobile_number');
        $caremanager->landline = $request->input('landline_number') ?? null;
        $caremanager->password = bcrypt($request->input('account.password'));
        // $caremanager->organization_role_id = $request->input('Organization_Roles');
        $caremanager->role_id = 2; // 2 is the role ID for care managers
        $caremanager->volunteer_status = 'Active'; // Status in COSE
        $caremanager->status = 'Active'; // Status for access to the system
        $caremanager->status_start_date = now();
        $caremanager->assigned_municipality_id = $request->input('municipality');

        // Save file paths and IDs
        $caremanager->photo = $administratorPhotoPath ?? null;
        $caremanager->government_issued_id = $request->hasFile('government_ID') ? $governmentIDPath : null;
        $caremanager->cv_resume = $request->hasFile('resume') ? $resumePath : null;
        $caremanager->sss_id_number = $request->input('sss_ID') ?? null;
        $caremanager->philhealth_id_number = $request->input('philhealth_ID') ?? null;
        $caremanager->pagibig_id_number = $request->input('pagibig_ID') ?? null;

        // Generate and save the remember_token
        $caremanager->remember_token = Str::random(60);


        $caremanager->save();

        // Redirect with success message
        return redirect()->route('admin.caremanagers.create')
            ->with('success', 'Care Manager has been successfully added!');

        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a unique constraint violation
            if ($e->getCode() == 23505) { // PostgreSQL unique violation error code
                // Check which field caused the violation
                if (strpos($e->getMessage(), 'cose_users_mobile_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['mobile_number' => 'This mobile number is already registered in the system.']);
                } elseif (strpos($e->getMessage(), 'cose_users_email_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['account.email' => 'This email address is already registered in the system.']);
                } elseif (strpos($e->getMessage(), 'cose_users_personal_email_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['personal_email' => 'This personal email address is already registered in the system.']);
                } else {
                    // Generic unique constraint error
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['error' => 'A record with some of this information already exists.']);
                }
            }

            // For other database errors
            \Log::error('Database error when creating care manager: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while saving the care manager. Please try again.']);
        } catch (\Exception $e) {
            // For any other unexpected errors
            \Log::error('Unexpected error when creating care manager: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }


    public function updateCaremanager(Request $request, $id)
    {
        // Find the care manager
        $caremanager = User::findOrFail($id);
        
        // Validate input
        $validator = Validator::make($request->all(), [
            'first_name' => [
                'required',
                'string',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/',
                'max:100'
            ],
            'last_name' => [
                'required',
                'string',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/',
                'max:100'
            ],
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(),
            'gender' => 'nullable|string|in:Male,Female,Other',
            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced',
            'religion' => 'nullable|string|regex:/^[a-zA-Z\s]*$/',
            'nationality' => 'nullable|string|regex:/^[a-zA-Z\s]*$/',
            'educational_background' => 'nullable|string|in:College,Highschool,Doctorate',
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/',
            ],
            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('cose_users', 'personal_email')->ignore($id),
            ],
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/',
                Rule::unique('cose_users', 'mobile')->ignore($id),
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/',
            ],
            'caremanager_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'government_ID' => 'nullable|image|mimes:jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'sss_ID' => 'nullable|string|max:10',
            'philhealth_ID' => 'nullable|string|max:12',
            'pagibig_ID' => 'nullable|string|max:12',
            'account.email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('cose_users', 'email')->ignore($id),
            ],
            'municipality' => 'required|exists:municipalities,municipality_id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle file uploads if new files are provided
        $uniqueIdentifier = time() . '_' . Str::random(5);

        if ($request->hasFile('caremanager_photo')) {
            $caremanagerPhotoPath = $request->file('caremanager_photo')->storeAs(
                'uploads/caremanager_photos',
                $caremanager->first_name . '_' . $caremanager->last_name . '_photo_' . $uniqueIdentifier . '.' . $request->file('caremanager_photo')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->photo = $caremanagerPhotoPath;
        }

        if ($request->hasFile('government_ID')) {
            $governmentIDPath = $request->file('government_ID')->storeAs(
                'uploads/caremanager_government_ids',
                $caremanager->first_name . '_' . $caremanager->last_name . '_government_id_' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->government_issued_id = $governmentIDPath;
        }

        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->storeAs(
                'uploads/caremanager_resumes',
                $caremanager->first_name . '_' . $caremanager->last_name . '_resume_' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
                'public'
            );
            $caremanager->cv_resume = $resumePath;
        }

        $mobile = $request->input('mobile_number');
        if (substr($mobile, 0, 3) !== '+63') {
            $mobile = '+63' . $mobile;
        }

        // Update care manager details
        $caremanager->first_name = $request->input('first_name');
        $caremanager->last_name = $request->input('last_name');
        $caremanager->birthday = $request->input('birth_date');
        $caremanager->gender = $request->input('gender');
        $caremanager->civil_status = $request->input('civil_status');
        $caremanager->religion = $request->input('religion');
        $caremanager->nationality = $request->input('nationality');
        $caremanager->educational_background = $request->input('educational_background');
        $caremanager->address = $request->input('address_details');
        $caremanager->personal_email = $request->input('personal_email');
        $caremanager->mobile = $mobile;
        $caremanager->landline = $request->input('landline_number');
        $caremanager->sss_id_number = $request->input('sss_ID');
        $caremanager->philhealth_id_number = $request->input('philhealth_ID');
        $caremanager->pagibig_id_number = $request->input('pagibig_ID');
        $caremanager->assigned_municipality_id = $request->input('municipality');
        
        // Update the email address if changed
        if ($request->filled('account.email')) {
            $caremanager->email = $request->input('account.email');
        }
        
        // Save the changes
        $caremanager->save();
        
        return redirect()->route('admin.caremanagers.index')->with('success', 
        'Care Manager ' . $caremanager->first_name . ' ' . $caremanager->last_name . 
        ' has been successfully updated!'
        );
    }


    public function editCaremanagerProfile($id)
    {
        // Find the care manager by ID
        $caremanager = User::where('role_id', 2)->where('id', $id)->first();
        
        if (!$caremanager) {
            return redirect()->route('admin.caremanagers.index')->with('error', 'Care Manager not found.');
        }

        // Format date for the form
        $birth_date = null;
        if ($caremanager->birthday) {
            $birth_date = Carbon::parse($caremanager->birthday)->format('Y-m-d');
        }

        // Get municipalities for the dropdown
        $municipalities = Municipality::all();

        return view('admin.editCaremanagerProfile', compact('caremanager', 'birth_date', 'municipalities'));
    }

    public function updateStatusAjax($id, Request $request)
    {
        try {
            \Log::info('Update caremanager status AJAX request', [
                'caremanager_id' => $id,
                'status' => $request->input('status')
            ]);
            
            // Find care manager (role_id = 2)
            $caremanager = User::where('role_id', 2)->find($id);
            
            if (!$caremanager) {
                return response()->json(['success' => false, 'message' => 'Care manager not found.'], 404);
            }
            
            // Get the status directly
            $status = $request->input('status');
            
            // Update ONLY the status column
            $caremanager->status = $status;
            $caremanager->updated_at = now();
            $caremanager->save();
            
            \Log::info('Caremanager status updated successfully', [
                'caremanager_id' => $id,
                'new_status' => $status
            ]);
            
            return response()->json(['success' => true, 'message' => 'Care manager status updated successfully.']);
        } catch (\Exception $e) {
            \Log::error('Caremanager status update failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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

    public function deleteBeneficiary(Request $request)
    {
        $result = $this->userManagementService->deleteBeneficiary(
            $request->input('beneficiary_id'),
            Auth::user()
        );
        
        return response()->json($result);
    }
}