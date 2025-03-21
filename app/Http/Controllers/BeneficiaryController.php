<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\BeneficiaryCategory;
use App\Models\BeneficiaryStatus;
use App\Models\Municipality;
use Illuminate\Support\Facades\Auth;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch all categories and statuses
        $categories = BeneficiaryCategory::all();
        $statuses = BeneficiaryStatus::all();

        // Fetch beneficiaries based on the search query and filters
        $beneficiaries = Beneficiary::with('category', 'status', 'municipality')
            ->when($search, function ($query, $search) {
                return $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                             ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->when($filter, function ($query, $filter) {
                if ($filter == 'category') {
                    return $query->orderBy('category_id');
                } elseif ($filter == 'status') {
                    return $query->orderBy('beneficiary_status_id');
                } elseif ($filter == 'municipality') {
                    return $query->orderBy('municipality_id');
                }
            })
            ->orderBy('first_name') // Order by first name alphabetically by default
            ->get();

        // Pass the data to the Blade template
        return view('admin.beneficiaryProfile', compact('beneficiaries', 'search', 'filter', 'categories', 'statuses'));
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|string',
            'reason' => 'required|string'
        ]);

        try {
            $beneficiary = Beneficiary::findOrFail($id);
            $status = BeneficiaryStatus::where('status_name', $request->input('status'))->firstOrFail();
            $beneficiary->beneficiary_status_id = $status->beneficiary_status_id;
            $beneficiary->status_reason = $request->input('reason');
            $beneficiary->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
            $beneficiary->updated_at = now(); // Set the updated_at column to the current timestamp
            $beneficiary->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error updating beneficiary status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function activate($id, Request $request)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $beneficiary->beneficiary_status_id = 1;
        $beneficiary->status_reason = null;
        $beneficiary->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
        $beneficiary->updated_at = now(); // Set the updated_at column to the current timestamp
        $beneficiary->save();

        return response()->json(['success' => true]);
    }
}