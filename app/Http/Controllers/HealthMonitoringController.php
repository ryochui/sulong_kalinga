<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\Municipality;
use App\Models\BeneficiaryCategory;
use App\Models\CareCategory;
use App\Models\Intervention;
use App\Models\CareNeed;
use App\Models\BeneficiaryStatus;
use App\Models\WeeklyCarePlan;
use App\Models\VitalSigns;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HealthMonitoringController extends Controller
{
    public function index(Request $request)
    {
        // Get all active beneficiaries (status_id = 1)
        $beneficiaries = Beneficiary::where('beneficiary_status_id', 1)
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
        $selectedBeneficiaryId = $request->input('beneficiary_id', null);
        $selectedMunicipalityId = $request->input('municipality_id', null);
        $selectedTimeRange = $request->input('time_range', 'weeks');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedStartMonth = $request->input('start_month', 1);
        $selectedEndMonth = $request->input('end_month', 12);
        
        // Use current year as default, and only use database years if they're recent
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

        // Get selected beneficiary details if one is selected
        $selectedBeneficiary = null;
        if ($selectedBeneficiaryId) {
            $selectedBeneficiary = Beneficiary::with(['category', 'municipality', 'barangay'])
                ->find($selectedBeneficiaryId);
        }
        
        // Compute health statistics when no specific beneficiary is selected
        $healthStatistics = [];
        $totalPopulation = 0;
        // Initialize totals with default values so it's always defined
        $totals = [
            'age_60_69' => 0, 'age_70_79' => 0, 'age_80_89' => 0, 'age_90_plus' => 0, 
            'male' => 0, 'female' => 0, 
            'single' => 0, 'married' => 0, 'widowed' => 0
        ];

        // Get vital signs history if a beneficiary is selected
        $vitalSignsHistory = [];
            
        if (!$selectedBeneficiary) {
            // Start with beneficiary query for active beneficiaries
            $beneficiaryQuery = Beneficiary::where('beneficiary_status_id', 1);
            
            // Filter by municipality if selected
            if ($selectedMunicipalityId) {
                $beneficiaryQuery->where('municipality_id', $selectedMunicipalityId);
            }
            
            // Get total population count
            $totalPopulation = $beneficiaryQuery->count();
            
            // Get statistics by category
            $categoryStats = $beneficiaryQuery->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->orderBy('category_id') // Order by the grouped column
            ->get();
                
            $categories = BeneficiaryCategory::all()->keyBy('category_id');
            
            // Format category statistics
            foreach ($categoryStats as $stat) {
                if (isset($categories[$stat->category_id])) {
                    $healthStatistics[] = [
                        'category' => $categories[$stat->category_id]->category_name,
                        'count' => $stat->count,
                        'percentage' => $totalPopulation > 0 ? round(($stat->count / $totalPopulation) * 100, 2) : 0
                    ];
                }
            }
            
            // Get statistics by gender
            $genderStats = $beneficiaryQuery->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->orderBy('gender') // Order by the grouped column
            ->get();
                
            foreach ($genderStats as $stat) {
                $healthStatistics[] = [
                    'category' => $stat->gender,
                    'count' => $stat->count,
                    'percentage' => $totalPopulation > 0 ? round(($stat->count / $totalPopulation) * 100, 2) : 0
                ];
            }
            
            // Get statistics by civil status
            $civilStatusStats = $beneficiaryQuery->select('civil_status', DB::raw('count(*) as count'))
                ->groupBy('civil_status')
                ->orderBy('civil_status') // Change ordering to use the grouped column instead
                ->get();
                
            foreach ($civilStatusStats as $stat) {
                $healthStatistics[] = [
                    'category' => $stat->civil_status,
                    'count' => $stat->count,
                    'percentage' => $totalPopulation > 0 ? round(($stat->count / $totalPopulation) * 100, 2) : 0
                ];
            }
            
            // Calculate age groups
            $now = Carbon::now();
            $ageGroups = [
                '60-69' => [0, 0], // [count, percentage]
                '70-79' => [0, 0],
                '80-89' => [0, 0],
                '90+' => [0, 0]
            ];
            
            $beneficiaryQuery->chunk(100, function ($beneficiaries) use (&$ageGroups, $now, $totalPopulation) {
                foreach ($beneficiaries as $beneficiary) {
                    $birthday = Carbon::parse($beneficiary->birthday);
                    $age = $birthday->diffInYears($now);
                    
                    if ($age >= 90) {
                        $ageGroups['90+'][0]++;
                    } elseif ($age >= 80) {
                        $ageGroups['80-89'][0]++;
                    } elseif ($age >= 70) {
                        $ageGroups['70-79'][0]++;
                    } else {
                        $ageGroups['60-69'][0]++;
                    }
                }
            });
            
            // Calculate percentages for age groups
            foreach ($ageGroups as $range => $data) {
                $count = $data[0];
                $percentage = $totalPopulation > 0 ? round(($count / $totalPopulation) * 100, 2) : 0;
                
                $healthStatistics[] = [
                    'category' => $range . ' years old',
                    'count' => $count,
                    'percentage' => $percentage
                ];
            }
        }

        // If a specific beneficiary is selected, get their vital signs history
        $vitalSignsHistory = [];
        if ($selectedBeneficiary) {
            $vitalSignsHistory = DB::table('vital_signs as vs')
                ->join('weekly_care_plans as wcp', 'vs.vital_signs_id', '=', 'wcp.vital_signs_id')
                ->where('wcp.beneficiary_id', $selectedBeneficiaryId)
                ->whereBetween('wcp.date', [$startDate, $endDate])
                ->orderBy('wcp.date', 'asc')
                ->select(
                    'wcp.date',
                    'vs.blood_pressure',
                    'vs.body_temperature',
                    'vs.pulse_rate',
                    'vs.respiratory_rate'
                )
                ->get();
        }


        
        if (!$selectedBeneficiary) {
            // Initialize statistics array and totals
            $healthStatistics = [];
            $totals = [
                'age_60_69' => 0, 'age_70_79' => 0, 'age_80_89' => 0, 'age_90_plus' => 0, 
                'male' => 0, 'female' => 0, 
                'single' => 0, 'married' => 0, 'widowed' => 0
            ];

            // Get beneficiaries filtered by municipality if selected
            $beneficiaryQuery = Beneficiary::where('beneficiary_status_id', 1);
            if ($selectedMunicipalityId) {
                $beneficiaryQuery->where('municipality_id', $selectedMunicipalityId);
            }
            $totalPopulation = $beneficiaryQuery->count();

            // Get category statistics and build matrix
            $categories = BeneficiaryCategory::all();
            foreach ($categories as $category) {
                $query = clone $beneficiaryQuery;
                $query->where('category_id', $category->category_id);
                $categoryCount = $query->count();
                
                if ($categoryCount > 0) {
                    // Initialize category statistics array
                    $healthStatistics[$category->category_name] = [
                        'age_60_69' => 0, 'age_70_79' => 0, 'age_80_89' => 0, 'age_90_plus' => 0,
                        'male' => 0, 'female' => 0, 
                        'single' => 0, 'married' => 0, 'widowed' => 0,
                        'percentage' => round(($categoryCount / $totalPopulation) * 100, 1)
                    ];
                    
                    // Get age distribution
                    $now = Carbon::now();
                    $query->chunk(100, function ($beneficiaries) use (&$healthStatistics, $category, $now, &$totals) {
                        foreach ($beneficiaries as $beneficiary) {
                            $birthday = Carbon::parse($beneficiary->birthday);
                            $age = $birthday->diffInYears($now);
                            
                            // Age group
                            if ($age >= 90) {
                                $healthStatistics[$category->category_name]['age_90_plus']++;
                                $totals['age_90_plus']++;
                            } elseif ($age >= 80) {
                                $healthStatistics[$category->category_name]['age_80_89']++;
                                $totals['age_80_89']++;
                            } elseif ($age >= 70) {
                                $healthStatistics[$category->category_name]['age_70_79']++;
                                $totals['age_70_79']++;
                            } else {
                                $healthStatistics[$category->category_name]['age_60_69']++;
                                $totals['age_60_69']++;
                            }
                            
                            // Gender
                            if ($beneficiary->gender === 'Male') {
                                $healthStatistics[$category->category_name]['male']++;
                                $totals['male']++;
                            } else if ($beneficiary->gender === 'Female') {
                                $healthStatistics[$category->category_name]['female']++;
                                $totals['female']++;
                            }
                            
                            // Civil status
                            if ($beneficiary->civil_status === 'Single') {
                                $healthStatistics[$category->category_name]['single']++;
                                $totals['single']++;
                            } else if ($beneficiary->civil_status === 'Married') {
                                $healthStatistics[$category->category_name]['married']++;
                                $totals['married']++;
                            } else if ($beneficiary->civil_status === 'Widowed') {
                                $healthStatistics[$category->category_name]['widowed']++;
                                $totals['widowed']++;
                            }
                        }
                    });
                }
            }
        }
        $careCategories = CareCategory::orderBy('care_category_name')->get();
        $careServicesSummary = [];
        
        // Query base - weekly care plans filtered by date range
        $wcpQuery = WeeklyCarePlan::whereBetween('date', [$startDate, $endDate]);
        
        // Filter by municipality if selected
        if ($selectedMunicipalityId) {
            $wcpQuery->whereHas('beneficiary', function($query) use ($selectedMunicipalityId) {
                $query->where('municipality_id', $selectedMunicipalityId);
            });
        }
        
        // Filter by beneficiary if selected
        if ($selectedBeneficiaryId) {
            $wcpQuery->where('beneficiary_id', $selectedBeneficiaryId);
        }
        
        // Get the filtered weekly care plan IDs
        $filteredWcpIds = $wcpQuery->pluck('weekly_care_plan_id')->toArray();
        
        // Prepare care services summary data
        foreach ($careCategories as $category) {
            $interventions = Intervention::where('care_category_id', $category->care_category_id)->get();
            $interventionData = [];
            $hasInterventions = false;
            
            foreach ($interventions as $intervention) {
                // Get implementation data for this intervention
                $implementations = DB::table('weekly_care_plan_interventions')
                    ->whereIn('weekly_care_plan_id', $filteredWcpIds)
                    ->where('intervention_id', $intervention->intervention_id)
                    ->count();
                    
                // Get total duration in minutes
                $totalMinutes = DB::table('weekly_care_plan_interventions')
                    ->whereIn('weekly_care_plan_id', $filteredWcpIds)
                    ->where('intervention_id', $intervention->intervention_id)
                    ->sum('duration_minutes');
                    
                // Only include interventions that were actually implemented
                if ($implementations > 0) {
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    
                    $interventionData[] = [
                        'description' => $intervention->intervention_description,
                        'implementations' => $implementations,
                        'hours' => $hours,
                        'minutes' => $minutes,
                        'formatted_duration' => $hours . ' hrs ' . ($minutes > 0 ? $minutes . ' min' : '')
                    ];
                    
                    $hasInterventions = true;
                }
            }
            
            // Add to care services summary
            $careServicesSummary[$category->care_category_id] = [
                'category_name' => $category->care_category_name,
                'interventions' => $interventionData,
                'has_interventions' => $hasInterventions
            ];
        }

        return view('admin.healthMonitoring', compact(
            'beneficiaries',
            'municipalities',
            'availableYears',
            'selectedBeneficiaryId',
            'selectedMunicipalityId',
            'selectedTimeRange',
            'selectedMonth',
            'selectedStartMonth',
            'selectedEndMonth',
            'selectedYear',
            'dateRangeLabel',
            'selectedBeneficiary',
            'healthStatistics',
            'totalPopulation',
            'vitalSignsHistory',
            'totals',
            'careCategories',
            'careServicesSummary'
        ));
    }
}