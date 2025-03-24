<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;

use App\Services\UserManagementService;

class AdminController extends Controller
{
    protected $userManagementService;
    
    public function __construct(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    public function storeAdministrator(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
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
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(), // Must be older than 14 years
            'gender' => 'required|string|in:Male,Female,Other', // Must match dropdown options
            'civil_status' => 'required|string|in:Single,Married,Widowed,Divorced', // Must match dropdown options
            'religion' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/', 
            ],
            'nationality' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/', 
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
        
            // Organization Roles
            'Organization_Roles' => 'required|integer|exists:organization_roles,organization_role_id',
        
            // Documents
            'administrator_photo' => 'required|image|mimes:jpeg,png|max:2048',
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

       $administratorPhotoPath = $request->file('administrator_photo')->storeAs(
           'uploads/administrator_photos', 
           $firstName . '' . $lastName . '_photo' . $uniqueIdentifier . '.' . $request->file('administrator_photo')->getClientOriginalExtension(),
           'public'
       );

       $governmentIDPath = $request->file('government_ID')->storeAs(
           'uploads/administrator_government_ids', 
           $firstName . '' . $lastName . '_government_id' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
           'public'
       );

       $resumePath = $request->file('resume')->storeAs(
           'uploads/administrator_resumes', 
           $firstName . '' . $lastName . '_resume' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
           'public'
       );

        // Save the administrator to the database
        $administrator = new User();
        $administrator->first_name = $request->input('first_name');
        $administrator->last_name = $request->input('last_name');
        // $administrator->name = $request->input('name') . ' ' . $request->input('last_name'); // Combine first and last name
        $administrator->birthday = $request->input('birth_date');
        $administrator->gender = $request->input('gender');
        $administrator->civil_status = $request->input('civil_status');
        $administrator->religion = $request->input('religion');
        $administrator->nationality = $request->input('nationality');
        $administrator->educational_background = $request->input('educational_background');
        $administrator->address = $request->input('address_details');
        $administrator->email = $request->input('account.email'); // Work email
        $administrator->personal_email = $request->input('personal_email'); // Personal email
        $administrator->mobile = '+63' . $request->input('mobile_number');
        $administrator->landline = $request->input('landline_number');
        $administrator->password = bcrypt($request->input('account.password'));
        $administrator->organization_role_id = $request->input('Organization_Roles');
        $administrator->role_id = 1; // 1 is the role ID for administrators
        $administrator->volunteer_status = 'Active'; // Status in COSE
        $administrator->status = 'Active'; // Status for access to the system
        $administrator->status_start_date = now();

        // Save file paths and IDs
        $administrator->photo = $administratorPhotoPath;
        $administrator->government_issued_id = $governmentIDPath;
        $administrator->cv_resume = $resumePath;
        $administrator->sss_id_number = $request->input('sss_ID');
        $administrator->philhealth_id_number = $request->input('philhealth_ID');
        $administrator->pagibig_id_number = $request->input('pagibig_ID');

        // Generate and save the remember_token
        $administrator->remember_token = Str::random(60);


        $administrator->save();

        // Redirect with success message
        return redirect()->route('admin.addAdministrator')->with('success', 'Administrator has been successfully added!');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch administrators based on the search query and filters
        $administrators = User::where('role_id', 1)
            ->with('organizationRole')
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                          ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->when($filter, function ($query, $filter) {
                if ($filter == 'status') {
                    return $query->orderBy('volunteer_status');
                } elseif ($filter == 'organizationrole') {
                    return $query->orderBy('organization_role_id');
                } elseif ($filter == 'area') {
                    return $query->join('organization_roles', 'users.organization_role_id', '=', 'organization_roles.organization_role_id')
                                 ->orderBy('organization_roles.area');
                }
            })
            ->orderBy('first_name') // Order by first name alphabetically by default
            ->get();

        // Debugging: Check the data
        // dd($administrators);

        // Pass the data to the Blade template
        return view('admin.administratorProfile', compact('administrators'));
    }

    public function viewAdminDetails(Request $request)
    {
        $administrator_id = $request->input('administrator_id');
        $administrator = User::where('role_id', 1)
        ->with('organizationRole')
        ->find($administrator_id);

        if (!$administrator) {
            return redirect()->route('administratorProfile')->with('error', 'Administrator not found.');
        }

        return view('admin.viewAdminDetails', compact('administrator'));
    }

    public function editAdminProfile(Request $request)
    {
        $administrator_id = $request->input('administrator_id');
        $administrator = User::where('role_id', 1)->where('id', $administrator_id)->first();

        if (!$administrator) {
            return redirect()->route('administratorProfile')->with('error', 'Administrator not found.');
        }

        return view('admin.editAdminProfile', compact('administrator'));    
    }

    public function updateStatus(Request $request, $id)
    {
        $administrator = User::where('role_id', 1)->find($id);

        if (!$administrator) {
            return redirect()->route('admin.careManagerProfile')->with('error', 'Administrator not found.');
        }

        $status = $request->input('status');
        $administrator->volunteer_status = $status;
        $administrator->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
        $administrator->updated_at = now(); // Set the updated_at column to the current timestamp

        if ($status == 'Inactive') {
            $administrator->status_end_date = now();
        } else {
            $administrator->status_end_date = null;
        }

        $administrator->save();

        return response()->json(['success' => true, 'message' => 'Administrator status updated successfully.']);
    }

    public function deleteAdministrator(Request $request)
    {
        try {
            // Get the ID from the request
            $id = $request->input('admin_id');
            
            // Find the administrator
            $administrator = User::where('id', $id)
                            ->where('role_id', 1)
                            ->first();
            
            if (!$administrator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Administrator not found.'
                ]);
            }
            
            // Don't allow deleting the Executive Director
            if ($administrator->organization_role_id == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Executive Director account cannot be deleted.',
                    'error_type' => 'executive_director'
                ]);
            }
            
            // Check if the current user is Executive Director
            if (Auth::user()->organization_role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Executive Director can delete administrator accounts.'
                ]);
            }
            
            // Check for various audit dependencies first before attempting to delete
            
            // 1. Check beneficiaries table for created_by/updated_by references
            $beneficiaryReferences = \DB::table('beneficiaries')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
                
            if ($beneficiaryReferences > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This administrator has created or updated beneficiary records which require audit history to be maintained.',
                    'error_type' => 'dependency_beneficiaries'
                ]);
            }
            
            // 2. Check users table for created_by/updated_by references
            $userReferences = \DB::table('cose_users')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
                
            if ($userReferences > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This administrator has created or updated user accounts which require audit history to be maintained.',
                    'error_type' => 'dependency_users'
                ]);
            }
            
            // 3. Check weekly care plans table for created_by/updated_by references
            $carePlanReferences = \DB::table('weekly_care_plans')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
                
            if ($carePlanReferences > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This administrator has created or updated care plans which require audit history to be maintained.',
                    'error_type' => 'dependency_care_plans'
                ]);
            }
            
            // 4. Check family members table for created_by/updated_by references
            $familyReferences = \DB::table('family_members')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
                
            if ($familyReferences > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This administrator has created or updated family member records which require audit history to be maintained.',
                    'error_type' => 'dependency_family'
                ]);
            }
            
            // Delete the administrator
            $administrator->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Administrator deleted successfully.'
            ]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error deleting administrator (QueryException): ' . $e->getMessage());
            
            // Check for foreign key constraint violation
            if (in_array($e->getCode(), ['23000', '23503']) || 
                (is_numeric($e->getCode()) && in_array((int)$e->getCode(), [23000, 23503]))) {
                
                // Try to determine which table has the dependency
                $errorMsg = $e->getMessage();
                
                if (stripos($errorMsg, 'beneficiaries') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This administrator has created or updated beneficiary records which require audit history to be maintained.',
                        'error_type' => 'dependency_beneficiaries'
                    ]);
                } else if (stripos($errorMsg, 'cose_users') !== false || stripos($errorMsg, 'users') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This administrator has created or updated user accounts which require audit history to be maintained.',
                        'error_type' => 'dependency_users'
                    ]);
                } else if (stripos($errorMsg, 'weekly_care_plans') !== false || stripos($errorMsg, 'care_plans') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This administrator has created or updated care plans which require audit history to be maintained.',
                        'error_type' => 'dependency_care_plans'
                    ]);
                } else if (stripos($errorMsg, 'family_members') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This administrator has created or updated family member records which require audit history to be maintained.',
                        'error_type' => 'dependency_family'
                    ]);
                }
                
                // Generic dependency message for other tables
                return response()->json([
                    'success' => false,
                    'message' => 'This administrator has created or updated records in the system that require audit history to be maintained.',
                    'error_type' => 'dependency_audit'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'A database error occurred. Please try again later or contact the system administrator.',
                'error_type' => 'database'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting administrator: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_type' => 'general'
            ]);
        }
    }

    public function deleteCaremanager(Request $request)
    {
        try {
            // Get the ID from the request
            $id = $request->input('caremanager_id');
            
            Log::info('Starting deletion process for care manager ID: ' . $id);
            
            // Find the care manager
            $caremanager = User::where('id', $id)
                            ->where('role_id', 2) // Care manager role
                            ->first();
            
            if (!$caremanager) {
                Log::warning('Care manager not found: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Care manager not found.'
                ]);
            }
            
            Log::info('Found care manager: ' . $caremanager->first_name . ' ' . $caremanager->last_name);
            
            // Check if the current user is an admin (role_id = 1)
            if (Auth::user()->role_id != 1) {
                Log::warning('Non-admin tried to delete care manager. User ID: ' . Auth::id());
                return response()->json([
                    'success' => false,
                    'message' => 'Only administrators can delete care manager accounts.'
                ]);
            }
            
            // Check for various audit dependencies first before attempting to delete
            Log::info('Checking audit dependencies for care manager ID: ' . $id);
            
            // 1. Check beneficiaries table for created_by/updated_by references
            $beneficiaryReferences = \DB::table('beneficiaries')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
            
            Log::info('Beneficiary references count: ' . $beneficiaryReferences);
                
            if ($beneficiaryReferences > 0) {
                Log::info('Care manager has beneficiary references, cannot delete');
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated beneficiary records which require audit history to be maintained.',
                    'error_type' => 'dependency_beneficiaries'
                ]);
            }
            
            // 2. Check users table for created_by/updated_by references
            $userReferences = \DB::table('cose_users')
                ->where('updated_by', $id)
                ->count();
            
            Log::info('User references count: ' . $userReferences);
                
            if ($userReferences > 0) {
                Log::info('Care manager has user references, cannot delete');
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated user accounts which require audit history to be maintained.',
                    'error_type' => 'dependency_users'
                ]);
            }
            
            // 3. Check weekly care plans table for created_by/updated_by references
            $carePlanReferences = \DB::table('weekly_care_plans')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
            
            Log::info('Care plan references count: ' . $carePlanReferences);
                
            if ($carePlanReferences > 0) {
                Log::info('Care manager has care plan references, cannot delete');
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated care plans which require audit history to be maintained.',
                    'error_type' => 'dependency_care_plans'
                ]);
            }
            
            // 4. Check family members table for created_by/updated_by references
            $familyReferences = \DB::table('family_members')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
            
            Log::info('Family references count: ' . $familyReferences);
                
            if ($familyReferences > 0) {
                Log::info('Care manager has family references, cannot delete');
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated family member records which require audit history to be maintained.',
                    'error_type' => 'dependency_family'
                ]);
            }
            
            // If we got here, we can proceed with deletion attempt
            Log::info('No audit dependencies found, proceeding with deletion');
            
            // Begin database transaction
            \DB::beginTransaction();
            
            try {
                
                Log::info('Attempting to delete care manager with ID: ' . $id);
                
                // Try with regular delete first
                try {
                    $result = $caremanager->delete();
                    Log::info('Regular delete result: ' . ($result ? 'success' : 'failed'));
                    
                    if (!$result) {
                        // If regular delete fails, try with force delete
                        Log::info('Regular delete failed, trying force delete');
                        $result = $caremanager->forceDelete();
                        Log::info('Force delete result: ' . ($result ? 'success' : 'failed'));
                        
                        if (!$result) {
                            throw new \Exception('Both regular and force delete failed');
                        }
                    }
                    
                    // If we got here, everything succeeded
                    \DB::commit();
                    Log::info('Care manager deleted successfully: ' . $id);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Care manager deleted successfully.'
                    ]);
                } catch (\Exception $deleteException) {
                    Log::error('Error during delete operation: ' . $deleteException->getMessage());
                    
                    // Try using DB::delete as a last resort
                    Log::info('Trying direct DB::delete');
                    $directDeleteResult = \DB::delete('DELETE FROM cose_users WHERE id = ?', [$id]);
                    Log::info('Direct DB::delete result: ' . $directDeleteResult);
                    
                    if ($directDeleteResult) {
                        \DB::commit();
                        Log::info('Care manager deleted successfully using direct DB::delete: ' . $id);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Care manager deleted successfully.'
                        ]);
                    } else {
                        throw $deleteException; // Re-throw to be caught by outer catch
                    }
                }
            } catch (\Exception $innerException) {
                // If anything fails, roll back the transaction
                \DB::rollBack();
                Log::error('Inner exception while deleting care manager: ' . $innerException->getMessage());
                Log::error('Stack trace: ' . $innerException->getTraceAsString());
                
                // Check if this is a PDO exception with more details
                if ($innerException instanceof \PDOException) {
                    Log::error('PDO error code: ' . $innerException->getCode());
                    Log::error('SQL state: ' . $innerException->errorInfo[0] ?? 'unknown');
                }
                
                throw $innerException; // Re-throw to be caught by outer catch
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error deleting care manager: ' . $e->getMessage());
            Log::error('Error code: ' . $e->getCode());
            
            // If we can get the SQL that failed, log it
            if (method_exists($e, 'getSql')) {
                Log::error('Failed SQL: ' . $e->getSql());
            }
            
            // Check for foreign key constraint violation
            if (in_array($e->getCode(), ['23000', '23503']) || 
                (is_numeric($e->getCode()) && in_array((int)$e->getCode(), [23000, 23503]))) {
                
                // Try to determine which table has the dependency
                $errorMsg = $e->getMessage();
                Log::error('Full error message: ' . $errorMsg);
                
                // Extract table name using various patterns
                $tableName = '';
                if (preg_match('/table "([^"]+)"/', $errorMsg, $matches)) {
                    $tableName = $matches[1];
                } elseif (preg_match('/table ([^\s]+)/', $errorMsg, $matches)) {
                    $tableName = $matches[1];
                } elseif (preg_match('/on table "([^"]+)"/', $errorMsg, $matches)) {
                    $tableName = $matches[1];
                }
                
                Log::info('Extracted table name: ' . $tableName);
                
                if (stripos($errorMsg, 'beneficiaries') !== false || $tableName == 'beneficiaries') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This care manager has created or updated beneficiary records which require audit history to be maintained.',
                        'error_type' => 'dependency_beneficiaries'
                    ]);
                } else if (stripos($errorMsg, 'cose_users') !== false || stripos($errorMsg, 'users') !== false || $tableName == 'cose_users') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This care manager has created or updated user accounts which require audit history to be maintained.',
                        'error_type' => 'dependency_users'
                    ]);
                } else if (stripos($errorMsg, 'weekly_care_plans') !== false || stripos($errorMsg, 'care_plans') !== false || $tableName == 'weekly_care_plans') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This care manager has created or updated care plans which require audit history to be maintained.',
                        'error_type' => 'dependency_care_plans'
                    ]);
                } else if (stripos($errorMsg, 'family_members') !== false || $tableName == 'family_members') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This care manager has created or updated family member records which require audit history to be maintained.',
                        'error_type' => 'dependency_family'
                    ]);
                }
                
                // If in development, return the actual error details
                if (app()->environment('local', 'development')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Database constraint error: ' . $errorMsg,
                        'error_type' => 'dependency_detail',
                        'code' => $e->getCode(),
                        'table' => $tableName ?: 'unknown'
                    ]);
                }
                
                // Generic dependency message for other tables
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated records in the system that require audit history to be maintained.',
                    'error_type' => 'dependency_audit'
                ]);
            }
            
            // If in development, return the actual error
            if (app()->environment('local', 'development')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                    'error_type' => 'database',
                    'code' => $e->getCode()
                ]);
            }
            
            // Generic message for production
            return response()->json([
                'success' => false,
                'message' => 'A database error occurred. Please try again later or contact the system administrator.',
                'error_type' => 'database'
            ]);
        } catch (\Exception $e) {
            Log::error('General error deleting care manager: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If in development, return the actual error
            if (app()->environment('local', 'development')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'error_type' => 'general',
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
            
            // Generic message for production
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_type' => 'general'
            ]);
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

}