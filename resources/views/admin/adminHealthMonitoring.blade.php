<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">

    <style>
        tbody .btn-link{
            color: black;
            text-decoration: none;
            font-size: 20px;
        }
        tbody td{
            vertical-align: middle;
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
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Beneficiary Select -->
                                            <div class="col-md-5 mb-1">
                                                <label for="beneficiarySelect" class="form-label">Select Beneficiary:</label>
                                                <select class="form-select" id="beneficiarySelect" name="beneficiary_id">
                                                    <option value="">All Beneficiaries</option>
                                                    <option value="1">Doe, John</option>
                                                    <option value="2">Smith, Jane</option>
                                                    <option value="3">Brown, Alice</option>
                                                    <option value="4">Johnson, Bob</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Time Range Select -->
                                            <div class="col-md-2 mb-1">
                                                <label for="timeRange" class="form-label">Time Range:</label>
                                                <select class="form-select" id="timeRange">
                                                    <option value="weeks">Weeks</option>
                                                    <option value="months">Months</option>
                                                    <option value="year">Year</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Week Filter (visible by default) -->
                                            <div class="col-md-5 mb-1" id="weekFilterContainer">
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
                                            <div class="col-md-5 mb-1 d-none" id="monthRangeFilterContainer">
                                                <div class="row">
                                                    <div class="col-md-6">
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
                                                    <div class="col-md-6">
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
                                            <div class="col-md-5 mb-1 d-none" id="yearFilterContainer">
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
                        <div class="row mb-3" id="beneficiaryDetailsRow">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong class="text-center d-block">Beneficiary Details</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="row text-md-start">
                                            <div class="col-md-2 col-sm-6 mb-1">
                                                <p><strong>Age:</strong> <span id="beneficiaryAge">65</span></p>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-1">
                                                <p><strong>Address:</strong> <span id="beneficiaryAddress">123 Main St, City</span></p>
                                            </div>
                                            <div class="col-md-2 col-sm-6 mb-1">
                                                <p><strong>Gender:</strong> <span id="beneficiaryGender">Male</span></p>
                                            </div>
                                            <div class="col-md-2 col-sm-6 mb-1">
                                                <p><strong>Civil Status:</strong> <span id="beneficiaryCivilStatus">Married</span></p>
                                            </div>
                                            <div class="col-md-3 col-sm-6 mb-1">
                                                <p><strong>Category:</strong> <span id="beneficiaryCategory">Frail</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistics Table Row -->
                        <div class="row mb-3" id="reportsTableRow">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <strong class="text-center d-block">Health Statistics</strong>
                                    </div>
                                    <div class="card-body p-0 pb-1">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th class="text-center">Male</th>
                                                        <th class="text-center">Female</th>
                                                        <th class="text-center">Total</th>
                                                        <th class="text-center">Percentage</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="reportsTableBody">
                                                    <tr>
                                                        <td>Bedridden</td>
                                                        <td class="text-center">12</td>
                                                        <td class="text-center">15</td>
                                                        <td class="text-center">27</td>
                                                        <td class="text-center">13%</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bedridden & Living Alone</td>
                                                        <td class="text-center">5</td>
                                                        <td class="text-center">7</td>
                                                        <td class="text-center">12</td>
                                                        <td class="text-center">6%</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Frail</td>
                                                        <td class="text-center">20</td>
                                                        <td class="text-center">25</td>
                                                        <td class="text-center">45</td>
                                                        <td class="text-center">22%</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Frail / PWD</td>
                                                        <td class="text-center">8</td>
                                                        <td class="text-center">10</td>
                                                        <td class="text-center">18</td>
                                                        <td class="text-center">9%</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Frail / PWD & Living Alone</td>
                                                        <td class="text-center">3</td>
                                                        <td class="text-center">4</td>
                                                        <td class="text-center">7</td>
                                                        <td class="text-center">3%</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Frail / Living Alone</td>
                                                        <td class="text-center">6</td>
                                                        <td class="text-center">8</td>
                                                        <td class="text-center">14</td>
                                                        <td class="text-center">7%</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Frail / Dementia</td>
                                                        <td class="text-center">4</td>
                                                        <td class="text-center">5</td>
                                                        <td class="text-center">9</td>
                                                        <td class="text-center">4%</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Dementia</td>
                                                        <td class="text-center">10</td>
                                                        <td class="text-center">12</td>
                                                        <td class="text-center">22</td>
                                                        <td class="text-center">11%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Total</strong></td>
                                                        <td class="text-center"><strong>68</strong></td>
                                                        <td class="text-center"><strong>86</strong></td>
                                                        <td class="text-center"><strong>154</strong></td>
                                                        <td class="text-center"><strong>100%</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                            <div class="row">
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
        let bloodPressureChart, heartRateChart, respiratoryRateChart, temperatureChart;

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
                'rgba(255, 99, 132, 1)', // Border color
                'rgba(255, 99, 132, 0.2)' // Background color
            );

            // Heart Rate Chart
            const heartRateCtx = document.getElementById('heartRateChart').getContext('2d');
            heartRateChart = createChart(
                heartRateCtx,
                'Heart Rate (bpm)',
                weekLabels,
                [72, 75, 78, 76],
                'rgba(54, 162, 235, 1)', // Border color
                'rgba(54, 162, 235, 0.2)' // Background color
            );

            // Respiratory Rate Chart
            const respiratoryRateCtx = document.getElementById('respiratoryRateChart').getContext('2d');
            respiratoryRateChart = createChart(
                respiratoryRateCtx,
                'Respiratory Rate (breaths/min)',
                weekLabels,
                [16, 18, 17, 16],
                'rgba(255, 206, 86, 1)', // Border color
                'rgba(255, 206, 86, 0.2)' // Background color
            );

            // Temperature Chart
            const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
            temperatureChart = createChart(
                temperatureCtx,
                'Temperature (°C)',
                weekLabels,
                [36.5, 36.7, 36.8, 36.6],
                'rgba(75, 192, 192, 1)', // Border color
                'rgba(75, 192, 192, 0.2)' // Background color
            );
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

        // Toggle Beneficiary Details and Health Reports
        function toggleBeneficiaryDetails() {
            const beneficiaryId = document.getElementById('beneficiarySelect').value;
            const detailsRow = document.getElementById('beneficiaryDetailsRow');
            const reportsRow = document.getElementById('reportsTableRow');

            if (beneficiaryId) {
                // Show Beneficiary Details and hide Health Reports
                detailsRow.classList.remove('d-none');
                reportsRow.classList.add('d-none');

                // Update Beneficiary Details with sample data
                document.getElementById('beneficiaryAge').textContent = Math.floor(Math.random() * 30) + 50;
                document.getElementById('beneficiaryGender').textContent = beneficiaryId % 2 === 0 ? 'Female' : 'Male';
                document.getElementById('beneficiaryCivilStatus').textContent =
                    ['Single', 'Married', 'Widowed', 'Separated'][Math.floor(Math.random() * 4)];
                document.getElementById('beneficiaryCategory').textContent =
                    ['Frail', 'Bedridden', 'Disabled', 'Chronic Illness'][Math.floor(Math.random() * 4)];
            } else {
                // Show Health Reports and hide Beneficiary Details
                detailsRow.classList.add('d-none');
                reportsRow.classList.remove('d-none');
            }
        }

        // Update time range filter visibility
        function updateTimeFilters() {
            const timeRange = document.getElementById('timeRange').value;

            // Hide all filters first
            document.getElementById('weekFilterContainer').classList.add('d-none');
            document.getElementById('monthRangeFilterContainer').classList.add('d-none');
            document.getElementById('yearFilterContainer').classList.add('d-none');

            // Show the appropriate filter
            if (timeRange === 'weeks') {
                document.getElementById('weekFilterContainer').classList.remove('d-none');
            } else if (timeRange === 'months') {
                document.getElementById('monthRangeFilterContainer').classList.remove('d-none');
            } else if (timeRange === 'year') {
                document.getElementById('yearFilterContainer').classList.remove('d-none');
            }
        }

        // Apply filters and update charts
        function applyFilters() {
            const timeRange = document.getElementById('timeRange').value;
            let labels, bpData, hrData, rrData, tempData;

            if (timeRange === 'weeks') {
                const month = document.getElementById('monthSelect').value;
                labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                bpData = [120, 125, 130, 128];
                hrData = [72, 75, 78, 76];
                rrData = [16, 18, 17, 16];
                tempData = [36.5, 36.7, 36.8, 36.6];
            } else if (timeRange === 'months') {
                const startMonth = parseInt(document.getElementById('startMonth').value);
                const endMonth = parseInt(document.getElementById('endMonth').value);
                labels = Array.from({ length: endMonth - startMonth + 1 }, (_, i) => `Month ${startMonth + i}`);
                bpData = labels.map(() => Math.random() * 20 + 110);
                hrData = labels.map(() => Math.random() * 10 + 70);
                rrData = labels.map(() => Math.random() * 5 + 15);
                tempData = labels.map(() => Math.random() * 1 + 36);
            } else if (timeRange === 'year') {
                const year = document.getElementById('yearSelect').value;
                labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                bpData = labels.map(() => Math.random() * 20 + 110);
                hrData = labels.map(() => Math.random() * 10 + 70);
                rrData = labels.map(() => Math.random() * 5 + 15);
                tempData = labels.map(() => Math.random() * 1 + 36);
            }

            // Update all charts
            updateChart(bloodPressureChart, labels, bpData);
            updateChart(heartRateChart, labels, hrData);
            updateChart(respiratoryRateChart, labels, rrData);
            updateChart(temperatureChart, labels, tempData);
        }

        // Update a specific chart
        function updateChart(chart, labels, data) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.update();
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function () {
            initCharts();

            // Event listeners
            document.getElementById('beneficiarySelect').addEventListener('change', toggleBeneficiaryDetails);
            document.getElementById('timeRange').addEventListener('change', updateTimeFilters);
            document.getElementById('applyFilterBtn').addEventListener('click', applyFilters);

            // Initial setup
            toggleBeneficiaryDetails();
            updateTimeFilters();
        });
    </script>

</body>
</html>