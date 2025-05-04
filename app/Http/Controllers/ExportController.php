<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\User;
use App\Models\GeneralCarePlan;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BeneficiariesExport;
use App\Exports\FamilyMembersExport;
use App\Exports\CareManagersExport;
use App\Exports\CareworkersExport;
use App\Exports\AdministratorsExport;

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
        
        // Fetch the beneficiaries with all their relationships
        $beneficiaries = Beneficiary::with([
            'category', 
            'barangay', 
            'municipality', 
            'status',
            'generalCarePlan.mobility', 
            'generalCarePlan.cognitiveFunction', 
            'generalCarePlan.emotionalWellbeing',
            'generalCarePlan.medications',
            'generalCarePlan.healthHistory',
            'generalCarePlan.careNeeds',
            'generalCarePlan.careWorkerResponsibility'
        ])->whereIn('beneficiary_id', $beneficiaryIds)->get();
        
        // Prepare care needs and care worker data for each beneficiary
        $allData = [];
        foreach ($beneficiaries as $beneficiary) {
            $data = [
                'beneficiary' => $beneficiary,
                'careNeeds1' => [],
                'careNeeds2' => [],
                'careNeeds3' => [],
                'careNeeds4' => [],
                'careNeeds5' => [],
                'careNeeds6' => [],
                'careNeeds7' => [],
                'careWorker' => null
            ];
            
            if ($beneficiary->generalCarePlan) {
                $data['careNeeds1'] = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 1);
                $data['careNeeds2'] = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 2);
                $data['careNeeds3'] = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 3);
                $data['careNeeds4'] = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 4);
                $data['careNeeds5'] = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 5);
                $data['careNeeds6'] = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 6);
                $data['careNeeds7'] = $beneficiary->generalCarePlan->careNeeds->where('care_category_id', 7);
                
                // Get the care worker
                $careWorkerResponsibility = $beneficiary->generalCarePlan->careWorkerResponsibility->first();
                $data['careWorker'] = $careWorkerResponsibility ? User::find($careWorkerResponsibility->care_worker_id) : null;
            }
            
            $allData[] = $data;
        }
        
        // Generate PDF with all beneficiaries' data
        $pdf = PDF::loadView('exports.beneficiaries-pdf', [
            'allData' => $allData,
            'beneficiaries' => $beneficiaries, // All beneficiaries
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
            'beneficiary.category', // Include beneficiary's category
        ])->whereIn('family_member_id', $familyMemberIds)->get();
        
        // Process each family member (similar to how we process beneficiaries)
        $allData = [];
        foreach ($familyMembers as $family_member) {
            // Set the status
            $family_member->status = $family_member->access ? 'Approved' : 'Denied';
            
            // Prepare data structure for this family member
            $data = [
                'family_member' => $family_member,
                // Add any additional processed data here if needed
                'relatedBeneficiaryInfo' => $family_member->beneficiary ? [
                    'name' => $family_member->beneficiary->first_name . ' ' . $family_member->beneficiary->last_name,
                    'category' => $family_member->beneficiary->category->category_name ?? 'N/A',
                    'status' => $family_member->beneficiary->status->status_name ?? 'N/A'
                ] : null
            ];
            
            $allData[] = $data;
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('exports.family-pdf', [
            'allData' => $allData,
            'familyMembers' => $familyMembers, // Pass all family members for TOC
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
        
        // Get the selected careworker IDs
        $careworkerIds = json_decode($request->selected_careworkers, true);
        
        if (empty($careworkerIds)) {
            return redirect()->back()->with('error', 'No care workers selected for export.');
        }
        
        // Fetch the careworkers with their relationships - add assignedCareManager
        $careworkers = User::with([
            'municipality',
            'barangay',
            'assignedCareManager' // Added the care manager relationship
        ])->whereIn('id', $careworkerIds)
        ->where('role_id', '3') // Only care workers
        ->get();
        
        // Process each careworker
        $allData = [];
        foreach ($careworkers as $careworker) {
            // Get assigned beneficiaries for this careworker
            $assignedBeneficiaries = Beneficiary::whereHas('generalCarePlan.careWorkerResponsibility', function($query) use ($careworker) {
                $query->where('care_worker_id', $careworker->id);
            })->get();
            
            // Create data structure for this careworker
            $data = [
                'careworker' => $careworker,
                'assignedBeneficiaries' => $assignedBeneficiaries
            ];
            
            $allData[] = $data;
        }
        
        // Generate PDF
        $pdf = PDF::loadView('exports.careworkers-pdf', [
            'allData' => $allData,
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
        
        // Get the selected caremanager IDs
        $caremanagerIds = json_decode($request->selected_caremanagers, true);
        
        if (empty($caremanagerIds)) {
            return redirect()->back()->with('error', 'No care managers selected for export.');
        }
        
        // Fetch the caremanagers with their relationships
        $caremanagers = User::with([
            'municipality',
            'barangay'
        ])->whereIn('id', $caremanagerIds)
        ->where('role_id', '2') // Only care managers
        ->get();
        
        // Process each caremanager
        $allData = [];
        foreach ($caremanagers as $caremanager) {
            // Create data structure for this care manager
            $data = [
                'caremanager' => $caremanager,
                // Add any additional processed data here if needed
            ];
            
            $allData[] = $data;
        }
        
        // Generate PDF
        $pdf = PDF::loadView('exports.caremanagers-pdf', [
            'allData' => $allData,
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
        $administrators = User::with([
            'organizationRole'
        ])->whereIn('id', $administratorIds)
        ->where('role_id', '1') // Only administrators
        ->get();
        
        // Process each administrator
        $allData = [];
        foreach ($administrators as $administrator) {
            // Create data structure for this administrator
            $data = [
                'administrator' => $administrator,
                // Add any additional processed data here if needed
            ];
            
            $allData[] = $data;
        }
        
        // Generate PDF
        $pdf = PDF::loadView('exports.administrators-pdf', [
            'allData' => $allData,
            'administrators' => $administrators,
            'exportDate' => now()->format('F j, Y')
        ]);
        
        // Set paper size to portrait (A4)
        $pdf->setPaper('a4', 'portrait');
        
        // Return PDF for download
        return $pdf->download('administrators-profiles-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportBeneficiariesToExcel(Request $request)
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
        
        // Generate filename with current date
        $filename = 'beneficiaries-' . now()->format('Y-m-d') . '.xlsx';
        
        // Return the Excel download
        return Excel::download(new BeneficiariesExport($beneficiaryIds), $filename);
    }

    public function exportFamilyMembersToExcel(Request $request)
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
        
        // Generate filename with current date
        $filename = 'family-members-' . now()->format('Y-m-d') . '.xlsx';
        
        // Return the Excel download
        return Excel::download(new FamilyMembersExport($familyMemberIds), $filename);
    }

    public function exportCareManagersToExcel(Request $request)
    {
        // Validate the request
        $request->validate([
            'selected_caremanagers' => 'required',
        ]);
        
        // Get the selected care manager IDs
        $careManagerIds = json_decode($request->selected_caremanagers, true);
        
        if (empty($careManagerIds)) {
            return redirect()->back()->with('error', 'No care managers selected for export.');
        }
        
        // Generate filename with current date
        $filename = 'care-managers-' . now()->format('Y-m-d') . '.xlsx';
        
        // Return the Excel download
        return Excel::download(new CareManagersExport($careManagerIds), $filename);
    }

    public function exportCareworkersToExcel(Request $request)
    {
        // Validate the request
        $request->validate([
            'selected_careworkers' => 'required',
        ]);
        
        // Get the selected careworker IDs
        $careworkerIds = json_decode($request->selected_careworkers, true);
        
        if (empty($careworkerIds)) {
            return redirect()->back()->with('error', 'No care workers selected for export.');
        }
        
        // Generate filename with current date
        $filename = 'careworkers-' . now()->format('Y-m-d') . '.xlsx';
        
        // Return the Excel download
        return Excel::download(new CareworkersExport($careworkerIds), $filename);
    }

    public function exportAdministratorsToExcel(Request $request)
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
        
        // Generate filename with current date
        $filename = 'administrators-' . now()->format('Y-m-d') . '.xlsx';
        
        // Return the Excel download
        return Excel::download(new AdministratorsExport($administratorIds), $filename);
    }

    public function exportCareWorkerPerformanceToPdf(Request $request)
    {
        // Get the same filters that were used in the view
        $careWorkerId = $request->input('care_worker_id');
        $municipalityId = $request->input('municipality_id');
        $timeRange = $request->input('time_range', 'weeks');
        $month = $request->input('month');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        $year = $request->input('year');

        // Create a new request with these parameters
        $performanceRequest = new Request();
        $performanceRequest->merge([
            'care_worker_id' => $careWorkerId,
            'municipality_id' => $municipalityId,
            'time_range' => $timeRange,
            'month' => $month,
            'start_month' => $startMonth,
            'end_month' => $endMonth,
            'year' => $year
        ]);

        // Use the CareWorkerPerformanceController to get the same data
        $performanceController = new CareWorkerPerformanceController();
        
        // This is a trick to get the data without rendering the view
        $response = $performanceController->index($performanceRequest);
        
        // Extract the data from the response
        $data = $response->getData();
        
        // Convert chart data to images using Chart.js server-side rendering or placeholder images
        // For simplicity, we'll use a PDF-optimized version without interactive charts
        
        // Get specific data needed for the PDF
        $selectedCareWorker = null;
        if ($careWorkerId) {
            $selectedCareWorker = User::find($careWorkerId);
        }
        
        $selectedMunicipality = null;
        if ($municipalityId) {
            $selectedMunicipality = Municipality::find($municipalityId);
        }
        
        // Format the date for the PDF
        $filterDescription = "";
        if ($selectedCareWorker) {
            $filterDescription .= "Care Worker: " . $selectedCareWorker->first_name . " " . $selectedCareWorker->last_name . " | ";
        } else {
            $filterDescription .= "Care Worker: All | ";
        }
        
        if ($selectedMunicipality) {
            $filterDescription .= "Municipality: " . $selectedMunicipality->municipality_name . " | ";
        } else {
            $filterDescription .= "Municipality: All | ";
        }
        
        $filterDescription .= "Time Range: " . ucfirst($timeRange) . " | ";
        $filterDescription .= "Date: " . $data['dateRangeLabel'];
        
        // Generate the PDF using the extracted data
        $pdf = PDF::loadView('exports.careworker-performance-pdf', [
            'data' => $data,
            'filterDescription' => $filterDescription,
            'exportDate' => now()->format('F d, Y h:i A'),
        ]);

        // Set paper size to landscape for better layout
        $pdf->setPaper('a4', 'landscape');
        
        // Generate a filename with date and time
        $filename = 'CareWorker_Performance_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        // Return the PDF as a download
        return $pdf->download($filename);
    }
}