<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;

class AdminController extends Controller
{
    public function storeAdministrator(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
            'first_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]*$/', // First character must be uppercase, no digits or symbols
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]*$/', // First character must be uppercase, no digits or symbols
            ],
            'birth_date' => 'required|date|before:today', // Must be a valid date before today
            'gender' => 'required|string|in:Male,Female,Other', // Must match dropdown options
            'civil_status' => 'required|string|in:Single,Married,Widowed,Divorced', // Must match dropdown options
            'religion' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Z\s]*$/', // Only alphabets and spaces allowed
            ],
            'nationality' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z\s]*$/', // Only alphabets and spaces allowed
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

        // Handle file uploads
        $administratorPhotoPath = $request->file('administrator_photo')->store('uploads/administrator_photos', 'public');
        $governmentIDPath = $request->file('government_ID')->store('uploads/government_ids', 'public');
        $resumePath = $request->file('resume')->store('uploads/resumes', 'public');

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
        return redirect()->route('addAdministrator')->with('success', 'Administrator has been successfully added!');
    }
}