<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdminController extends Controller
{
    public function storeAdministrator(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'civil_status' => 'required|string',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'address_details' => 'required|string',
            'account.email' => 'required|email|unique:cose_users,email',
            'personal_email' => 'required|email|unique:cose_users,personal_email',
            'mobile_number' => 'required|string|unique:cose_users,mobile|min:11|max:12',
            'landline_number' => 'nullable|string|min:7|max:10',
            'account.password' => 'required|string|min:8|confirmed',
            'Organization_Roles' => 'required|integer|exists:organization_roles,organization_role_id',
            'administrator_photo' => 'required|image|mimes:jpeg,png|max:2048',
            'government_ID' => 'required|image|mimes:jpeg,png|max:2048',
            'resume' => 'required|mimes:pdf,doc,docx|max:2048',
            'sss_ID' => 'nullable|string|max:20',
            'philhealth_ID' => 'nullable|string|max:20',
            'pagibig_ID' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle file uploads
        $administratorPhotoPath = $request->file('care_worker_photo')->store('uploads/care_worker_photos', 'public');
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
        $administrator->address = $request->input('address_details');
        $administrator->email = $request->input('account.email'); // Work email
        $administrator->personal_email = $request->input('personal_email'); // Personal email
        $administrator->mobile = $request->input('mobile_number');
        $administrator->landline = $request->input('landline_number');
        $administrator->password = bcrypt($request->input('account.password'));
        $administrator->organization_role_id = $request->input('Organization_Roles');
        $administrator->role_id = 1; // Assuming 1 is the role ID for administrators
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

        $administrator->save();

        // Redirect with success message
        return redirect()->route('admin.addAdministrator')->with('success', 'Administrator has been successfully added!');
    }
}