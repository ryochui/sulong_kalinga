<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            color:rgb(233, 137, 137);
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
                                <input type="text" class="form-control search-input" placeholder="     Search appointments..." aria-label="Search appointments">
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
                                    <h5 class="section-heading text-white mb-0">
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
                    <form id="addAppointmentForm">
                        <!-- Care Worker Selection -->
                        <div class="form-group">
                            <label for="careWorkerSelect">
                                <i class="bi bi-person-badge"></i> Care Worker
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="careWorkerSelect" required>
                                    <option value="">Select Care Worker</option>
                                    <option value="1">Sarah Johnson</option>
                                    <option value="2">Michael Brown</option>
                                    <option value="3">Emma Davis</option>
                                    <option value="4">David Miller</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Beneficiary Selection -->
                        <div class="form-group">
                            <label for="beneficiarySelect">
                                <i class="bi bi-person-heart"></i> Beneficiary
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="beneficiarySelect" required>
                                    <option value="">Select Beneficiary</option>
                                    <option value="1">Robert Chen</option>
                                    <option value="2">Margaret Williams</option>
                                    <option value="3">James Wilson</option>
                                    <option value="4">Patricia Taylor</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="startDate">
                                        <i class="bi bi-calendar-date"></i> Start Date
                                    </label>
                                    <input type="date" class="form-control" id="startDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="endDate">
                                        <i class="bi bi-calendar-date"></i> End Date (Optional)
                                    </label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
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
                                        <input type="time" class="form-control" id="startTime">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="endTime" class="form-label">End Time</label>
                                        <input type="time" class="form-control" id="endTime">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Open Time / Flexible Schedule Option -->
                            <div class="open-time-container">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="openTimeCheck">
                                    <label class="form-check-label" for="openTimeCheck">
                                        Open Time / Flexible Schedule (Care Worker will determine actual time)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Day Selection -->
                        <div class="form-group">
                            <label>
                                <i class="bi bi-calendar-week"></i> Repeat on Days
                            </label>
                            <div class="day-checkboxes">
                                <div class="day-checkbox">
                                    <input type="checkbox" id="monday" name="days" value="Monday">
                                    <label for="monday">Mon</label>
                                </div>
                                <div class="day-checkbox">
                                    <input type="checkbox" id="tuesday" name="days" value="Tuesday">
                                    <label for="tuesday">Tue</label>
                                </div>
                                <div class="day-checkbox">
                                    <input type="checkbox" id="wednesday" name="days" value="Wednesday">
                                    <label for="wednesday">Wed</label>
                                </div>
                                <div class="day-checkbox">
                                    <input type="checkbox" id="thursday" name="days" value="Thursday">
                                    <label for="thursday">Thu</label>
                                </div>
                                <div class="day-checkbox">
                                    <input type="checkbox" id="friday" name="days" value="Friday">
                                    <label for="friday">Fri</label>
                                </div>
                                <div class="day-checkbox">
                                    <input type="checkbox" id="saturday" name="days" value="Saturday">
                                    <label for="saturday">Sat</label>
                                </div>
                                <div class="day-checkbox">
                                    <input type="checkbox" id="sunday" name="days" value="Sunday">
                                    <label for="sunday">Sun</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Visit Type -->
                        <div class="form-group">
                            <label for="visitType">
                                <i class="bi bi-clipboard2-pulse"></i> Visit Type
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="visitType" required>
                                    <option value="">Select Visit Type</option>
                                    <option value="routine">Routine Care Visit</option>
                                    <option value="service">Service Request</option>
                                    <option value="emergency">Emergency Visit</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="form-group mb-0">
                            <label for="notes">
                                <i class="bi bi-journal-text"></i> Visit Notes
                            </label>
                            <textarea class="form-control" id="notes" rows="3" placeholder="Enter any additional instructions or notes about this appointment..."></textarea>
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

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Cancellation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this appointment?</p>
                    <div class="form-group mt-3">
                        <label for="confirmPassword" class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Cancel It</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var appointmentDetailsEl = document.getElementById('appointmentDetails');
            var addAppointmentForm = document.getElementById('addAppointmentForm');
            var addAppointmentModal = new bootstrap.Modal(document.getElementById('addAppointmentModal'));
            var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
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
            
            // Color coding for different care workers
            const careWorkerColors = {
                'Sarah Johnson': {
                    background: '#4e73df',
                    border: '#4668cc'
                },
                'Michael Brown': {
                    background: '#1cc88a',
                    border: '#19b77d'
                },
                'Emma Davis': {
                    background: '#36b9cc',
                    border: '#31a8ba'
                },
                'David Miller': {
                    background: '#f6c23e',
                    border: '#e4b138'
                }
            };

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: currentView,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventDisplay: 'block',
                events: [
                    // Regular scheduled appointments
                    {
                        id: '1',
                        title: 'Sarah Johnson',
                        start: new Date().setHours(9, 0),
                        end: new Date().setHours(10, 30),
                        extendedProps: {
                            careWorker: 'Sarah Johnson',
                            beneficiary: 'Robert Chen',
                            address: '123 Maple St, Springfield, SP 12345',
                            phone: '(555) 123-4567',
                            notes: 'Monthly checkup and medication review',
                            visitType: 'Routine Care Visit',
                            isOpenTime: false
                        },
                        backgroundColor: careWorkerColors['Sarah Johnson'].background,
                        borderColor: careWorkerColors['Sarah Johnson'].border
                    },
                    {
                        id: '2',
                        title: 'Michael Brown',
                        start: new Date(new Date().getTime() + 86400000).setHours(11, 0), // Tomorrow
                        end: new Date(new Date().getTime() + 86400000).setHours(12, 0),
                        extendedProps: {
                            careWorker: 'Michael Brown',
                            beneficiary: 'Margaret Williams',
                            address: '456 Oak Ave, Springfield, SP 12345',
                            phone: '(555) 987-6543',
                            notes: 'Physical therapy session',
                            visitType: 'Service Request',
                            isOpenTime: false
                        },
                        backgroundColor: careWorkerColors['Michael Brown'].background,
                        borderColor: careWorkerColors['Michael Brown'].border
                    },
                    
                    // Open-time / flexible schedule appointments
                    {
                        id: '3',
                        title: 'Emma Davis - Open Time',
                        start: new Date(new Date().getTime() + 2 * 86400000), // Day after tomorrow
                        allDay: true,
                        extendedProps: {
                            careWorker: 'Emma Davis',
                            beneficiary: 'James Wilson',
                            address: '789 Pine Rd, Springfield, SP 12345',
                            phone: '(555) 456-7890',
                            notes: 'Assistance with medication administration and daily tasks',
                            visitType: 'Routine Care Visit',
                            isOpenTime: true
                        },
                        backgroundColor: 'rgba(54, 185, 204, 0.15)',
                        borderColor: '#36b9cc',
                        textColor: '#333',
                        classNames: ['open-time']
                    },
                    {
                        id: '4',
                        title: 'David Miller - Open Time',
                        start: new Date(new Date().getTime() + 3 * 86400000), // 3 days from now
                        allDay: true,
                        extendedProps: {
                            careWorker: 'David Miller',
                            beneficiary: 'Patricia Taylor',
                            address: '321 Elm Blvd, Springfield, SP 12345',
                            phone: '(555) 789-0123',
                            notes: 'Emergency welfare check requested by family member',
                            visitType: 'Emergency Visit',
                            isOpenTime: true
                        },
                        backgroundColor: 'rgba(246, 194, 62, 0.15)',
                        borderColor: '#f6c23e',
                        textColor: '#333',
                        classNames: ['open-time']
                    }
                ],
                eventContent: function(arg) {
                    const isOpenTime = arg.event.extendedProps.isOpenTime;
                    
                    if (arg.view.type === 'dayGridMonth') {
                        // Show simplified content in month view
                        let eventEl = document.createElement('div');
                        eventEl.className = 'fc-event-main';
                        
                        if (isOpenTime) {
                            eventEl.innerHTML = `
                                <div class="event-worker">${arg.event.extendedProps.careWorker}</div>
                                <div class="event-details">
                                    <div>${arg.event.extendedProps.beneficiary}</div>
                                    <div class="open-time-indicator">
                                        <i class="bi bi-clock"></i> Flexible Time
                                    </div>
                                </div>
                            `;
                        } else {
                            eventEl.innerHTML = `
                                <div class="event-worker">${arg.event.extendedProps.careWorker}</div>
                                <div class="event-details">
                                    <div>${arg.event.extendedProps.beneficiary}</div>
                                    <div>${formatTime(arg.event.start)} - ${formatTime(arg.event.end)}</div>
                                </div>
                            `;
                        }
                        return { domNodes: [eventEl] };
                    } else {
                        // Show more details in week/day view
                        let eventEl = document.createElement('div');
                        eventEl.className = 'fc-event-main';
                        
                        if (isOpenTime) {
                            eventEl.innerHTML = `
                                <div class="event-worker">${arg.event.extendedProps.careWorker}</div>
                                <div class="event-details">
                                    <div>${arg.event.extendedProps.beneficiary}</div>
                                    <div class="open-time-indicator">
                                        <i class="bi bi-clock"></i> Flexible Time
                                    </div>
                                </div>
                            `;
                        } else {
                            eventEl.innerHTML = `
                                <div class="event-worker">${arg.event.extendedProps.careWorker}</div>
                                <div class="event-details">
                                    <div>${arg.event.extendedProps.beneficiary}</div>
                                    <div>${arg.event.extendedProps.visitType}</div>
                                </div>
                            `;
                        }
                        return { domNodes: [eventEl] };
                    }
                },
                eventDidMount: function(arg) {
                    if (arg.el) {
                        const isOpenTime = arg.event.extendedProps.isOpenTime;
                        let tooltipTitle;
                        
                        if (isOpenTime) {
                            tooltipTitle = `${arg.event.extendedProps.careWorker} → ${arg.event.extendedProps.beneficiary}\n` +
                                          `Type: ${arg.event.extendedProps.visitType}\n` +
                                          `Schedule: Flexible Time`;
                        } else {
                            tooltipTitle = `${arg.event.extendedProps.careWorker} → ${arg.event.extendedProps.beneficiary}\n` +
                                          `Time: ${formatTime(arg.event.start)} - ${formatTime(arg.event.end)}\n` +
                                          `Type: ${arg.event.extendedProps.visitType}`;
                        }
                        
                        arg.el.setAttribute('data-bs-toggle', 'tooltip');
                        arg.el.setAttribute('data-bs-placement', 'top');
                        arg.el.setAttribute('title', tooltipTitle);
                        new bootstrap.Tooltip(arg.el);
                    }
                },
                eventClick: function(info) {
                    // Save current event for editing/deletion
                    currentEvent = info.event;
                    
                    // Display full details in the side panel
                    const isOpenTime = info.event.extendedProps.isOpenTime;
                    
                    appointmentDetailsEl.innerHTML = `
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-person-badge"></i> Care Worker</div>
                            <p class="mb-0">${info.event.extendedProps.careWorker}</p>
                        </div>
                        
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-person-heart"></i> Beneficiary</div>
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">${info.event.extendedProps.beneficiary}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value">${info.event.extendedProps.phone}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Address:</span>
                                <span class="detail-value">${info.event.extendedProps.address}</span>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-calendar-date"></i> Visit Details</div>
                            <div class="detail-item">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value">${info.event.start.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                            </div>
                            ${isOpenTime ? 
                              `<div class="detail-item">
                                  <span class="detail-label">Schedule:</span>
                                  <span class="detail-value"><span class="open-time-indicator"><i class="bi bi-clock"></i> Flexible Time</span></span>
                               </div>` : 
                              `<div class="detail-item">
                                  <span class="detail-label">Time:</span>
                                  <span class="detail-value">${formatTime(info.event.start)} - ${formatTime(info.event.end)}</span>
                               </div>`
                            }
                            <div class="detail-item">
                                <span class="detail-label">Type:</span>
                                <span class="detail-value">${info.event.extendedProps.visitType}</span>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-journal-text"></i> Notes</div>
                            <p class="mb-0">${info.event.extendedProps.notes}</p>
                        </div>
                    `;
                    
                    // Enable edit and delete buttons when an event is selected
                    editButton.disabled = false;
                    deleteButton.disabled = false;
                }
            });

            calendar.render();
            
            // Format time helper function
            function formatTime(date) {
                if (!date) return '';
                return new Date(date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
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

            // Form submission handler
            document.getElementById('submitAppointment').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get form values
                const careWorkerSelect = document.getElementById('careWorkerSelect');
                const beneficiarySelect = document.getElementById('beneficiarySelect');
                const visitType = document.getElementById('visitType');
                const startDate = document.getElementById('startDate');
                const isOpenTime = document.getElementById('openTimeCheck').checked;
                const notes = document.getElementById('notes').value;
                const dayCheckboxes = document.querySelectorAll('input[name="days"]:checked');
                
                // Validate required fields
                if (!careWorkerSelect.value || !beneficiarySelect.value || !startDate.value || 
                    !visitType.value || dayCheckboxes.length === 0) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // Validate time fields if not open time
                if (!isOpenTime && (!startTimeInput.value || !endTimeInput.value)) {
                    alert('Please specify start and end times or check "Open Time / Flexible Schedule"');
                    return;
                }
                
                // Get selected values
                const careWorker = careWorkerSelect.options[careWorkerSelect.selectedIndex].text;
                const beneficiary = beneficiarySelect.options[beneficiarySelect.selectedIndex].text;
                const visitTypeText = visitType.options[visitType.selectedIndex].text;
                const selectedDays = Array.from(dayCheckboxes).map(checkbox => checkbox.value);
                
                // In a real app, you would send this data to your backend
                console.log('New Appointment:', {
                    careWorker,
                    beneficiary,
                    startDate: startDate.value,
                    isOpenTime: isOpenTime,
                    startTime: isOpenTime ? null : startTimeInput.value,
                    endTime: isOpenTime ? null : endTimeInput.value,
                    visitType: visitTypeText,
                    days: selectedDays,
                    notes
                });
                
                // Show success message
                alert('Appointment(s) scheduled successfully!');
                
                // Reset form and close modal
                addAppointmentForm.reset();
                addAppointmentModal.hide();
                
                // In a real app, you would refresh the calendar here
                // calendar.refetchEvents();
            });
            
            // Edit button click handler
            editButton.addEventListener('click', function() {
                if (!currentEvent) return;
                
                // Here you would pre-fill the form with current event data
                const event = currentEvent;
                
                // Populate form fields with event data
                document.getElementById('careWorkerSelect').value = event.id; // assuming id corresponds to care worker id
                document.getElementById('beneficiarySelect').value = event.extendedProps.beneficiaryId || '';
                document.getElementById('visitType').value = event.extendedProps.visitTypeValue || '';
                document.getElementById('notes').value = event.extendedProps.notes || '';
                
                // Set date
                const eventDate = new Date(event.start);
                const formattedDate = eventDate.toISOString().split('T')[0];
                document.getElementById('startDate').value = formattedDate;
                
                // Handle open time vs specific time
                const isOpenTime = event.extendedProps.isOpenTime;
                document.getElementById('openTimeCheck').checked = isOpenTime;
                
                if (!isOpenTime && event.start && event.end) {
                    // Format times for input fields (HH:MM format)
                    const startHour = eventDate.getHours().toString().padStart(2, '0');
                    const startMinute = eventDate.getMinutes().toString().padStart(2, '0');
                    document.getElementById('startTime').value = `${startHour}:${startMinute}`;
                    
                    const endDate = new Date(event.end);
                    const endHour = endDate.getHours().toString().padStart(2, '0');
                    const endMinute = endDate.getMinutes().toString().padStart(2, '0');
                    document.getElementById('endTime').value = `${endHour}:${endMinute}`;
                    
                    // Enable time fields
                    document.getElementById('startTime').disabled = false;
                    document.getElementById('endTime').disabled = false;
                    document.getElementById('timeSelectionContainer').style.opacity = '1';
                } else {
                    // Disable time fields for open time
                    document.getElementById('startTime').value = '';
                    document.getElementById('endTime').value = '';
                    document.getElementById('startTime').disabled = true;
                    document.getElementById('endTime').disabled = true;
                    document.getElementById('timeSelectionContainer').style.opacity = '0.5';
                }
                
                // Show modal
                addAppointmentModal.show();
            });
            
            // Delete button click handler
            deleteButton.addEventListener('click', function() {
                if (!currentEvent) return;
                document.getElementById('confirmPassword').value = '';
                confirmationModal.show();
            });
            
            // Confirm delete handler with password verification
            document.getElementById('confirmDelete').addEventListener('click', function() {
                const password = document.getElementById('confirmPassword').value;
                
                if (!password) {
                    alert('Please enter your password to confirm');
                    return;
                }
                
                if (!currentEvent) return;
                
                // In a real app, you would verify the password on the server
                if (password === 'admin') {  // For demo purposes only - in a real app this would be a server-side check
                    // Send delete request to server
                    console.log('Deleting appointment:', currentEvent.id);
                    
                    // Remove the event from calendar
                    currentEvent.remove();
                    
                    // Reset state
                    currentEvent = null;
                    editButton.disabled = true;
                    deleteButton.disabled = true;
                    
                    // Clear details panel
                    appointmentDetailsEl.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                            <p class="mt-3 mb-0">Select an appointment to view details</p>
                        </div>
                    `;
                    
                    // Close modal
                    confirmationModal.hide();
                } else {
                    alert('Incorrect password. Cancellation denied.');
                }
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                
                if (searchTerm.length < 2) {
                    // If search term is too short, show all events
                    calendar.getEvents().forEach(event => {
                        event.setProp('display', 'auto');
                    });
                    return;
                }
                
                // Filter events
                calendar.getEvents().forEach(event => {
                    const careWorker = event.extendedProps.careWorker.toLowerCase();
                    const beneficiary = event.extendedProps.beneficiary.toLowerCase();
                    const visitType = event.extendedProps.visitType.toLowerCase();
                    const notes = event.extendedProps.notes.toLowerCase();
                    
                    if (careWorker.includes(searchTerm) || 
                        beneficiary.includes(searchTerm) || 
                        visitType.includes(searchTerm) || 
                        notes.includes(searchTerm)) {
                        event.setProp('display', 'auto');
                    } else {
                        event.setProp('display', 'none');
                    }
                });
            });
        });
    </script>
</body>
</html>