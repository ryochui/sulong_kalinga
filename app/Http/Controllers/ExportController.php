<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use App\Models\GeneralCarePlan;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportBeneficiariesToPdf(Request $request)
    {
        // Validate the request
        $request->validate([
            'selected_beneficiaries' => 'required',
        ]);
        
        // Get the selected beneficiary IDs
        $beneficiaryIds = json_decode($request->selected_beneficiaries, true);
        
        if (empty($beneficiaryIds)) {
            return redirect()->back()->with('error', 'No beneficiaries selected for export.');
        }
        
        // Fetch the beneficiaries with their relationships
        $beneficiaries = Beneficiary::with([
            'category', 
            'barangay', 
            'municipality', 
            'status',
            'generalCarePlan'
        ])->whereIn('beneficiary_id', $beneficiaryIds)->get();
        
        // Generate PDF
        $pdf = PDF::loadView('exports.beneficiaries-pdf', [
            'beneficiaries' => $beneficiaries,
            'exportDate' => now()->format('F j, Y')
        ]);
        
        // Set paper size to portrait (A4)
        $pdf->setPaper('a4', 'portrait');
        
        // Return PDF for download
        return $pdf->download('beneficiaries-profiles-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportFamilyToPdf(Request $request)
    {
        // Validate the request
        $request->validate([
            'selected_family_members' => 'required',
        ]);
        
        // Get the selected family member IDs
        $familyMemberIds = json_decode($request->selected_family_members, true);
        
        if (empty($familyMemberIds)) {
            return redirect()->back()->with('error', 'No family members selected for export.');
        }
        
        // Fetch the family members with their relationships
        $familyMembers = FamilyMember::with([
            'beneficiary',
        ])->whereIn('family_member_id', $familyMemberIds)->get()
        ->map(function ($family_member) {
            $family_member->status = $family_member->access ? 'Approved' : 'Denied';
            return $family_member;
        });
        
        // Generate PDF
        $pdf = Pdf::loadView('exports.family-pdf', [
            'familyMembers' => $familyMembers,
            'exportDate' => now()->format('F j, Y')
        ]);
        
        // Set paper size to portrait (A4)
        $pdf->setPaper('a4', 'portrait');
        
        // Return PDF for download
        return $pdf->download('family-members-profiles-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCareworkersToPdf(Request $request)
    {
        // Validate the request
        $request->validate([
            'selected_careworkers' => 'required',
        ]);
        
        // Get the selected care worker IDs
        $careworkerIds = json_decode($request->selected_careworkers, true);
        
        if (empty($careworkerIds)) {
            return redirect()->back()->with('error', 'No care workers selected for export.');
        }
        
        // Fetch the care workers with their relationships
        $careworkers = User::where('role_id', 3)
            ->with(['municipality'])
            ->whereIn('id', $careworkerIds)
            ->get();
        
        // For each care worker, get their assigned beneficiaries
        foreach ($careworkers as $careworker) {
            // Fetch all general care plans associated with this care worker
            $generalCarePlans = GeneralCarePlan::where('care_worker_id', $careworker->id)->get();
            
            // Fetch all beneficiaries associated with these general care plans
            $careworker->assignedBeneficiaries = Beneficiary::whereIn('general_care_plan_id', $generalCarePlans->pluck('general_care_plan_id'))->get();
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('exports.careworkers-pdf', [
            'careworkers' => $careworkers,
            'exportDate' => now()->format('F j, Y')
        ]);
        
        // Set paper size to portrait (A4)
        $pdf->setPaper('a4', 'portrait');
        
        // Return PDF for download
        return $pdf->download('careworkers-profiles-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCaremanagersToPdf(Request $request)
    {
        // Validate the request
        $request->validate([
            'selected_caremanagers' => 'required',
        ]);
        
        // Get the selected care manager IDs
        $caremanagerIds = json_decode($request->selected_caremanagers, true);
        
        if (empty($caremanagerIds)) {
            return redirect()->back()->with('error', 'No care managers selected for export.');
        }
        
        // Fetch the care managers with their relationships
        $caremanagers = User::where('role_id', 2)
            ->with(['municipality'])
            ->whereIn('id', $caremanagerIds)
            ->get();
        
        // For each care manager, get their assigned care workers
        foreach ($caremanagers as $caremanager) {
            // Fetch all care workers assigned to this care manager
            $caremanager->assignedCareWorkers = User::where('role_id', 3)
                ->where('id', $caremanager->id)
                ->get();
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('exports.caremanagers-pdf', [
            'caremanagers' => $caremanagers,
            'exportDate' => now()->format('F j, Y')
        ]);
        
        // Set paper size to portrait (A4)
        $pdf->setPaper('a4', 'portrait');
        
        // Return PDF for download
        return $pdf->download('caremanagers-profiles-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportAdministratorsToPdf(Request $request)
    {
        // Validate the request
        $request->validate([
            'selected_administrators' => 'required',
        ]);
        
        // Get the selected administrator IDs
        $administratorIds = json_decode($request->selected_administrators, true);
        
        if (empty($administratorIds)) {
            return redirect()->back()->with('error', 'No administrators selected for export.');
        }
        
        // Fetch the administrators with their relationships
        $administrators = User::where('role_id', 1)
            ->with(['organizationRole'])
            ->whereIn('id', $administratorIds)
            ->get();
        
        // Generate PDF
        $pdf = Pdf::loadView('exports.administrators-pdf', [
            'administrators' => $administrators,
            'exportDate' => now()->format('F j, Y')
        ]);
        
        // Set paper size to portrait (A4)
        $pdf->setPaper('a4', 'portrait');
        
        // Return PDF for download
        return $pdf->download('administrators-profiles-' . now()->format('Y-m-d') . '.pdf');
    }
}