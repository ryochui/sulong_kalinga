<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Worker Scheduling</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        /* Card Design */
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            border: 1px solid rgba(0,0,0,0.07);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: 0.8rem 1.25rem;
            background-color: #f8f9fc;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .section-heading {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 0;
        }
        
        /* Calendar Enhancements */
        #calendar-container {
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding: 1rem;
            margin-bottom: 1.5rem;
            overflow-x: auto; /* Enable horizontal scrolling */
        }
        
        #calendar {
            background-color: white;
            min-height: 600px;
            min-width: 800px; /* Set minimum width to ensure functionality */
        }
        
        /* Event Styling */
        .fc-event {
            cursor: pointer;
            border: none !important;
            padding: 4px 6px;
            margin-bottom: 2px;
            border-radius: 6px;
        }
        
        .fc-event.open-time {
            border-left: 4px solid rgb(201, 20, 59) !important;
            background-color: #dc3545 !important;
            color: #333;
        }
        
        .fc-event-main {
            display: flex;
            flex-direction: column;
            padding: 4px 0;
        }
        
        .event-worker {
            font-weight: 600;
            font-size: 0.85rem;
            white-space: normal !important;
        }
        
        .event-details {
            font-size: 0.75rem;
            line-height: 1.3;
            white-space: normal !important;
        }
        
        .fc-daygrid-event-dot {
            display: none; /* Hide default event dots */
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.6rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
        }
        
        .action-btn i {
            margin-right: 8px;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Search Bar Enhancements */
        .search-container {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .search-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            transition: opacity 0.2s ease;
        }
        
        .search-container .search-input:focus + i,
        .search-container .search-input:not(:placeholder-shown) + i {
            opacity: 0;
        }
        
        .search-input {
            padding-left: 35px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        /* Fix for search placeholder */
        .search-input::placeholder {
            color: #a0a5aa;
        }
        
        /* Appointment Details Panel */
        .details-container {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .details-header {
            padding: 0.8rem 1.25rem;
            background-color: #4e73df;
            color: white;
        }
        
        .details-body {
            padding: 1rem;
            background-color: white;
            border: 1px solid rgba(0,0,0,0.07);
            border-top: none;
        }
        
        .detail-section {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.07);
        }
        
        .detail-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section-title {
            font-weight: 600;
            color: #4e73df;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 8px;
        }
        
        .detail-item {
            margin-bottom: 0.4rem;
            display: flex;
        }
        
        .detail-label {
            font-weight: 500;
            width: 80px;
            font-size: 0.85rem;
            color: #666;
        }
        
        .detail-value {
            font-size: 0.85rem;
            flex: 1;
        }
        
        /* Open time indicator */
        .open-time-indicator {
            display: inline-flex;
            align-items: center;
            font-size: 0.7rem;
            font-weight: 500;
            margin-top: 2px;
        }
        
        .open-time-indicator i {
            margin-right: 3px;
        }
        
        /* Dropdown select styling */
        .select-container {
            position: relative;
        }
        
        .select-container::after {
            content: "";
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #666;
            pointer-events: none;
        }
        
        .select-container select {
            padding-right: 25px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        
        /* Modal Enhancements */
        .modal-content {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .modal-header {
            background-color: #4e73df;
            color: white;
            padding: 1rem 1.5rem;
            border-bottom: none;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        /* Form Enhancements */
        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
            color: #495057;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
        }
        
        .form-control {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        /* Day Checkboxes */
        .day-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        
        .day-checkbox {
            flex: 0 0 calc(25% - 8px);
            position: relative;
        }
        
        .day-checkbox input {
            position: absolute;
            opacity: 0;
        }
        
        .day-checkbox label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 4px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.8rem;
            width: 100%;
            text-align: center;
        }
        
        .day-checkbox input:checked + label {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }
        
        /* Time Input Group */
        .time-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .time-input {
            flex: 1;
        }
        
        .time-separator {
            font-weight: bold;
            color: #495057;
        }
        
        /* Open Time checkbox */
        .open-time-container {
            margin-top: 0.5rem;
            padding-top: 0.75rem;
            border-top: 1px solid #dee2e6;
        }
        
        /* Action Buttons in Modal */
        .modal-action-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .btn-schedule {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        
        .btn-schedule:hover {
            background-color: #3a5fc8;
            border-color: #3a5fc8;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 767.98px) {
            .day-checkbox {
                flex: 0 0 calc(33.333% - 8px);
            }
            
            .fc-toolbar-title {
                font-size: 1.1rem !important;
            }
            
            .fc-button {
                font-size: 0.8rem;
                padding: 0.3rem 0.5rem;
            }
            
            .action-btn {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .day-checkbox {
                flex: 0 0 calc(50% - 8px);
            }
            
            .detail-label {
                width: 70px;
            }
        }
        
        /* FullCalendar Customizations */
        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.2rem;
        }
        
        .fc .fc-button-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        
        .fc .fc-button-primary:hover {
            background-color: #3a5fc8;
            border-color: #3a5fc8;
        }
        
        .fc .fc-button-primary:disabled {
            background-color: #6c8ae4;
            border-color: #6c8ae4;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active, 
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #3a5fc8;
            border-color: #3a5fc8;
        }
        
        .fc-daygrid-day-number {
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .fc-col-header-cell-cushion {
            font-weight: 600;
        }
        
        /* Open Time / All Day Events */
        .fc-daygrid-block-event .fc-event-time {
            font-weight: 600;
        }
        
        /* Tooltip Styling */
        .tooltip-inner {
            max-width: 300px;
            padding: 10px 12px;
            text-align: left;
            background-color: #343a40;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">CARE WORKER SCHEDULING</div>
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <!-- Main content area -->
                <div class="col-12 mb-4">
                    <div class="row">
                        <!-- Calendar Column -->
                        <div class="col-lg-8 col-md-7 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-heading">
                                        <i class="bi bi-calendar3"></i> Appointment Calendar
                                    </h5>
                                    <div class="calendar-actions d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleWeekView">
                                            <i class="bi bi-calendar-week"></i> Week View
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="calendar-container">
                                        <div id="calendar" class="p-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Details Column -->
                        <div class="col-lg-4 col-md-5">
                            <!-- Search Bar -->
                            <div class="search-container">
                                <input type="text" id="searchInput" class="form-control search-input" placeholder="     Search appointments..." aria-label="Search appointments">
                                <i class="bi bi-search"></i>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="action-buttons mb-4">
                                <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                    <i class="bi bi-plus-circle"></i> Schedule New Appointment
                                </button>
                                <button type="button" class="btn btn-outline-warning action-btn" id="editAppointmentButton" disabled>
                                    <i class="bi bi-pencil-square"></i> Edit Selected Appointment
                                </button>
                                <button type="button" class="btn btn-outline-danger action-btn" id="deleteAppointmentButton" disabled>
                                    <i class="bi bi-trash3"></i> Cancel Selected Appointment
                                </button>
                            </div>
                            
                            <!-- Appointment Details Panel -->
                            <div class="card details-container">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-heading mb-0">
                                        <i class="bi bi-info-circle"></i> Appointment Details
                                    </h5>
                                </div>
                                <div class="card-body" id="appointmentDetails">
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <p class="mt-3 mb-0">Select an appointment to view details</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalLabel">
                        <i class="bi bi-calendar-plus"></i> Schedule New Appointment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrors" class="alert alert-danger d-none mb-3">
                        <ul id="errorList" class="mb-0"></ul>
                    </div>
                    
                    <form id="addAppointmentForm">
                        @csrf
                        <input type="hidden" id="visitationId" name="visitation_id">
                        
                        <!-- Care Worker Selection -->
                        <div class="form-group">
                            <label for="careWorkerSelect">
                                <i class="bi bi-person-badge"></i> Care Worker
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="careWorkerSelect" name="care_worker_id" required>
                                    <option value="">Select Care Worker</option>
                                    @foreach($careWorkers as $worker)
                                        <option value="{{ $worker->id }}">{{ $worker->first_name }} {{ $worker->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-feedback" id="care-worker-error"></div>
                        </div>
                        
                        <!-- Beneficiary Selection -->
                        <div class="form-group">
                            <label for="beneficiarySelect">
                                <i class="bi bi-person-heart"></i> Beneficiary
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="beneficiarySelect" name="beneficiary_id" required>
                                    <option value="">Select Beneficiary</option>
                                </select>
                            </div>
                            <div class="error-feedback" id="beneficiary-error"></div>
                        </div>
                        
                        <!-- Beneficiary Details (Auto-filled) -->
                        <div class="form-group">
                            <label for="beneficiaryAddress">
                                <i class="bi bi-geo-alt"></i> Address
                            </label>
                            <input type="text" class="form-control" id="beneficiaryAddress" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="beneficiaryPhone">
                                <i class="bi bi-telephone"></i> Phone
                            </label>
                            <input type="text" class="form-control" id="beneficiaryPhone" readonly>
                        </div>
                        
                        <!-- Visit Date -->
                        <div class="form-group">
                            <label for="visitDate">
                                <i class="bi bi-calendar-date"></i> Visit Date
                            </label>
                            <input type="date" class="form-control" id="visitDate" name="visitation_date" required>
                            <div class="error-feedback" id="visitation-date-error"></div>
                        </div>
                        
                        <!-- Time Selection -->
                        <div class="form-group">
                            <label>
                                <i class="bi bi-clock"></i> Appointment Time
                            </label>
                            <div class="row" id="timeSelectionContainer">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="startTime" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="startTime" name="start_time">
                                        <div class="error-feedback" id="start-time-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="endTime" class="form-label">End Time</label>
                                        <input type="time" class="form-control" id="endTime" name="end_time">
                                        <div class="error-feedback" id="end-time-error"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Open Time / Flexible Schedule Option -->
                            <div class="open-time-container">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="openTimeCheck" name="is_flexible_time">
                                    <label class="form-check-label" for="openTimeCheck">
                                        Open Time / Flexible Schedule (Care Worker will determine actual time)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Visit Type -->
                        <div class="form-group">
                            <label for="visitType">
                                <i class="bi bi-clipboard2-pulse"></i> Visit Type
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="visitType" name="visit_type" required>
                                    <option value="">Select Visit Type</option>
                                    <option value="routine_care_visit">Routine Care Visit</option>
                                    <option value="service_request">Service Request</option>
                                    <option value="emergency_visit">Emergency Visit</option>
                                </select>
                            </div>
                            <div class="error-feedback" id="visit-type-error"></div>
                        </div>
                        
                        <!-- Recurring Options -->
                        <div class="form-check mt-3 mb-3">
                            <input class="form-check-input" type="checkbox" id="recurringCheck" name="is_recurring">
                            <label class="form-check-label" for="recurringCheck">
                                Make this a recurring appointment
                            </label>
                        </div>
                        
                        <div id="recurringOptionsContainer" class="border rounded p-3 mb-3" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Recurrence Pattern</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternDaily" value="daily">
                                    <label class="form-check-label" for="patternDaily">Daily</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternWeekly" value="weekly" checked>
                                    <label class="form-check-label" for="patternWeekly">Weekly</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternMonthly" value="monthly">
                                    <label class="form-check-label" for="patternMonthly">Monthly</label>
                                </div>
                            </div>
                            
                            <div id="weeklyOptions" class="mt-3">
                                <label class="form-label">Repeat on</label>
                                <div class="day-checkboxes">
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="daySun" name="day_of_week[]" value="0">
                                        <label for="daySun">Sun</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayMon" name="day_of_week[]" value="1">
                                        <label for="dayMon">Mon</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayTue" name="day_of_week[]" value="2">
                                        <label for="dayTue">Tue</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayWed" name="day_of_week[]" value="3">
                                        <label for="dayWed">Wed</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayThu" name="day_of_week[]" value="4">
                                        <label for="dayThu">Thu</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayFri" name="day_of_week[]" value="5">
                                        <label for="dayFri">Fri</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="daySat" name="day_of_week[]" value="6">
                                        <label for="daySat">Sat</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-3">
                                <label for="recurrenceEnd" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="recurrenceEnd" name="recurrence_end">
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="form-group mb-0">
                            <label for="notes">
                                <i class="bi bi-journal-text"></i> Visit Notes
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any additional instructions or notes about this appointment..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary modal-action-btn btn-schedule" id="submitAppointment">
                        <i class="bi bi-calendar-check"></i> Schedule Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Cancel Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="cancelMessage">Are you sure you want to cancel this appointment?</p>
                    
                    <!-- Recurring options - only shown for recurring events -->
                    <div id="recurringCancelOptions" class="mb-3 d-none">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="cancelOption" id="cancelThisOnly" value="this" checked>
                            <label class="form-check-label" for="cancelThisOnly">
                                Cancel only this occurrence
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelOption" id="cancelFuture" value="future">
                            <label class="form-check-label" for="cancelFuture">
                                Cancel this and all future occurrences
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for cancellation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancelReason" rows="3" required></textarea>
                        <div class="invalid-feedback">Please provide a reason for cancellation.</div>
                    </div>
                    
                    <!-- Password verification -->
                    <div class="mb-3">
                        <label for="cancelPassword" class="form-label">Enter your password to confirm <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="cancelPassword" required>
                        <div class="invalid-feedback" id="passwordError">Please enter your password.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Cancel Appointment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade error-modal" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var calendarEl = document.getElementById('calendar');
            var appointmentDetailsEl = document.getElementById('appointmentDetails');
            var addAppointmentModal = new bootstrap.Modal(document.getElementById('addAppointmentModal'));
            var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            var editButton = document.getElementById('editAppointmentButton');
            var deleteButton = document.getElementById('deleteAppointmentButton');
            var toggleWeekButton = document.getElementById('toggleWeekView');
            var openTimeCheck = document.getElementById('openTimeCheck');
            var timeSelectionContainer = document.getElementById('timeSelectionContainer');
            var startTimeInput = document.getElementById('startTime');
            var endTimeInput = document.getElementById('endTime');
            var currentEvent = null;
            var currentView = 'dayGridMonth';

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Handle open time checkbox
            openTimeCheck.addEventListener('change', function() {
                if (this.checked) {
                    // If checked, disable time inputs
                    startTimeInput.disabled = true;
                    endTimeInput.disabled = true;
                    startTimeInput.value = '';
                    endTimeInput.value = '';
                    timeSelectionContainer.style.opacity = '0.5';
                } else {
                    // If unchecked, enable time inputs
                    startTimeInput.disabled = false;
                    endTimeInput.disabled = false;
                    timeSelectionContainer.style.opacity = '1';
                }
            });
            
            // Initialize recurring options
            document.getElementById('recurringCheck').addEventListener('change', function() {
                document.getElementById('recurringOptionsContainer').style.display = this.checked ? 'block' : 'none';
            });
            
            // Day selection based on appointment date
            document.getElementById('visitDate').addEventListener('change', function() {
                if (document.getElementById('recurringCheck').checked && document.getElementById('patternWeekly').checked) {
                    const date = new Date(this.value);
                    const dayOfWeek = date.getDay();
                    const checkbox = document.querySelector(`input[name="day_of_week[]"][value="${dayOfWeek}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                }
            });
            
            // Pattern type change handling
            document.querySelectorAll('input[name="pattern_type"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    document.getElementById('weeklyOptions').style.display = this.value === 'weekly' ? 'block' : 'none';
                });
            });
            
            // Initialize calendar with dynamic events
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: currentView,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventDisplay: 'block',
                events: function(info, successCallback, failureCallback) {
                    const start = info.startStr;
                    const end = info.endStr;
                    const searchTerm = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
                    const careWorkerId = document.getElementById('careWorkerSelect') ? document.getElementById('careWorkerSelect').value : '';
                    
                    $.ajax({
                        url: '{{ route("admin.careworker.appointments.get") }}',
                        method: 'GET',
                        data: {
                            start: start,
                            end: end,
                            search: searchTerm,
                            care_worker_id: careWorkerId
                        },
                        success: function(response) {
                            successCallback(response);
                        },
                        error: function(xhr) {
                            failureCallback(xhr);
                            showErrorModal('Failed to fetch appointments');
                        }
                    });
                },
                eventClick: function(info) {
                    showEventDetails(info.event);
                },
                eventContent: function(arg) {
                    const isFlexibleTime = arg.event.extendedProps.is_flexible_time;
                    
                    if (arg.view.type === 'dayGridMonth') {
                        // Show simplified content in month view
                        let eventEl = document.createElement('div');
                        eventEl.className = 'fc-event-main';
                        
                        if (isFlexibleTime) {
                            eventEl.innerHTML = `
                                <div class="event-worker">${arg.event.extendedProps.care_worker}</div>
                                <div class="event-details">
                                    <div>${arg.event.extendedProps.beneficiary}</div>
                                    <div class="open-time-indicator">
                                        <i class="bi bi-clock"></i> Flexible Time
                                    </div>
                                </div>
                            `;
                        } else {
                            eventEl.innerHTML = `
                                <div class="event-worker">${arg.event.extendedProps.care_worker}</div>
                                <div class="event-details">
                                    <div>${arg.event.extendedProps.beneficiary}</div>
                                    <div>${formatTime(new Date(arg.event.start))} - ${formatTime(new Date(arg.event.end))}</div>
                                </div>
                            `;
                        }
                        return { domNodes: [eventEl] };
                    } else {
                        // Show more details in week/day view
                        let eventEl = document.createElement('div');
                        eventEl.className = 'fc-event-main';
                        
                        if (isFlexibleTime) {
                            eventEl.innerHTML = `
                                <div class="event-worker">${arg.event.extendedProps.care_worker}</div>
                                <div class="event-details">
                                    <div>${arg.event.extendedProps.beneficiary}</div>
                                    <div class="open-time-indicator">
                                        <i class="bi bi-clock"></i> Flexible Time
                                    </div>
                                </div>
                            `;
                        } else {
                            eventEl.innerHTML = `
                                <div class="event-worker">${arg.event.extendedProps.care_worker}</div>
                                <div class="event-details">
                                    <div>${arg.event.extendedProps.beneficiary}</div>
                                    <div>${arg.event.extendedProps.visit_type}</div>
                                </div>
                            `;
                        }
                        return { domNodes: [eventEl] };
                    }
                },
                eventDidMount: function(arg) {
                    if (arg.el) {
                        const isFlexibleTime = arg.event.extendedProps.is_flexible_time;
                        let tooltipTitle;
                        
                        if (isFlexibleTime) {
                            tooltipTitle = `${arg.event.extendedProps.care_worker} → ${arg.event.extendedProps.beneficiary}\n` +
                                        `Type: ${arg.event.extendedProps.visit_type}\n` +
                                        `Schedule: Flexible Time`;
                        } else {
                            tooltipTitle = `${arg.event.extendedProps.care_worker} → ${arg.event.extendedProps.beneficiary}\n` +
                                        `Time: ${formatTime(new Date(arg.event.start))} - ${formatTime(new Date(arg.event.end))}\n` +
                                        `Type: ${arg.event.extendedProps.visit_type}`;
                        }
                        
                        arg.el.setAttribute('data-bs-toggle', 'tooltip');
                        arg.el.setAttribute('data-bs-placement', 'top');
                        arg.el.setAttribute('title', tooltipTitle);
                        new bootstrap.Tooltip(arg.el);
                    }
                }
            });

            calendar.render();
            
            // Load beneficiaries on page load
            loadBeneficiaries();
            
            // Load beneficiaries for dropdown
            function loadBeneficiaries() {
                $.ajax({
                    url: '{{ route("admin.careworker.appointments.beneficiaries") }}',
                    method: 'GET',
                    success: function(response) {
                        const select = document.getElementById('beneficiarySelect');
                        select.innerHTML = '<option value="">Select Beneficiary</option>';
                        
                        if (response.success && response.beneficiaries.length > 0) {
                            response.beneficiaries.forEach(function(beneficiary) {
                                const option = document.createElement('option');
                                option.value = beneficiary.id;
                                option.textContent = beneficiary.name;
                                select.appendChild(option);
                            });
                        }
                    },
                    error: function(xhr) {
                        showErrorModal('Failed to load beneficiaries');
                    }
                });
            }
            
            // Get beneficiary details and autofill form fields
            document.getElementById('beneficiarySelect').addEventListener('change', function() {
                const beneficiaryId = this.value;
                if (!beneficiaryId) return;
                
                // Show loading indicator
                document.getElementById('beneficiaryAddress').value = "Loading...";
                document.getElementById('beneficiaryPhone').value = "Loading...";
                
                $.ajax({
                    url: '{{ route("admin.careworker.appointments.beneficiary.details", ["id" => "__id__"]) }}'.replace('__id__', beneficiaryId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const beneficiary = response.beneficiary;
                            document.getElementById('beneficiaryAddress').value = beneficiary.address || 'Not Available';
                            document.getElementById('beneficiaryPhone').value = beneficiary.phone || 'Not Available';
                            
                            // If care worker is assigned in general care plan, select them
                            if (beneficiary.care_worker) {
                                const careWorkerSelect = document.getElementById('careWorkerSelect');
                                const options = careWorkerSelect.options;
                                
                                for (let i = 0; i < options.length; i++) {
                                    if (options[i].value == beneficiary.care_worker.id) {
                                        careWorkerSelect.selectedIndex = i;
                                        break;
                                    }
                                }
                            }
                        } else {
                            // Clear fields if there was a problem
                            document.getElementById('beneficiaryAddress').value = 'Not Available';
                            document.getElementById('beneficiaryPhone').value = 'Not Available';
                            showErrorModal(response.message || 'Failed to load beneficiary details');
                        }
                    },
                    error: function(xhr) {
                        // Clear fields on error
                        document.getElementById('beneficiaryAddress').value = 'Not Available';
                        document.getElementById('beneficiaryPhone').value = 'Not Available';
                        
                        // Show error message
                        let errorMsg = 'Failed to load beneficiary details';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showErrorModal(errorMsg);
                    }
                });
            });
            
            // Format time helper function
            function formatTime(date) {
                if (!date || isNaN(date.getTime())) return '';
                return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
            
            // Toggle week view button
            toggleWeekButton.addEventListener('click', function() {
                if (currentView === 'dayGridMonth') {
                    calendar.changeView('timeGridWeek');
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-month"></i> Month View';
                    currentView = 'timeGridWeek';
                } else {
                    calendar.changeView('dayGridMonth');
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> Week View';
                    currentView = 'dayGridMonth';
                }
            });
            
            // Show event details in side panel
            function showEventDetails(event) {
                // Enable action buttons
                editButton.disabled = false;
                deleteButton.disabled = false;
                
                // Store current event
                currentEvent = event;
                
                // Format date for display
                const eventDate = new Date(event.start);
                const formattedDate = eventDate.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                
                // Build details HTML
                appointmentDetailsEl.innerHTML = `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-person-badge"></i> Care Worker</div>
                        <p class="mb-0">${event.extendedProps.care_worker}</p>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-person-heart"></i> Beneficiary</div>
                        <div class="detail-item">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value">${event.extendedProps.beneficiary}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value">${event.extendedProps.phone || 'Not Available'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Address:</span>
                            <span class="detail-value">${event.extendedProps.address || 'Not Available'}</span>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-calendar-date"></i> Visit Details</div>
                        <div class="detail-item">
                            <span class="detail-label">Date:</span>
                            <span class="detail-value">${formattedDate}</span>
                        </div>
                        ${event.extendedProps.is_flexible_time ? 
                        `<div class="detail-item">
                            <span class="detail-label">Schedule:</span>
                            <span class="detail-value"><span class="open-time-indicator"><i class="bi bi-clock"></i> Flexible Time</span></span>
                        </div>` : 
                        `<div class="detail-item">
                            <span class="detail-label">Time:</span>
                            <span class="detail-value">${formatTime(new Date(event.start))} - ${formatTime(new Date(event.end))}</span>
                        </div>`
                        }
                        <div class="detail-item">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value">${event.extendedProps.visit_type}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">${getStatusBadge(event.extendedProps.status)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Recurring:</span>
                            <span class="detail-value">${event.extendedProps.recurring ? 'Yes' : 'No'}</span>
                        </div>
                    </div>
                    
                    ${event.extendedProps.notes ? `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-journal-text"></i> Notes</div>
                        <p class="mb-0">${event.extendedProps.notes}</p>
                    </div>
                    ` : ''}
                `;
                
                // Only show action buttons for scheduled appointments
                if (event.extendedProps.status.toLowerCase() !== 'scheduled') {
                    editButton.disabled = true;
                    deleteButton.disabled = true;
                }
            }
            
            // Helper function to create status badge
            function getStatusBadge(status) {
                const statusLower = status.toLowerCase();
                let badgeClass = 'bg-secondary';
                
                if (statusLower === 'scheduled') badgeClass = 'bg-primary';
                else if (statusLower === 'completed') badgeClass = 'bg-success';
                else if (statusLower === 'canceled') badgeClass = 'bg-danger';
                
                return `<span class="badge ${badgeClass}">${status}</span>`;
            }
            
            // Edit button click handler
            editButton.addEventListener('click', function() {
                if (!currentEvent) return;
                
                // Reset form
                document.getElementById('addAppointmentForm').reset();
                document.getElementById('modalErrors').classList.add('d-none');
                
                // Update modal title
                document.getElementById('addAppointmentModalLabel').innerHTML = '<i class="bi bi-pencil-square"></i> Edit Appointment';
                
                // Set form data from event
                document.getElementById('visitationId').value = currentEvent.extendedProps.visitation_id;
                
                // Set beneficiary and care worker
                document.getElementById('beneficiaryAddress').value = currentEvent.extendedProps.address;
                document.getElementById('beneficiaryPhone').value = currentEvent.extendedProps.phone;
                document.getElementById('notes').value = currentEvent.extendedProps.notes || '';
                document.getElementById('visitType').value = getVisitTypeValue(currentEvent.extendedProps.visit_type);
                
                // Set date - Fix timezone issue by getting local date components instead of using ISO string
                const eventDate = new Date(currentEvent.start);
                const year = eventDate.getFullYear();
                const month = String(eventDate.getMonth() + 1).padStart(2, '0');
                const day = String(eventDate.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;
                document.getElementById('visitDate').value = formattedDate;
                
                // Set time or flexible time
                document.getElementById('openTimeCheck').checked = currentEvent.extendedProps.is_flexible_time;
                if (currentEvent.extendedProps.is_flexible_time) {
                    timeSelectionContainer.style.opacity = '0.5';
                    startTimeInput.disabled = true;
                    endTimeInput.disabled = true;
                } else {
                    timeSelectionContainer.style.opacity = '1';
                    startTimeInput.disabled = false;
                    endTimeInput.disabled = false;
                    
                    if (currentEvent.start) {
                        const startTime = new Date(currentEvent.start);
                        document.getElementById('startTime').value = `${String(startTime.getHours()).padStart(2, '0')}:${String(startTime.getMinutes()).padStart(2, '0')}`;
                    }
                    
                    if (currentEvent.end) {
                        const endTime = new Date(currentEvent.end);
                        document.getElementById('endTime').value = `${String(endTime.getHours()).padStart(2, '0')}:${String(endTime.getMinutes()).padStart(2, '0')}`;
                    }
                }
                
                // Set recurring options
                const isRecurring = currentEvent.extendedProps.recurring;
                document.getElementById('recurringCheck').checked = isRecurring;
                document.getElementById('recurringOptionsContainer').style.display = isRecurring ? 'block' : 'none';
                
                if (isRecurring && currentEvent.extendedProps.recurring_pattern) {
                    const pattern = currentEvent.extendedProps.recurring_pattern;
                    document.querySelector(`input[name="pattern_type"][value="${pattern.type}"]`).checked = true;
                    document.getElementById('weeklyOptions').style.display = pattern.type === 'weekly' ? 'block' : 'none';
                    
                    // Handle day_of_week properly
                    if (pattern.day_of_week && pattern.type === 'weekly') {
                        // Convert to string and split if it's not already a string
                        const days = typeof pattern.day_of_week === 'string' ? 
                            pattern.day_of_week.split(',') : 
                            [pattern.day_of_week.toString()];
                            
                        days.forEach(day => {
                            const checkbox = document.querySelector(`input[name="day_of_week[]"][value="${day}"]`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }
                    
                    if (pattern.end_date) {
                        document.getElementById('recurrenceEnd').value = pattern.end_date;
                    }
                }
                
                // Show warning for editing recurring event
                if (isRecurring) {
                    // Add warning message to the modal
                    const modalContent = document.querySelector('#addAppointmentModal .modal-body');
                    const warningEl = document.createElement('div');
                    warningEl.className = 'alert alert-warning recurring-edit-warning';
                    warningEl.innerHTML = '<strong>Note:</strong> Editing a recurring appointment will only affect this and future occurrences. Past appointments will remain unchanged.';
                    
                    // Remove any existing warning
                    const existingWarning = document.querySelector('.recurring-edit-warning');
                    if (existingWarning) existingWarning.remove();
                    
                    // Insert at the beginning
                    modalContent.insertBefore(warningEl, modalContent.firstChild);
                }
                
                // Load full beneficiary and care worker lists
                loadBeneficiariesForEdit(currentEvent.extendedProps.beneficiary_id, currentEvent.extendedProps.care_worker_id);
                
                // Show modal
                addAppointmentModal.show();
            });
            
            // Helper function to convert UI visit type to backend value
            function getVisitTypeValue(uiVisitType) {
                const lower = uiVisitType.toLowerCase();
                if (lower.includes('routine')) return 'routine_care_visit';
                if (lower.includes('service')) return 'service_request';
                if (lower.includes('emergency')) return 'emergency_visit';
                return '';
            }
            
            // Load beneficiaries for edit form
            function loadBeneficiariesForEdit(beneficiaryId, careWorkerId) {
                // Load beneficiaries
                $.ajax({
                    url: '{{ route("admin.careworker.appointments.beneficiaries") }}',
                    method: 'GET',
                    success: function(response) {
                        const select = document.getElementById('beneficiarySelect');
                        select.innerHTML = '<option value="">Select Beneficiary</option>';
                        
                        if (response.success && response.beneficiaries.length > 0) {
                            response.beneficiaries.forEach(function(beneficiary) {
                                const option = document.createElement('option');
                                option.value = beneficiary.id;
                                option.textContent = beneficiary.name;
                                option.selected = (beneficiary.id == beneficiaryId);
                                select.appendChild(option);
                            });
                        }
                    }
                });
                
                // Select care worker if provided
                if (careWorkerId) {
                    const careWorkerSelect = document.getElementById('careWorkerSelect');
                    const options = careWorkerSelect.options;
                    
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value == careWorkerId) {
                            careWorkerSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            }
            
           // Form submission handler in adminCareworkerAppointments.blade.php
            document.getElementById('submitAppointment').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Reset error messages
                document.getElementById('modalErrors').classList.add('d-none');
                document.querySelectorAll('.error-feedback').forEach(el => el.innerHTML = '');
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                // Get form data
                const formData = new FormData(document.getElementById('addAppointmentForm'));
                const visitationId = formData.get('visitation_id');
                
                // Ensure is_flexible_time is explicitly set
                const isFlexibleTime = document.getElementById('openTimeCheck').checked;
                formData.set('is_flexible_time', isFlexibleTime ? '1' : '0');
                
                const url = visitationId ? 
                    '{{ route("admin.careworker.appointments.update") }}' : 
                    '{{ route("admin.careworker.appointments.store") }}';
                
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            let message = response.message;
                            
                            // If this was a split of a recurring event, show a more detailed message
                            if (response.split_occurred) {
                                showSuccessMessage('Recurring appointment updated. Past appointments have been preserved and future ones now follow the new pattern.');
                            } else {
                                showSuccessMessage(visitationId ? 'Appointment updated successfully!' : 'Appointment created successfully!');
                            }
                            
                            // Close the modal
                            addAppointmentModal.hide();
                            
                            // Reset form
                            document.getElementById('addAppointmentForm').reset();
                            
                            // Remove any warning message
                            const existingWarning = document.querySelector('.recurring-edit-warning');
                            if (existingWarning) existingWarning.remove();
                            
                            // Refresh calendar
                            calendar.refetchEvents();
                            
                            // Clear details panel if we're editing the currently selected event
                            if (currentEvent && visitationId && 
                                (currentEvent.extendedProps.visitation_id == visitationId || 
                                currentEvent.id.startsWith(visitationId + '-'))) {
                                // Update UI as necessary
                            }
                            // Show error message - can be validation errors or "no changes" message
                            if (response.message) {
                                // Display the specific message (like "No changes were made")
                                document.getElementById('modalErrors').classList.remove('d-none');
                                document.getElementById('modalErrors').innerHTML = `
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> ${response.message}
                                    </div>
                                `;
                            } else if (response.errors) {
                                // Display validation errors
                                showValidationErrors(response.errors);
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            // General error
                            showErrorModal('Failed to process appointment. Please try again.');
                        }
                    }
                });
            });
            
            // Delete button click handler - Update this in adminCareworkerAppointments.blade.php
            deleteButton.addEventListener('click', function() {
                if (!currentEvent) return;
                
                // Update the cancel message with the appointment details
                const beneficiary = currentEvent.extendedProps.beneficiary;
                const careWorker = currentEvent.extendedProps.care_worker;
                document.getElementById('cancelMessage').textContent = 
                    `Are you sure you want to cancel the appointment for ${beneficiary} with ${careWorker}?`;
                
                // Show or hide recurring options based on whether this is a recurring event
                const recurringCancelOptions = document.getElementById('recurringCancelOptions');
                if (currentEvent.extendedProps.recurring) {
                    recurringCancelOptions.classList.remove('d-none');
                } else {
                    recurringCancelOptions.classList.add('d-none');
                }
                
                // Reset the form
                document.getElementById('cancelReason').value = '';
                document.getElementById('cancelReason').classList.remove('is-invalid');
                document.getElementById('cancelPassword').value = '';
                document.getElementById('cancelPassword').classList.remove('is-invalid');
                document.getElementById('passwordError').textContent = 'Please enter your password.';
                
                // Show the modal
                confirmationModal.show();
            });

            // Confirm delete handler - Update this in adminCareworkerAppointments.blade.php
            // Cancel button handler
            document.getElementById('confirmDelete').addEventListener('click', function() {
                const reason = document.getElementById('cancelReason').value.trim();
                const password = document.getElementById('cancelPassword').value.trim();
                let isValid = true;
                
                // Validate reason
                if (!reason) {
                    document.getElementById('cancelReason').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('cancelReason').classList.remove('is-invalid');
                }
                
                // Validate password
                if (!password) {
                    document.getElementById('cancelPassword').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('cancelPassword').classList.remove('is-invalid');
                }
                
                if (!isValid) return;
                
                // Get cancellation option for recurring events
                let cancelOption = 'this'; // Default is cancel just this occurrence
                if (currentEvent.extendedProps.recurring) {
                    const selectedOption = document.querySelector('input[name="cancelOption"]:checked');
                    cancelOption = selectedOption ? selectedOption.value : 'this';
                }
                
                // Get the specific date for this occurrence (important for recurring events)
                const occurrenceDate = currentEvent.start ? formatDateForAPI(currentEvent.start) : null;
                
                // Show loading state
                const confirmButton = document.getElementById('confirmDelete');
                const originalText = confirmButton.textContent;
                confirmButton.disabled = true;
                confirmButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                
                // CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                $.ajax({
                    url: '{{ route("admin.careworker.appointments.cancel") }}',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        visitation_id: currentEvent.extendedProps.visitation_id,
                        occurrence_date: occurrenceDate,
                        reason: reason,
                        password: password,
                        cancel_option: cancelOption
                    },
                    success: function(response) {
                        // Handle success response
                        if (response.success) {
                            showSuccessMessage(response.message || 'Appointment cancelled successfully!');
                            
                            // Close the modal
                            confirmationModal.hide();
                            
                            // Reset form
                            document.getElementById('cancelReason').value = '';
                            document.getElementById('cancelPassword').value = '';
                            
                            // Refresh calendar
                            calendar.refetchEvents();
                            
                            // Clear details panel
                            appointmentDetailsEl.innerHTML = `
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                    <p class="mt-3 mb-0">Select an appointment to view details</p>
                                </div>
                            `;
                            
                            // Disable buttons
                            editButton.disabled = true;
                            deleteButton.disabled = true;
                            
                            // Reset current event
                            currentEvent = null;
                        } else {
                            // Show error
                            if (response.passwordError) {
                                document.getElementById('cancelPassword').classList.add('is-invalid');
                                document.getElementById('passwordError').textContent = response.passwordError;
                            } else if (response.errors) {
                                const errorMessage = Object.values(response.errors).flat().join('<br>');
                                showErrorModal(errorMessage);
                            } else {
                                showErrorModal(response.message || 'Failed to cancel appointment');
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to cancel appointment. Please try again.';
                        
                        if (xhr.status === 401) {
                            document.getElementById('cancelPassword').classList.add('is-invalid');
                            document.getElementById('passwordError').textContent = 'Incorrect password.';
                        } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            // Format validation errors
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                            showErrorModal(errorMessage);
                        } else {
                            showErrorModal(errorMessage);
                        }
                        
                        console.error('Error details:', xhr.responseText);
                    },
                    complete: function() {
                        // Reset button state
                        confirmButton.disabled = false;
                        confirmButton.innerHTML = originalText;
                    }
                });
            });

            // Helper function to format date for API
            function formatDateForAPI(date) {
                const d = new Date(date);
                return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
            }
            
            // Confirm delete handler
            document.getElementById('confirmDelete').addEventListener('click', function() {
                const reason = document.getElementById('cancelReason').value.trim();
                
                if (!reason) {
                    document.getElementById('cancelReason').classList.add('is-invalid');
                    return;
                }
                
                $.ajax({
                    url: '{{ route("admin.careworker.appointments.cancel") }}',
                    method: 'POST',
                    data: {
                        visitation_id: currentEvent.extendedProps.visitation_id,
                        reason: reason
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showSuccessMessage('Appointment canceled successfully!');
                            
                            // Close the modal
                            confirmationModal.hide();
                            
                            // Reset form
                            document.getElementById('cancelReason').value = '';
                            document.getElementById('cancelReason').classList.remove('is-invalid');
                            
                            // Refresh calendar
                            calendar.refetchEvents();
                            
                            // Clear details panel
                            appointmentDetailsEl.innerHTML = `
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                    <p class="mt-3 mb-0">Select an appointment to view details</p>
                                </div>
                            `;
                            
                            // Disable buttons
                            editButton.disabled = true;
                            deleteButton.disabled = true;
                        } else {
                            showErrorModal(response.message || 'Failed to cancel appointment');
                        }
                    },
                    error: function(xhr) {
                        showErrorModal('Failed to cancel appointment. Please try again.');
                    }
                });
            });
            
            // Show validation errors
            function showValidationErrors(errors) {
                const errorList = document.getElementById('errorList');
                errorList.innerHTML = '';
                
                for (const field in errors) {
                    const errorMsg = errors[field][0];
                    errorList.innerHTML += `<li>${errorMsg}</li>`;
                    
                    // Highlight the invalid field
                    const inputField = document.querySelector(`[name="${field}"]`);
                    if (inputField) {
                        inputField.classList.add('is-invalid');
                        
                        // Add error message below the field
                        const fieldId = field.replace(/_/g, '-');
                        const feedbackEl = document.getElementById(`${fieldId}-error`);
                        if (feedbackEl) {
                            feedbackEl.innerHTML = errorMsg;
                        }
                    }
                }
                
                document.getElementById('modalErrors').classList.remove('d-none');
            }
            
            // Show error modal
            function showErrorModal(message) {
                document.getElementById('errorMessage').textContent = message;
                errorModal.show();
            }
            
            // Show success message
            function showSuccessMessage(message) {
                // Create a toast element
                const toastEl = document.createElement('div');
                toastEl.className = 'toast position-fixed top-0 end-0 m-3';
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                
                toastEl.innerHTML = `
                    <div class="toast-header bg-success text-white">
                        <strong class="me-auto">Success</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                `;
                
                document.body.appendChild(toastEl);
                
                const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
                toast.show();
                
                // Remove the element when hidden
                toastEl.addEventListener('hidden.bs.toast', function() {
                    toastEl.remove();
                });
            }
            
            // Search functionality
            document.getElementById('searchInput').addEventListener('input', debounce(function() {
                calendar.refetchEvents();
            }, 500));
            
            // Debounce helper function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        });
    </script>
</body>
</html>