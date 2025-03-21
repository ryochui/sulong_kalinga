<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            ->orderBy('last_name') // Order by last name alphabetically by default
            ->get()
            ->map(function ($family_member) {
                $family_member->status = $family_member->access ? 'Approved' : 'Denied';
                $family_member->municipality = $family_member->beneficiary->municipality;
                return $family_member;
            });

        // Pass the data to the Blade template
        return view('admin.familyProfile', compact('family_members', 'search', 'filter'));
    }
}