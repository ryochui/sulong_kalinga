<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyMember;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;


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

}