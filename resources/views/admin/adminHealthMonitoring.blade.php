<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">

    <style>
        tbody td{
            vertical-align: middle;
        }
        th button{
            transform: translateY(-3px);
        }

        tbody td {
        vertical-align: middle;
        }

        th button {
            transform: translateY(-3px);
        }

        #home-content {
            font-size: clamp(0.8rem, 1vw, 1rem);
        }

        #home-content th,
        #home-content td {
            font-size: clamp(0.7rem, 0.9vw, 0.9rem);
        }

        #home-content .card-header,
        #home-content .form-label {
            font-size: clamp(0.9rem, 1.1vw, 1.1rem);
        }

        #home-content .btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        #home-content .form-select,
        #home-content .form-control {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        #home-content .table {
            font-size: 0.8rem;
        }

        #beneficiaryDetailsRow .form-label {
            font-size: clamp(0.8rem, 1vw, 1rem);
        }

        #beneficiaryDetailsRow .form-control {
            font-size: clamp(0.8rem, 1vw, 1rem);
            padding: 0.3rem 0.5rem;
        }

        #beneficiaryDetailsRow .card-header {
            font-size: clamp(1rem, 1.2vw, 1.2rem);
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-left">
                <strong>HEALTH MONITORING</strong>
            </div>
            <button class="btn btn-danger btn-md" id="exportPdfBtn">
                <i class="bi bi-file-earmark-pdf"></i> Export to PDF
            </button>
        </div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12 mb-2">
                    <!-- Combined Beneficiary Select and Time Range Filter -->
                    <div class="row mb-3 mt-1 justify-content-center">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <strong class="me-3">Filters</strong>
                                    </div>
                                    <button class="btn btn-primary btn-sm" id="applyFilterBtn">Apply Filters</button>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row g-2">
                                        <!-- Beneficiary Select -->
                                        <div class="col">
                                            <label for="beneficiarySelect" class="form-label">Select Beneficiary:</label>
                                            <select class="form-select" id="beneficiarySelect" name="beneficiary_id">
                                                <option value="">All Beneficiaries</option>
                                                <option value="1">Doe, John</option>
                                                <option value="2">Smith, Jane</option>
                                                <option value="3">Brown, Alice</option>
                                                <option value="4">Johnson, Bob</option>
                                            </select>
                                        </div>

                                        <!-- Municipalities Select -->
                                        <div class="col">
                                            <label for="municipalitySelect" class="form-label">Select Municipality:</label>
                                            <select class="form-select" id="municipalitySelect">
                                                <option value="">All Municipalities</option>
                                                <option value="Mondragon">Mondragon</option>
                                                <option value="San Roque">San Roque</option>
                                            </select>
                                        </div>

                                        <!-- Time Range Select -->
                                        <div class="col">
                                            <label for="timeRange" class="form-label">Time Range:</label>
                                            <select class="form-select" id="timeRange">
                                                <option value="weeks">Weeks</option>
                                                <option value="months">Months</option>
                                                <option value="year">Year</option>
                                            </select>
                                        </div>

                                        <!-- Week Filter (visible by default) -->
                                        <div class="col d-none" id="weekFilterContainer">
                                            <label for="monthSelect" class="form-label">Select Month:</label>
                                            <select class="form-select" id="monthSelect">
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>

                                        <!-- Month Range Filter (hidden by default) -->
                                        <div class="col d-none" id="monthRangeFilterContainer">
                                            <div class="row g-2">
                                                <div class="col">
                                                    <label for="startMonth" class="form-label">Start Month:</label>
                                                    <select class="form-select" id="startMonth">
                                                        <option value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <label for="endMonth" class="form-label">End Month:</label>
                                                    <select class="form-select" id="endMonth">
                                                        <option value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Year Filter (hidden by default) -->
                                        <div class="col d-none" id="yearFilterContainer">
                                            <label for="yearSelect" class="form-label">Select Year:</label>
                                            <select class="form-select" id="yearSelect">
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Beneficiary Details Row -->
                    <div class="row mb-3 d-none" id="beneficiaryDetailsRow">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <strong class="text-center d-block">Beneficiary Details</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row mb-1">
                                        <div class="col-md-4 col-sm-9 position-relative">
                                            <label for="benficiary" class="form-label">Beneficiary Name</label>
                                            <input type="text" class="form-control" id="beneficiary" value="" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>    
                                        </div>
                                        <div class="col-md-2 col-sm-3">
                                            <label for="age" class="form-label">Age</label>
                                            <input type="text" class="form-control" id="age" value="" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>                                            
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label for="birthDate" class="form-label">Birthdate</label>
                                            <input type="text" class="form-control" id="birthDate" value="" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>                                            
                                        </div>
                                        <div class="col-md-3 col-sm-6 position-relative">
                                            <label for="gender" class="form-label">Gender</label>
                                            <input type="text" class="form-control" id="gender" value="" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-3 col-sm-4 position-relative">
                                            <label for="civilStatus" class="form-label">Civil Status</label>
                                            <input type="text" class="form-control" id="civilStatus" value="" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>
                                            </div>
                                        <div class="col-md-6 col-sm-8">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" value="" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>
                                            </div>
                                        <div class="col-md-3 col-sm-12">
                                            <label for="category" class="form-label">Category</label>
                                            <input type="text" class="form-control" id="category" 
                                            value="" 
                                            readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
      
                    
                    <!-- Statistics Table Row -->
                    <div class="row mb-3" id="reportsTableRow">
                        <div class="col-12">
                            <div class="card shadow-sm" id="healthStatistics">
                                <div class="card-header">
                                    <strong class="text-center d-block">Health Statistics (All Beneficiaries)</strong>
                                </div>
                                <div class="card-body p-0 pb-1">
                                <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-center">Age 60-69</th>
                                            <th class="text-center">Age 70-79</th>
                                            <th class="text-center">Age 80-89</th>
                                            <th class="text-center">Age 90+</th>
                                            <th class="text-center">Male</th>
                                            <th class="text-center">Female</th>
                                            <th class="text-center">Single</th>
                                            <th class="text-center">Married</th>
                                            <th class="text-center">Widowed</th>
                                            <th class="text-center">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reportsTableBody">
                                        <tr>
                                            <td>Bedridden</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">27</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">12</td>
                                            <td class="text-center">15</td>
                                            <td class="text-center">5</td>
                                            <td class="text-center">15</td>
                                            <td class="text-center">7</td>
                                            <td class="text-center">13%</td>
                                        </tr>
                                        <tr>
                                            <td>Bedridden & Living Alone</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">12</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">5</td>
                                            <td class="text-center">7</td>
                                            <td class="text-center">3</td>
                                            <td class="text-center">6</td>
                                            <td class="text-center">3</td>
                                            <td class="text-center">6%</td>
                                        </tr>
                                        <tr>
                                            <td>Frail</td>
                                            <td class="text-center">45</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">20</td>
                                            <td class="text-center">25</td>
                                            <td class="text-center">10</td>
                                            <td class="text-center">20</td>
                                            <td class="text-center">15</td>
                                            <td class="text-center">22%</td>
                                        </tr>
                                        <tr>
                                            <td>Frail / PWD</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">18</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">8</td>
                                            <td class="text-center">10</td>
                                            <td class="text-center">4</td>
                                            <td class="text-center">8</td>
                                            <td class="text-center">6</td>
                                            <td class="text-center">9%</td>
                                        </tr>
                                        <tr>
                                            <td>Frail / PWD & Living Alone</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">7</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">3</td>
                                            <td class="text-center">4</td>
                                            <td class="text-center">2</td>
                                            <td class="text-center">3</td>
                                            <td class="text-center">2</td>
                                            <td class="text-center">3%</td>
                                        </tr>
                                        <tr>
                                            <td>Frail / Living Alone</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">14</td>
                                            <td class="text-center">6</td>
                                            <td class="text-center">8</td>
                                            <td class="text-center">3</td>
                                            <td class="text-center">7</td>
                                            <td class="text-center">4</td>
                                            <td class="text-center">7%</td>
                                        </tr>
                                        <tr>
                                            <td>Frail / Dementia</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">9</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">4</td>
                                            <td class="text-center">5</td>
                                            <td class="text-center">2</td>
                                            <td class="text-center">4</td>
                                            <td class="text-center">3</td>
                                            <td class="text-center">4%</td>
                                        </tr>
                                        <tr>
                                            <td>Dementia</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">22</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">10</td>
                                            <td class="text-center">12</td>
                                            <td class="text-center">5</td>
                                            <td class="text-center">10</td>
                                            <td class="text-center">7</td>
                                            <td class="text-center">11%</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total</strong></td>
                                            <td class="text-center"><strong>45</strong></td>
                                            <td class="text-center"><strong>54</strong></td>
                                            <td class="text-center"><strong>41</strong></td>
                                            <td class="text-center"><strong>14</strong></td>
                                            <td class="text-center"><strong>68</strong></td>
                                            <td class="text-center"><strong>86</strong></td>
                                            <td class="text-center"><strong>34</strong></td>
                                            <td class="text-center"><strong>73</strong></td>
                                            <td class="text-center"><strong>47</strong></td>
                                            <td class="text-center"><strong>100%</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Care Services Summary Section -->
                    <div class="row mt-3 mb-3" id="careServicesSummaryRow">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header text-center">
                                    <strong>Care Services Summary</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div id="careServicesCarousel" class="carousel slide" data-bs-interval="false">
                                        <div class="carousel-inner">
                                            <!-- Mobility Table -->
                                            <div class="carousel-item active">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2" class="text-center bg-light position-relative">
                                                                <button class="btn btn-sm btn-outline-secondary position-absolute start-0" data-bs-target="#careServicesCarousel" data-bs-slide="prev">
                                                                    <i class="bi bi-chevron-left"></i>
                                                                </button>
                                                                Mobility
                                                                <button class="btn btn-sm btn-outline-secondary position-absolute end-0" data-bs-target="#careServicesCarousel" data-bs-slide="next">
                                                                    <i class="bi bi-chevron-right"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th>Intervention Implemented</th>
                                                            <th class="text-center">Frequency</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Provided walker and physical therapy</td>
                                                            <td class="text-center">3x/week</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Installed stair lift</td>
                                                            <td class="text-center">Daily</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Caregiver-assisted transfers</td>
                                                            <td class="text-center">As needed</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Cognitive/Communication Table -->
                                            <div class="carousel-item">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2" class="text-center bg-light position-relative">
                                                                <button class="btn btn-sm btn-outline-secondary position-absolute start-0" data-bs-target="#careServicesCarousel" data-bs-slide="prev">
                                                                    <i class="bi bi-chevron-left"></i>
                                                                </button>
                                                                Cognitive / Communication
                                                                <button class="btn btn-sm btn-outline-secondary position-absolute end-0" data-bs-target="#careServicesCarousel" data-bs-slide="next">
                                                                    <i class="bi bi-chevron-right"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th>Intervention Implemented</th>
                                                            <th class="text-center">Frequency</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Memory exercises and reminders</td>
                                                            <td class="text-center">Daily</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Speech therapy sessions</td>
                                                            <td class="text-center">2x/week</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Self-sustainability Table -->
                                            <div class="carousel-item">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2" class="text-center bg-light position-relative">
                                                                <button class="btn btn-sm btn-outline-secondary position-absolute start-0" data-bs-target="#careServicesCarousel" data-bs-slide="prev">
                                                                    <i class="bi bi-chevron-left"></i>
                                                                </button>
                                                                Self-sustainability
                                                                <button class="btn btn-sm btn-outline-secondary position-absolute end-0" data-bs-target="#careServicesCarousel" data-bs-slide="next">
                                                                    <i class="bi bi-chevron-right"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th>Intervention Implemented</th>
                                                            <th class="text-center">Frequency</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Assistance with bathing and grooming</td>
                                                            <td class="text-center">Daily</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Meal delivery service</td>
                                                            <td class="text-center">3x/day</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row align-items-center justify-content-center">
                        <!-- Row 1 -->
                        <div class="row mb-3">
                            <!-- Blood Pressure Chart -->
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center">
                                        <strong>Blood Pressure</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="bloodPressureChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- Heart Rate Chart -->
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center">
                                        <strong>Heart Rate</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="heartRateChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2 -->
                        <div class="row mb-3">
                            <!-- Respiratory Rate Chart -->
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center">
                                        <strong>Respiratory Rate</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="respiratoryRateChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- Temperature Chart -->
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center">
                                        <strong>Temperature</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="temperatureChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 3 -->
                        <div class="row mb-3 justify-content-center" id="medicalConditionRow">
                            <!-- Medical Condition Pie Chart Section -->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header text-center">
                                            <strong>Medical Condition</strong>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="medicalConditionChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    // Chart variables
    let bloodPressureChart, heartRateChart, respiratoryRateChart, temperatureChart, medicalConditionChart;
    let currentBeneficiaryName = "All Beneficiaries";

    // Initialize charts
    function initCharts() {
        const weekLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];

        // Blood Pressure Chart
        const bloodPressureCtx = document.getElementById('bloodPressureChart').getContext('2d');
        bloodPressureChart = createChart(
            bloodPressureCtx,
            'Blood Pressure (mmHg)',
            weekLabels,
            [120, 125, 130, 128],
            'rgba(255, 99, 132, 1)',
            'rgba(255, 99, 132, 0.2)'
        );

        // Heart Rate Chart
        const heartRateCtx = document.getElementById('heartRateChart').getContext('2d');
        heartRateChart = createChart(
            heartRateCtx,
            'Heart Rate (bpm)',
            weekLabels,
            [72, 75, 78, 76],
            'rgba(54, 162, 235, 1)',
            'rgba(54, 162, 235, 0.2)'
        );

        // Respiratory Rate Chart
        const respiratoryRateCtx = document.getElementById('respiratoryRateChart').getContext('2d');
        respiratoryRateChart = createChart(
            respiratoryRateCtx,
            'Respiratory Rate (breaths/min)',
            weekLabels,
            [16, 18, 17, 16],
            'rgba(255, 206, 86, 1)',
            'rgba(255, 206, 86, 0.2)'
        );

        // Temperature Chart
        const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
        temperatureChart = createChart(
            temperatureCtx,
            'Temperature (°C)',
            weekLabels,
            [36.5, 36.7, 36.8, 36.6],
            'rgba(75, 192, 192, 1)',
            'rgba(75, 192, 192, 0.2)'
        );

        // Medical Condition Pie Chart
        initMedicalConditionChart();
    }

    // Create a chart
    function createChart(ctx, label, labels, data, borderColor, backgroundColor) {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: borderColor,
                    backgroundColor: backgroundColor,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }

    // Initialize the Medical Condition Pie Chart
    function initMedicalConditionChart() {
        const ctx = document.getElementById('medicalConditionChart').getContext('2d');
        medicalConditionChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Frail', 'Bedridden', 'Disabled', 'Chronic Illness'],
                datasets: [{
                    data: [40, 30, 20, 10], // Example data
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }

    // Show or hide the Medical Condition Pie Chart based on the selected beneficiary
    function toggleMedicalConditionChart() {
        const medicalConditionRow = document.getElementById('medicalConditionRow');
        const beneficiaryId = document.getElementById('beneficiarySelect').value;

        if (beneficiaryId === '') {
            medicalConditionRow.classList.remove('d-none');
        } else {
            medicalConditionRow.classList.add('d-none');
        }
    }

    // Ensure this function is called whenever the beneficiary dropdown changes
    document.getElementById('beneficiarySelect').addEventListener('change', toggleMedicalConditionChart);

    // Update card headers with beneficiary name
    function updateCardHeaders() {
        const beneficiaryText = currentBeneficiaryName === "All Beneficiaries" ? "" : ` (${currentBeneficiaryName})`;
        
        // Update chart headers
        document.querySelectorAll('.card-header strong').forEach(header => {
            if (header.textContent.includes('Blood Pressure') || 
                header.textContent.includes('Heart Rate') || 
                header.textContent.includes('Respiratory Rate') || 
                header.textContent.includes('Temperature')) {
                header.textContent = header.textContent.replace(/\(.*\)/, '') + beneficiaryText;
            }
        });
        
        // Update Care Services Summary header
        const careServicesHeader = document.querySelector('#careServicesSummaryRow .card-header strong');
        if (careServicesHeader) {
            careServicesHeader.textContent = 'Care Services Summary' + beneficiaryText;
        }
        
        // Update Health Statistics header
        const healthStatsHeader = document.querySelector('#reportsTableRow .card-header strong');
        if (healthStatsHeader) {
            healthStatsHeader.textContent = currentBeneficiaryName === "All Beneficiaries" 
                ? 'Health Statistics (All Beneficiaries)' 
                : `Health Statistics (${currentBeneficiaryName})`;
        }
    }

    // Toggle Beneficiary Details and related components
    function toggleBeneficiaryDetails() {
        const beneficiaryId = document.getElementById('beneficiarySelect').value;
        const beneficiarySelect = document.getElementById('beneficiarySelect');
        const detailsRow = document.getElementById('beneficiaryDetailsRow');
        const healthStatistics = document.getElementById('healthStatistics');

        // Get selected beneficiary name
        currentBeneficiaryName = beneficiaryId ? 
            beneficiarySelect.options[beneficiarySelect.selectedIndex].text : 
            "All Beneficiaries";

        if (beneficiaryId) {
            // Show Beneficiary Details and hide Health Statistics
            detailsRow.classList.remove('d-none');
            healthStatistics.classList.add('d-none');
        } else {
            // Show Health Statistics and hide Beneficiary Details
            detailsRow.classList.add('d-none');
            healthStatistics.classList.remove('d-none');
        }
        
        // Update card headers with beneficiary name
        updateCardHeaders();
        toggleMedicalConditionChart(); // Show or hide the pie chart
    }

    // Apply filters and update all content
    function applyFilters() {
        const beneficiaryId = document.getElementById('beneficiarySelect').value;

        // First update beneficiary details if needed
        toggleBeneficiaryDetails();

        // Update Medical Condition Chart visibility
        toggleMedicalConditionChart();
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function () {
        initCharts();
        
        // Set "All Beneficiaries" as default selected
        document.getElementById('beneficiarySelect').value = '';
        
        // Event listeners
        document.getElementById('beneficiarySelect').addEventListener('change', toggleBeneficiaryDetails);
        document.getElementById('applyFilterBtn').addEventListener('click', applyFilters);

        // Initial setup
        applyFilters(); // This will show all content with default filters
    });
</script>

</body>
</html>