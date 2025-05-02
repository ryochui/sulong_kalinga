<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Worker Appointments</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Calendar Container */
        #calendar {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 15px;
            min-height: 600px;
        }

        /* Event Styling */
        .fc-event {
            cursor: pointer;
            border: none;
            padding: 3px 5px;
            margin-bottom: 2px;
        }

        .fc-event-main {
            display: flex;
            flex-direction: column;
        }

        .event-worker {
            font-weight: bold;
            font-size: clamp(0.8rem, 1vw, 0.9rem);
            white-space: normal !important;
        }

        .event-details {
            font-size: clamp(0.7rem, 0.9vw, 0.8rem);
            line-height: 1.2;
            white-space: normal !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 767.98px) {
            #calendar {
                padding: 5px;
            }
            .fc-toolbar-title {
                font-size: 1.2em !important;
            }
            .fc-button {
                font-size: clamp(0.8rem, 1vw, 1rem);
                padding: 0.25rem 0.5rem;
            }
        }

        /* Tooltip Styling */
        .tooltip-inner {
            max-width: 300px;
            padding: 8px 12px;
            text-align: left;
        }
        
        /* All-day event styling */
        .fc-daygrid-event-dot {
            display: none !important;
        }

        #home-content {
            font-size: clamp(0.8rem, 1.2vw, 1rem);
            line-height: 1.4;
        }

        .fc-toolbar-title {
            font-size: clamp(0.9rem, 1vw, 1rem);
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 10px;
        }

        #appointmentDetails h6 {
            font-size: clamp(0.9rem, 1.2vw, 1rem);
        }

        #appointmentDetails p {
            font-size: clamp(0.8rem, 1vw, 0.9rem);
        }

        /* Add Appointment Modal Styling */
        #addAppointmentModal .form-group {
            margin-bottom: 0.8rem;
        }

        #addAppointmentModal label {
            font-weight: 500;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        #addAppointmentModal .form-control {
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
        }

        #addAppointmentModal .btn-submit {
            width: 100%;
            margin-top: 0.8rem;
        }

        .day-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }

        .day-checkbox {
            flex: 1 0 calc(25% - 6px);
            min-width: 0;
        }

        .day-checkbox label {
            display: flex;
            align-items: center;
            padding: 6px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.8rem;
        }

        .day-checkbox input:checked + label {
            background-color: #e9f7fe;
            border-color: #4e73df;
        }

        .day-checkbox input {
            margin-right: 6px;
        }

        /* Add Appointment Button */
        .btn-add-appointment {
            width: 100%;
            margin-bottom: 20px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        /* Compact Modal */
        #addAppointmentModal .modal-dialog {
            max-width: 550px;
            margin: 1.5rem auto;
        }

        #addAppointmentModal .modal-content {
            padding: 10px;
            max-height: 90vh;
            overflow-y: auto;
        }

        #addAppointmentModal .modal-header {
            padding: 0.75rem 1rem;
        }

        #addAppointmentModal .modal-body {
            padding: 0.8rem;
        }

        #addAppointmentModal .modal-footer {
            padding: 0.5rem;
        }

        #addAppointmentModal .modal-title {
            font-size: 1.1rem;
        }

        #addAppointmentModal textarea {
            min-height: 80px;
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">CARE WORKER APPOINTMENTS</div>
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <div class="col-12 p-0">
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <!-- Calendar Display of Appointments -->
                            <div id="calendar" class="p-2"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Appointment Details Panel -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">Appointment Details</h5>
                                        </div>
                                        <div class="card-body" id="appointmentDetails">
                                            <p class="text-muted mb-0">Select an appointment to view details</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Search Bar -->
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" placeholder="Search appointments..." aria-label="Search appointments" aria-describedby="searchButton">
                                        <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                            <i class="bi bi-search"></i> Search
                                        </button>
                                    </div>
                                    <!-- Add Appointment Button -->
                                    <button type="button" class="btn btn-success btn-add-appointment mb-2" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                        <i class="bi bi-plus-circle"></i> Add New Appointment
                                    </button>
                                    <!-- Edit Appointment Button -->
                                    <button type="button" class="btn btn-warning btn-add-appointment mb-2" id="editAppointmentButton">
                                        <i class="bi bi-pencil-square"></i> Edit Appointment
                                    </button>
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
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addAppointmentModalLabel"><i class="bi bi-plus-circle"></i> Add New Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAppointmentForm">
                        <div class="form-group">
                            <label for="careWorkerSelect">Care Worker</label>
                            <select class="form-control" id="careWorkerSelect" required>
                                <option value="">Select Care Worker</option>
                                <option value="1">Sarah Johnson</option>
                                <option value="2">Michael Brown</option>
                                <option value="3">Emma Davis</option>
                                <option value="4">David Miller</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="beneficiarySelect">Beneficiary</label>
                            <select class="form-control" id="beneficiarySelect" required>
                                <option value="">Select Beneficiary</option>
                                <option value="1">Robert Chen</option>
                                <option value="2">Margaret Williams</option>
                                <option value="3">James Wilson</option>
                                <option value="4">Patricia Taylor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Day of the Week</label>
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
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" rows="2" placeholder="Enter any additional notes"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitAppointment">
                        <i class="bi bi-calendar-plus"></i> Schedule
                    </button>
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
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                eventDisplay: 'block',
                events: [
                    // Dummy data - showing all-day events
                    {
                        title: 'Sarah Johnson',
                        start: new Date(),
                        allDay: true,
                        extendedProps: {
                            careWorker: 'Sarah Johnson',
                            beneficiary: 'Robert Chen',
                            address: '123 Maple St, Springfield, SP 12345',
                            phone: '(555) 123-4567',
                            notes: 'Monthly checkup and medication review'
                        },
                        backgroundColor: '#4e73df',
                        borderColor: '#4e73df'
                    },
                    {
                        title: 'Michael Brown',
                        start: new Date(new Date().getTime() + 86400000), // Tomorrow
                        allDay: true,
                        extendedProps: {
                            careWorker: 'Michael Brown',
                            beneficiary: 'Margaret Williams',
                            address: '456 Oak Ave, Springfield, SP 12345',
                            phone: '(555) 987-6543',
                            notes: 'Physical therapy session'
                        },
                        backgroundColor: '#1cc88a',
                        borderColor: '#1cc88a'
                    },
                    {
                        title: 'Emma Davis',
                        start: new Date(new Date().getTime() + 2 * 86400000), // Day after tomorrow
                        allDay: true,
                        extendedProps: {
                            careWorker: 'Emma Davis',
                            beneficiary: 'James Wilson',
                            address: '789 Pine Rd, Springfield, SP 12345',
                            phone: '(555) 456-7890',
                            notes: 'Wound dressing change'
                        },
                        backgroundColor: '#36b9cc',
                        borderColor: '#36b9cc'
                    },
                    {
                        title: 'David Miller',
                        start: new Date(new Date().getTime() + 3 * 86400000), // 3 days from now
                        allDay: true,
                        extendedProps: {
                            careWorker: 'David Miller',
                            beneficiary: 'Patricia Taylor',
                            address: '321 Elm Blvd, Springfield, SP 12345',
                            phone: '(555) 789-0123',
                            notes: 'Nutrition counseling'
                        },
                        backgroundColor: '#f6c23e',
                        borderColor: '#f6c23e'
                    }
                ],
                eventContent: function(arg) {
                    // Custom event content to show more details
                    let eventEl = document.createElement('div');
                    eventEl.className = 'fc-event-main';
                    eventEl.innerHTML = `
                        <div class="event-worker">${arg.event.extendedProps.careWorker}</div>
                        <div class="event-details">
                            <div>${arg.event.extendedProps.beneficiary}</div>
                            <div>${arg.event.extendedProps.address.split(',')[0]}</div>
                        </div>
                    `;
                    return { domNodes: [eventEl] };
                },
                eventDidMount: function(arg) {
                    // Add tooltip with full address
                    if (arg.el) {
                        arg.el.setAttribute('data-bs-toggle', 'tooltip');
                        arg.el.setAttribute('data-bs-placement', 'top');
                        arg.el.setAttribute('title', 
                            `Care Worker: ${arg.event.extendedProps.careWorker}\n` +
                            `Beneficiary: ${arg.event.extendedProps.beneficiary}\n` +
                            `Address: ${arg.event.extendedProps.address}`
                        );
                        new bootstrap.Tooltip(arg.el);
                    }
                },
                eventClick: function(info) {
                    // Display full details in the side panel
                    appointmentDetailsEl.innerHTML = `
                        <h6 class="text-primary mb-3">${info.event.extendedProps.careWorker}'s Appointment</h6>
                        <div class="mb-3">
                            <h6 class="text-secondary">Beneficiary Information</h6>
                            <p class="mb-1"><strong>Name:</strong> ${info.event.extendedProps.beneficiary}</p>
                            <p class="mb-1"><strong>Phone:</strong> ${info.event.extendedProps.phone}</p>
                            <p class="mb-1"><strong>Address:</strong> ${info.event.extendedProps.address}</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-secondary">Appointment Date</h6>
                            <p class="mb-1">${info.event.start.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                        <div>
                            <h6 class="text-secondary">Notes</h6>
                            <p>${info.event.extendedProps.notes}</p>
                        </div>
                    `;
                }
            });

            calendar.render();

            // Handle form submission
            document.getElementById('submitAppointment').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get form values
                const careWorkerSelect = document.getElementById('careWorkerSelect');
                const beneficiarySelect = document.getElementById('beneficiarySelect');
                const notes = document.getElementById('notes');
                const dayCheckboxes = document.querySelectorAll('input[name="days"]:checked');
                
                // Validate form
                if (!careWorkerSelect.value || !beneficiarySelect.value || dayCheckboxes.length === 0) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // Get selected values
                const careWorker = careWorkerSelect.options[careWorkerSelect.selectedIndex].text;
                const beneficiary = beneficiarySelect.options[beneficiarySelect.selectedIndex].text;
                const selectedDays = Array.from(dayCheckboxes).map(checkbox => checkbox.value);
                const appointmentNotes = notes.value;
                
                // In a real app, you would send this data to your backend
                console.log('New Appointment:', {
                    careWorker,
                    beneficiary,
                    days: selectedDays,
                    notes: appointmentNotes
                });
                
                // Show success message
                alert('Appointment(s) scheduled successfully!');
                
                // Reset form and close modal
                addAppointmentForm.reset();
                addAppointmentModal.hide();
                
                // In a real app, you would refresh the calendar here
                // calendar.refetchEvents();
            });
        });
    </script>
</body>
</html>