<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\FamilyMember;
// use App\Models\Beneficiary;
// use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
// use App\Models\User;
use App\Models\Municipality;
use App\Models\FamilyMember;
use App\Models\Beneficiary;



class FamilyMemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch family members based on the search query and filters
        $family_members = FamilyMember::with(['beneficiary.municipality'])
            ->when($search, function ($query, $search) {
                return $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                             ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->when($filter, function ($query, $filter) {
                if ($filter == 'access') {
                    return $query->orderBy('access');
                }
            })
            ->orderBy('first_name') // Order by last name alphabetically by default
            ->get()
            ->map(function ($family_member) {
                $family_member->status = $family_member->access ? 'Approved' : 'Denied';
                $family_member->municipality = $family_member->beneficiary->municipality;
                return $family_member;
            });

        // Pass the data to the Blade template
        return view('admin.familyProfile', compact('family_members', 'search', 'filter'));
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        \Log::info('Status received: ' . $request->input('status')); // Log the status value

        try {
            $family_member = FamilyMember::findOrFail($id);
            $family_member->access = $request->input('status') === 'Approved' ? 1 : 0;
            $family_member->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
            $family_member->updated_at = now(); // Set the updated_at column to the current timestamp
            $family_member->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error updating family member status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function viewFamilyDetails(Request $request)
    {
        $family_member_id = $request->input('family_member_id');
        $family_member = FamilyMember::with('beneficiary')->find($family_member_id);

        if (!$family_member) {
            return redirect()->route('familyProfile')->with('error', 'Family member not found.');
        }

        // Add the status property based on the access value
        $family_member->status = $family_member->access ? 'Approved' : 'Denied';

        return view('admin.viewFamilyDetails', compact('family_member'));
    }

    public function editFamilyProfile(Request $request)
    {
        $family_member_id = $request->input('family_member_id');
        $family_member = FamilyMember::find($family_member_id);

        if (!$family_member) {
            return redirect()->route('familyProfile')->with('error', 'Family member not found.');
        }

        return view('admin.editFamilyProfile', compact('family_member'));
    }

    
    // To revise so that dropdown will be dynamic
    public function create()
    {
        // Fetch all beneficiaries from the database
        $beneficiaries = Beneficiary::select('beneficiary_id', 'first_name', 'last_name')->get();

        // Pass the beneficiaries to the view
        return view('admin.addFamily', compact('beneficiaries'));
    }

    public function storeFamily(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
            'family_photo' => 'required|image|mimes:jpeg,png|max:2048',
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
            'gender' => 'required|string|in:Male,Female,Other', // Must match dropdown options
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(), // Must be older than 14 years
            
            'relatedBeneficiary' => 'required|integer|exists:beneficiaries,beneficiary_id',
            'relation_to_beneficiary' => [
                'required',
                'string',
                'in:Son,Daughter,Spouse,Sibling,Grandchild,Other',
            ],

            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,personal_email',
            ],

            'is_primary_caregiver' => [
                'required',
                'boolean',
            ],

            // Address
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/', // Allows alphanumeric characters, spaces, commas, periods, and hyphens
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
        
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle file uploads
        $familyPhotoPath = $request->file('family_photo')->store('uploads/family_photos', 'public');

        // Retrieve the portal_account_id from the selected beneficiary
        $beneficiary = Beneficiary::find($request->input('relatedBeneficiary'));
        if (!$beneficiary) {
            return redirect()->back()->withErrors(['relatedBeneficiary' => 'The selected beneficiary does not exist.'])->withInput();
        }

        // Check if another family member is already the primary caregiver for the same beneficiary
        if ($request->input('is_primary_caregiver')) {
            $existingPrimaryCaregiver = FamilyMember::where('related_beneficiary_id', $request->input('relatedBeneficiary'))
                ->where('is_primary_caregiver', true)
                ->first();

            if ($existingPrimaryCaregiver) {
                return redirect()->back()->withErrors([
                    'is_primary_caregiver' => 'There is already a primary caregiver for this beneficiary. Please update the existing caregiver or set this family member as not the primary caregiver.'
                ])->withInput();
            }
        }

        // Save the administrator to the database
        $familymember = new FamilyMember();
        $familymember->first_name = $request->input('first_name');
        $familymember->last_name = $request->input('last_name');
        // $familymember->name = $request->input('name') . ' ' . $request->input('last_name'); // Combine first and last name
        $familymember->gender = $request->input('gender');
        $familymember->birthday = $request->input('birth_date');
        $familymember->mobile = '+63' . $request->input('mobile_number');
        $familymember->landline = $request->input('landline_number');
        $familymember->is_primary_caregiver = $request->input('is_primary_caregiver');
        $familymember->related_beneficiary_id = $request->input('relatedBeneficiary');
        $familymember->relation_to_beneficiary = $request->input('relation_to_beneficiary');
        $familymember->email = $request->input('personal_email');
        // $familymember->password = bcrypt($request->input('account.password'));
        $familymember->street_address = $request->input('address_details');
        $familymember->access = True; // Status for access to the system
        $familymember->created_at = now();
        $familymember->created_by = Auth::id(); // Set the created_by column to the current user's ID   
        // $familymember->assigned_municipality_id = $request->input('municipality');

        // Save file paths and IDs
        $familymember->photo = $familyPhotoPath;

        // Assign the portal_account_id from the beneficiary
        $familymember->portal_account_id = $beneficiary->portal_account_id;

        // Generate and save the remember_token
        $familymember->remember_token = Str::random(60);


        $familymember->save();

        // Redirect with success message
        return redirect()->route('admin.addFamily')->with('success', 'Family Member or Relative has been successfully added!');
    }
}