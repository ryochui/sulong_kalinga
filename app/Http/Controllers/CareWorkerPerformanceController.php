<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Municipality;
use App\Models\WeeklyCarePlan;
use App\Models\WeeklyCarePlanInterventions;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CareWorkerPerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Get all care workers (role_id = 3)
        $careWorkers = User::where('role_id', 3)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        // Get all municipalities
        $municipalities = Municipality::orderBy('municipality_name')->get();
        
        // Get available years from weekly care plans
        $availableYears = WeeklyCarePlan::select(DB::raw('EXTRACT(YEAR FROM date) as year'))
        ->distinct()
        ->orderBy('year')
        ->pluck('year')
        ->toArray();

        // If no years available, add current year
        if (empty($availableYears)) {
            $availableYears = [Carbon::now()->year];
        }
        
        // Get filters from request or set defaults
        $selectedCareWorkerId = $request->input('care_worker_id', null);
        $selectedMunicipalityId = $request->input('municipality_id', null);
        $selectedTimeRange = $request->input('time_range', 'weeks');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedStartMonth = $request->input('start_month', 1);
        $selectedEndMonth = $request->input('end_month', 12);
        
        // FIXED: Use current year as default, and only use database years if they're recent
        $currentYear = Carbon::now()->year;
        if (!empty($availableYears) && max($availableYears) >= ($currentYear - 5)) {
            // Only use most recent year from database if it's within 5 years of current
            $selectedYear = $request->input('year', max($availableYears));
        } else {
            // Otherwise default to current year
            $selectedYear = $request->input('year', $currentYear);
            
            // Make sure current year is in the available years list
            if (!in_array($currentYear, $availableYears)) {
                $availableYears[] = $currentYear;
                sort($availableYears);
            }
        }
        
        // Get active care workers count
        $activeCareWorkersCount = User::where('role_id', 3)
            ->where('status', 'Active')
            ->count();
        
        // Calculate date ranges based on time range selection
        $startDate = null;
        $endDate = null;
        $dateRangeLabel = '';
        
        switch ($selectedTimeRange) {
            case 'weeks':
                $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();
                $dateRangeLabel = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');
                break;
                
            case 'months':
                $startDate = Carbon::createFromDate($selectedYear, $selectedStartMonth, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($selectedYear, $selectedEndMonth, 1)->endOfMonth();
                $startMonthName = Carbon::createFromDate($selectedYear, $selectedStartMonth, 1)->format('F');
                $endMonthName = Carbon::createFromDate($selectedYear, $selectedEndMonth, 1)->format('F');
                $dateRangeLabel = "$startMonthName - $endMonthName $selectedYear";
                break;
                
            case 'year':
                $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
                $endDate = Carbon::createFromDate($selectedYear, 12, 31)->endOfYear();
                $dateRangeLabel = $selectedYear;
                break;
        }

        // Build query based on filters
        $query = WeeklyCarePlan::query()
            ->whereBetween('date', [$startDate, $endDate]);

        // Filter by care worker if selected
        if ($selectedCareWorkerId) {
            $query->where('care_worker_id', $selectedCareWorkerId);
        }

        // Filter by municipality if selected and care worker is not selected
        if ($selectedMunicipalityId && !$selectedCareWorkerId) {
            $careWorkerIds = User::where('assigned_municipality_id', $selectedMunicipalityId)
                ->where('role_id', 3)
                ->pluck('id');
            $query->whereIn('care_worker_id', $careWorkerIds);
        }

        // Get filtered weekly care plans
        $weeklyCarePlanIds = $query->pluck('weekly_care_plan_id')->toArray();

        // Calculate total care hours (in minutes)
        $totalCareMinutes = WeeklyCarePlanInterventions::whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
            ->sum('duration_minutes');
        
        // Convert minutes to hours
        $totalHours = floor($totalCareMinutes / 60);
        $totalMinutes = $totalCareMinutes % 60;
        $formattedCareTime = [
            'hours' => $totalHours,
            'minutes' => $totalMinutes
        ];

        // Count total interventions
        $totalInterventions = WeeklyCarePlanInterventions::whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
            ->count();

        // Check if any records exist for the selected filters
        $hasRecords = !empty($weeklyCarePlanIds);
        
        // Get most implemented interventions
        $mostImplementedInterventions = [];
        if ($hasRecords) {
            $mostImplementedInterventions = WeeklyCarePlanInterventions::whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
                ->select('intervention_description', DB::raw('COUNT(*) as count'))
                ->groupBy('intervention_description')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
        }
        
        // Get hours per client
        $hoursPerClient = [];
        if ($hasRecords) {
            $hoursPerClient = DB::table('weekly_care_plan_interventions')
                ->join('weekly_care_plans', 'weekly_care_plan_interventions.weekly_care_plan_id', '=', 'weekly_care_plans.weekly_care_plan_id')
                ->join('beneficiaries', 'weekly_care_plans.beneficiary_id', '=', 'beneficiaries.beneficiary_id')
                ->whereIn('weekly_care_plan_interventions.weekly_care_plan_id', $weeklyCarePlanIds)
                ->select(
                    DB::raw("CONCAT(beneficiaries.last_name, ', ', LEFT(beneficiaries.first_name, 1), '.') as beneficiary_name"),
                    DB::raw('SUM(weekly_care_plan_interventions.duration_minutes) / 60 as hours')
                )
                ->groupBy('beneficiaries.beneficiary_id', 'beneficiaries.last_name', 'beneficiaries.first_name')
                ->orderBy('hours', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
        }
        
        return view('admin.careWorkerPerformance', compact(
            'careWorkers',
            'municipalities',
            'availableYears',
            'selectedCareWorkerId',
            'selectedMunicipalityId',
            'selectedTimeRange',
            'selectedMonth',
            'selectedStartMonth',
            'selectedEndMonth',
            'selectedYear',
            'formattedCareTime',  // New variable
            'activeCareWorkersCount',
            'totalInterventions',
            'dateRangeLabel',
            'hasRecords',
            'mostImplementedInterventions',
            'hoursPerClient'
        ));
    }
}