<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\CoseUser;

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
            'emmail_address' => 'required|email|unique:cose_users,email',
            'mobile_number' => 'required|string|unique:cose_users,mobile|max:11',
            'landline_number' => 'nullable|string|max:8',
            'account.email' => 'required|email|unique:cose_users,email',
            'account.password' => 'required|string|min:8|confirmed',
            'Orgnanization_Roles' => 'required|integer|exists:organization_roles,organization_role_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Combine first name and last name into the name column
        $fullName = $request->input('first_name') . ' ' . $request->input('last_name');

        // Save the administrator to the database
        $administrator = new CoseUser();
        $administrator->name = $fullName; // Combine first and last name
        $administrator->first_name = $request->input('first_name');
        $administrator->last_name = $request->input('last_name');
        $administrator->birthday = $request->input('birth_date');
        $administrator->gender = $request->input('gender');
        $administrator->civil_status = $request->input('civil_status');
        $administrator->religion = $request->input('religion');
        $administrator->nationality = $request->input('nationality');
        $administrator->address = $request->input('address_details');
        $administrator->email = $request->input('account.email');
        $administrator->mobile = $request->input('mobile_number');
        $administrator->landline = $request->input('landline_number');
        $administrator->password = Hash::make($request->input('account.password'));
        $administrator->organization_role_id = $request->input('Orgnanization_Roles');
        $administrator->role_id = 1; // Assuming 1 is the role ID for administrators
        $administrator->status = 'active'; // Default status
        $administrator->status_start_date = now();

        $administrator->save();

        // Redirect with success message
        return redirect()->route('admin.addAdministrator')->with('success', 'Administrator has been successfully added!');
    }
}