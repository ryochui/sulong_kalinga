<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\Medication;
use App\Models\GeneralCarePlan;
use App\Models\CareNeed;
use App\Models\BeneficiaryCategory;
use App\Models\BeneficiaryStatus;
use App\Models\Municipality;
use App\Models\CareWorkerResponsibility;
use App\Models\User;
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

    public function viewProfileDetails(Request $request)
    {
        // Fetch care needs with care_category_id = 1
        $categories = BeneficiaryCategory::all();
        $beneficiary_id = $request->input('beneficiary_id');
        $beneficiary = Beneficiary::with([
            'category', 
            'barangay', 
            'municipality', 
            'status',
            'generalCarePlan.mobility', 
            'generalCarePlan.cognitiveFunction', 
            'generalCarePlan.emotionalWellbeing', 
            'generalCarePlan.mobility',
            'generalCarePlan.medications',
            'generalCarePlan.healthHistory',
            'generalCarePlan.careWorkerResponsibility',
            ])->find($beneficiary_id);
        if (!$beneficiary) {
            return redirect()->route('beneficiaryProfile')->with('error', 'Beneficiary not found.');
        }

        $careNeeds1 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 1);
        $careNeeds2 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 2);
        $careNeeds3 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 3);
        $careNeeds4 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 4);
        $careNeeds5 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 5);
        $careNeeds6 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 6);
        $careNeeds7 = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 7);
        
        // Get the first care worker responsibility for each general care plan
        $careWorkerResponsibility = $beneficiary->generalCarePlan->careWorkerResponsibility->first();
        $careWorker = $careWorkerResponsibility ? $careWorkerResponsibility->careWorker : null;

        return view('admin.viewProfileDetails', compact('beneficiary', 'careNeeds1', 'careNeeds2', 'careNeeds3', 'careNeeds4', 'careNeeds5', 'careNeeds6', 'careNeeds7', 'careWorker')); 
    }

    public function editProfile(Request $request)
    {
        $beneficiary_id = $request->input('beneficiary_id');
        $beneficiary = Beneficiary::with(['category', 'barangay', 'municipality', 'status'])->find($beneficiary_id);
        $beneficiary = Beneficiary::with(['generalCarePlan.mobility', 'generalCarePlan.cognitiveFunction', 'generalCarePlan.emotionalWellbeing', 'generalCarePlan.medications'])->find($request->beneficiary_id);


        if (!$beneficiary) {
            return redirect()->route('beneficiaryProfile')->with('error', 'Beneficiary not found.');
        }

        return view('admin.editProfile', compact('beneficiary')); 
    }
}