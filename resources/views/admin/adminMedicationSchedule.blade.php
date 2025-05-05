<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Schedule</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --font-size-min: 0.9rem;
            --font-size-max: 1.1rem;
            --primary-color: #4a6fa5;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Enhanced search box */
        .search-box {
            position: relative;
            max-width: 100%;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }
        
        .search-input {
            padding-left: 45px;
            border-radius: 50px;
            border: 1px solid #ced4da;
            height: 45px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(74, 111, 165, 0.25);
        }
        
        /* Improved table styling */
        .table-responsive {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            background-color: white;
            overflow: hidden;
        }
        
        .table thead {
            background-color: var(--primary-color);
            color: white;
        }
        
        .table th {
            font-weight: 500;
            padding: 15px;
            vertical-align: middle;
        }
        
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(74, 111, 165, 0.05);
        }
        
        /* Enhanced badge styling */
        .badge-time {
            background-color: #e9f7fe;
            color: #31708f;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: normal;
            margin-right: 8px;
            margin-bottom: 5px;
            display: inline-block;
            font-size: 0.85rem;
        }
        
        /* Improved action buttons */
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
        
        /* Darker border between beneficiaries */
        .beneficiary-divider {
            border-bottom: 2px solid #e0e0e0 !important;
        }
        
        /* Medical info styling */
        .medical-info-cell div {
            margin-bottom: 5px;
        }
        
        .medical-info-cell strong {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        /* Medication name styling */
        .medication-info {
            font-weight: 500;
        }
        
        .medication-info .text-muted {
            font-weight: normal;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {            
            .search-box {
                max-width: 100%;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
        }
        
        /* Modal improvements */
        .modal-xl {
            max-width: 850px;
        }
        
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }
        
        .modal-title {
            font-weight: 500;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        @media (max-width: 576px) {
            .header-container {
                flex-wrap: nowrap; /* Prevent wrapping on very small screens */
            }
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="container-fluid">
            <div class="header-container d-flex justify-content-between align-items-center flex-wrap">
                <!-- Title -->
                <div class="text-left">MEDICATION SCHEDULE</div>

                <!-- Add Schedule Button -->
                <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                    <i class="bi bi-plus-lg me-2"></i>
                    <span class="d-none d-sm-inline">Add Schedule</span>
                    <span class="d-inline d-sm-none">Add</span>
                </button>
            </div>
            <div class="row pe-1 ps-1" id="home-content">
                <div class="col-12">
                    <!-- Search Box -->
                    <div class="search-box mb-3">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control search-input" placeholder="Search beneficiaries or medications...">
                    </div>

                    <!-- Medication Schedule Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Beneficiary</th>
                                    <th>Medical Information</th>
                                    <th>Medication</th>
                                    <th>Schedule</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Beneficiary 1 -->
                                <tr class="beneficiary-divider">
                                    <td rowspan="3" class="align-middle fw-semibold">John Doe</td>
                                    <td rowspan="3" class="align-middle medical-info-cell">
                                        <div><strong>Condition:</strong> Type 2 Diabetes</div>
                                        <div><strong>Illness:</strong> Hypertension</div>
                                    </td>
                                    <td class="medication-info">Metformin <span class="text-muted">(500mg)</span></td>
                                    <td>
                                        <span class="badge-time">8:00 AM</span>
                                        <span class="badge-time">6:00 PM</span>
                                    </td>
                                    <td rowspan="3" class="align-middle">
                                        <button class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="medication-info">Lisinopril <span class="text-muted">(10mg)</span></td>
                                    <td><span class="badge-time">8:00 AM</span></td>
                                </tr>
                                <tr class="beneficiary-divider">
                                    <td class="medication-info">Atorvastatin <span class="text-muted">(20mg)</span></td>
                                    <td><span class="badge-time">9:00 PM</span></td>
                                </tr>

                                <!-- Beneficiary 2 -->
                                <tr>
                                    <td rowspan="2" class="align-middle fw-semibold">Mary Smith</td>
                                    <td rowspan="2" class="align-middle medical-info-cell">
                                        <div><strong>Condition:</strong> Hypothyroidism</div>
                                        <div><strong>Illness:</strong> Asthma</div>
                                    </td>
                                    <td class="medication-info">Levothyroxine <span class="text-muted">(50mcg)</span></td>
                                    <td><span class="badge-time">7:00 AM</span></td>
                                    <td rowspan="2" class="align-middle">
                                        <button class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr class="beneficiary-divider">
                                    <td class="medication-info">Albuterol Inhaler</td>
                                    <td><span class="badge-time">As needed</span></td>
                                </tr>

                                <!-- Beneficiary 3 -->
                                <tr>
                                    <td rowspan="2" class="align-middle fw-semibold">Robert Johnson</td>
                                    <td rowspan="2" class="align-middle medical-info-cell">
                                        <div><strong>Condition:</strong> Heart Disease</div>
                                        <div><strong>Illness:</strong> Edema</div>
                                    </td>
                                    <td class="medication-info">Warfarin <span class="text-muted">(5mg)</span></td>
                                    <td><span class="badge-time">6:00 PM</span></td>
                                    <td rowspan="2" class="align-middle">
                                        <button class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr class="beneficiary-divider">
                                    <td class="medication-info">Furosemide <span class="text-muted">(40mg)</span></td>
                                    <td>
                                        <span class="badge-time">8:00 AM</span>
                                        <span class="badge-time">2:00 PM</span>
                                    </td>
                                </tr>

                                <!-- Beneficiary 4 -->
                                <tr>
                                    <td rowspan="3" class="align-middle fw-semibold">Sarah Williams</td>
                                    <td rowspan="3" class="align-middle medical-info-cell">
                                        <div><strong>Condition:</strong> Type 1 Diabetes</div>
                                        <div><strong>Illness:</strong> High Blood Pressure</div>
                                    </td>
                                    <td class="medication-info">Insulin Glargine</td>
                                    <td><span class="badge-time">9:00 PM</span></td>
                                    <td rowspan="3" class="align-middle">
                                        <button class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="medication-info">Metoprolol <span class="text-muted">(25mg)</span></td>
                                    <td>
                                        <span class="badge-time">7:00 AM</span>
                                        <span class="badge-time">7:00 PM</span>
                                    </td>
                                </tr>
                                <tr class="beneficiary-divider">
                                    <td class="medication-info">Aspirin <span class="text-muted">(81mg)</span></td>
                                    <td><span class="badge-time">8:00 AM</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addScheduleModalLabel">Add New Medication Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="beneficiarySelect" class="form-label">Beneficiary</label>
                                <select class="form-select" id="beneficiarySelect">
                                    <option value="" selected disabled>Select beneficiary</option>
                                    <option value="1">John Doe</option>
                                    <option value="2">Mary Smith</option>
                                    <option value="3">Robert Johnson</option>
                                    <option value="4">Sarah Williams</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="medicalCondition" class="form-label">Medical Condition</label>
                                <input type="text" class="form-control" id="medicalCondition" placeholder="e.g., Type 2 Diabetes" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="illness" class="form-label">Illness</label>
                                <input type="text" class="form-control" id="illness" placeholder="e.g., Hypertension" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="medicationName" class="form-label">Medication Name</label>
                                <input type="text" class="form-control" id="medicationName" placeholder="e.g., Metformin">
                            </div>
                            <div class="col-md-4">
                                <label for="medicationDosage" class="form-label">Dosage</label>
                                <input type="text" class="form-control" id="medicationDosage" placeholder="e.g., 500mg">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Schedule Times</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" id="morningCheck">
                                    <label class="form-check-label d-flex align-items-center gap-2" for="morningCheck">
                                        Morning
                                        <input type="time" class="form-control form-control-sm" value="08:00" style="width: 100px;">
                                    </label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" id="afternoonCheck">
                                    <label class="form-check-label d-flex align-items-center gap-2" for="afternoonCheck">
                                        Afternoon
                                        <input type="time" class="form-control form-control-sm" value="12:00" style="width: 100px;">
                                    </label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" id="eveningCheck">
                                    <label class="form-check-label d-flex align-items-center gap-2" for="eveningCheck">
                                        Evening
                                        <input type="time" class="form-control form-control-sm" value="18:00" style="width: 100px;">
                                    </label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="checkbox" id="nightCheck">
                                    <label class="form-check-label d-flex align-items-center gap-2" for="nightCheck">
                                        Night
                                        <input type="time" class="form-control form-control-sm" value="21:00" style="width: 100px;">
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="asNeededCheck">
                                    <label class="form-check-label" for="asNeededCheck">
                                        As needed
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="specialInstructions" class="form-label">Special Instructions</label>
                            <textarea class="form-control" id="specialInstructions" rows="3" placeholder="Enter any special instructions..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save Schedule</button>
                </div>
            </div>
        </div>
    </div>
   
    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // This would be replaced with actual data fetching in a real application
        document.getElementById('beneficiarySelect').addEventListener('change', function() {
            const beneficiaryData = {
                '1': { condition: 'Type 2 Diabetes', illness: 'Hypertension' },
                '2': { condition: 'Hypothyroidism', illness: 'Asthma' },
                '3': { condition: 'Heart Disease', illness: 'Edema' },
                '4': { condition: 'Type 1 Diabetes', illness: 'High Blood Pressure' }
            };
            
            const selectedValue = this.value;
            if (selectedValue && beneficiaryData[selectedValue]) {
                document.getElementById('medicalCondition').value = beneficiaryData[selectedValue].condition;
                document.getElementById('illness').value = beneficiaryData[selectedValue].illness;
            } else {
                document.getElementById('medicalCondition').value = '';
                document.getElementById('illness').value = '';
            }
        });
    </script>
</body>
</html>