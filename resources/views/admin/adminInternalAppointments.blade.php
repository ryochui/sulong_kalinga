<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Appointments</title>
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
            width: 100% !important;
        }

        /* Fix for fc-scrollgrid responsiveness */
        .fc-scrollgrid {
            width: 100% !important;
            min-width: 0 !important;
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 10px;
        }

        /* Event Styling */
        .fc-event {
            cursor: pointer;
            border: none;
            padding: 5px;
            margin-bottom: 2px;
            border-radius: 4px;
            overflow: hidden;
        }

        .fc-event-main {
            display: flex;
            flex-direction: column;
        }

        .event-title {
            font-weight: bold;
            font-size: 0.85rem;
            white-space: normal;
            line-height: 1.2;
        }

        .event-details {
            font-size: 0.75rem;
            line-height: 1.2;
            white-space: normal;
            margin-top: 2px;
        }

        .event-time {
            font-weight: 500;
            color: #fff;
            background-color: rgba(0,0,0,0.2);
            padding: 1px 4px;
            border-radius: 3px;
            display: inline-block;
            margin-top: 3px;
        }

        .event-type {
            font-style: italic;
            color: #fff;
            opacity: 0.9;
        }

        /* Attendees Dropdown */
        .attendees-dropdown {
            position: relative;
        }

        .attendees-select {
            display: none;
        }

        .attendees-display {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            min-height: 38px;
            cursor: pointer;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            background-color: #fff;
        }

        .attendee-tag {
            background-color: #e9ecef;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            display: flex;
            align-items: center;
            font-size: 0.8rem;
        }

        .attendee-tag-remove {
            margin-left: 5px;
            cursor: pointer;
            color: #6c757d;
        }

        .attendee-tag-remove:hover {
            color: #dc3545;
        }

        .attendees-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background-color: white;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
            padding: 5px;
            display: none;
        }

        .attendee-search {
            padding: 5px;
            margin-bottom: 5px;
            border-bottom: 1px solid #dee2e6;
        }

        .attendee-search input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .attendee-option {
            padding: 5px 10px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .attendee-option:hover {
            background-color: #f8f9fa;
        }

        .attendee-option input {
            margin-right: 8px;
        }

        .no-attendees {
            color: #6c757d;
            font-style: italic;
            padding: 0.375rem 0.75rem;
        }

        /* Appointment Details Panel */
        #appointmentDetails {
            max-height: 400px;
            overflow-y: auto;
        }

        .detail-item {
            margin-bottom: 0.8rem;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.2rem;
        }

        .detail-value {
            padding-left: 1rem;
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
            .event-title {
                font-size: 0.75rem;
            }
            .event-details {
                font-size: 0.65rem;
            }
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">INTERNAL APPOINTMENTS</div>
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
                                </div>
                                <div class="col-md-12">
                                    <!-- Search Bar -->
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" placeholder="Search appointments..." aria-label="Search appointments" aria-describedby="searchButton">
                                        <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                            <i class="bi bi-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-6">
                                    <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                        <i class="bi bi-plus-circle"></i> Add New
                                    </button>
                                </div>
                                <div class="col-md-12 col-sm-6">
                                    <button type="button" class="btn btn-warning w-100 mb-2" id="editAppointmentButton">
                                        <i class="bi bi-pencil-square"></i> Edit
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
                        <div class="form-group mb-3">
                            <label for="appointmentTitle">Appointment Title</label>
                            <input type="text" class="form-control" id="appointmentTitle" placeholder="Enter appointment title" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="appointmentType">Appointment Type</label>
                            <select class="form-select" id="appointmentType" required>
                                <option value="" disabled selected>Select appointment type</option>
                                <option value="Briefing">Briefing</option>
                                <option value="Team Meeting">Team Meeting</option>
                                <option value="One-on-One">One-on-One</option>
                                <option value="Training Session">Training Session</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="attendees">Attendees</label>
                            <div class="attendees-dropdown">
                                <select class="attendees-select" id="attendees" multiple style="display: none;">
                                    <option value="John Doe">John Doe</option>
                                    <option value="Jane Smith">Jane Smith</option>
                                    <option value="Michael Brown">Michael Brown</option>
                                    <option value="Emily Davis">Emily Davis</option>
                                </select>
                                <div class="attendees-display" id="attendeesDisplay" onclick="toggleAttendeesDropdown()">
                                    <span class="no-attendees">Select attendees...</span>
                                </div>
                                <div class="attendees-dropdown-menu" id="attendeesDropdown">
                                    <div class="attendee-search">
                                        <input type="text" placeholder="Search attendees..." id="attendeeSearch">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="datetime-row mb-3">
                            <div class="form-group">
                                <label for="appointmentDate">Date</label>
                                <input type="date" class="form-control" id="appointmentDate" required>
                            </div>
                            <div class="form-group">
                                <label for="appointmentTime">Time</label>
                                <input type="time" class="form-control" id="appointmentTime" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="appointmentPlace">Place</label>
                            <input type="text" class="form-control" id="appointmentPlace" placeholder="Enter location" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="appointmentNotes">Notes</label>
                            <textarea class="form-control" id="appointmentNotes" rows="3" placeholder="Enter any additional notes"></textarea>
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
            
            // Initialize attendees dropdown
            const attendeesSelect = document.getElementById('attendees');
            const attendeesDisplay = document.getElementById('attendeesDisplay');
            const attendeesDropdown = document.getElementById('attendeesDropdown');
            const attendeeSearch = document.getElementById('attendeeSearch');
            
            // Populate attendees dropdown with checkboxes
            function populateAttendeesDropdown(filter = '') {
                attendeesDropdown.innerHTML = `
                    <div class="attendee-search">
                        <input type="text" placeholder="Search attendees..." id="attendeeSearch" value="${filter}">
                    </div>
                `;
                
                const searchInput = document.getElementById('attendeeSearch');
                searchInput.addEventListener('input', function() {
                    populateAttendeesDropdown(this.value);
                });
                
                const selectedAttendees = Array.from(attendeesSelect.selectedOptions).map(opt => opt.value);
                
                let hasMatches = false;
                Array.from(attendeesSelect.options).forEach(option => {
                    if (filter === '' || option.text.toLowerCase().includes(filter.toLowerCase())) {
                        hasMatches = true;
                        const optionDiv = document.createElement('div');
                        optionDiv.className = 'attendee-option';
                        optionDiv.innerHTML = `
                            <input type="checkbox" id="attendee-${option.value}" value="${option.value}" 
                                ${selectedAttendees.includes(option.value) ? 'checked' : ''}>
                            <label for="attendee-${option.value}">${option.text}</label>
                        `;
                        attendeesDropdown.appendChild(optionDiv);
                    }
                });
                
                if (!hasMatches) {
                    const noResults = document.createElement('div');
                    noResults.className = 'text-muted p-2';
                    noResults.textContent = 'No matching attendees found';
                    attendeesDropdown.appendChild(noResults);
                }
            }
            
            populateAttendeesDropdown();
            
            // Handle attendee selection
            attendeesDropdown.addEventListener('change', function(e) {
                if (e.target.type === 'checkbox') {
                    updateAttendeesDisplay();
                }
            });
            
            function updateAttendeesDisplay() {
                const selectedAttendees = Array.from(attendeesDropdown.querySelectorAll('input[type="checkbox"]:checked')).map(cb => ({
                    value: cb.value,
                    text: cb.nextElementSibling.textContent
                }));
                
                attendeesDisplay.innerHTML = '';
                
                if (selectedAttendees.length === 0) {
                    attendeesDisplay.innerHTML = '<span class="no-attendees">Select attendees...</span>';
                } else {
                    selectedAttendees.forEach(attendee => {
                        const tag = document.createElement('div');
                        tag.className = 'attendee-tag';
                        tag.innerHTML = `
                            ${attendee.text}
                            <span class="attendee-tag-remove" onclick="removeAttendee('${attendee.value}', event)">
                                <i class="bi bi-x"></i>
                            </span>
                        `;
                        attendeesDisplay.appendChild(tag);
                    });
                }
                
                // Update the hidden select element
                Array.from(attendeesSelect.options).forEach(option => {
                    option.selected = selectedAttendees.some(a => a.value === option.value);
                });
            }
            
            window.toggleAttendeesDropdown = function() {
                attendeesDropdown.style.display = attendeesDropdown.style.display === 'block' ? 'none' : 'block';
                if (attendeesDropdown.style.display === 'block') {
                    document.getElementById('attendeeSearch').focus();
                }
            }
            
            window.removeAttendee = function(value, event) {
                if (event) event.stopPropagation();
                const checkbox = attendeesDropdown.querySelector(`input[value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = false;
                    updateAttendeesDisplay();
                }
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.attendees-dropdown')) {
                    attendeesDropdown.style.display = 'none';
                }
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
                    {
                        title: 'Team Meeting',
                        start: new Date(),
                        allDay: false,
                        extendedProps: {
                            type: 'Team Meeting',
                            attendees: 'John Doe, Jane Smith',
                            time: '10:00 AM',
                            place: 'Conference Room A',
                            notes: 'Discuss project updates',
                            creator: 'Admin'
                        },
                        backgroundColor: '#4e73df',
                        borderColor: '#4e73df'
                    },
                    {
                        title: 'Briefing',
                        start: new Date(new Date().getTime() + 86400000), // Tomorrow
                        allDay: false,
                        extendedProps: {
                            type: 'Briefing',
                            attendees: 'Michael Brown, Emily Davis',
                            time: '2:00 PM',
                            place: 'Office B',
                            notes: 'Prepare for client presentation',
                            creator: 'Admin'
                        },
                        backgroundColor: '#1cc88a',
                        borderColor: '#1cc88a'
                    }
                ],
                eventContent: function(arg) {
                    let eventEl = document.createElement('div');
                    eventEl.className = 'fc-event-main';
                    
                    // Format time for display
                    const time = arg.event.extendedProps.time || '';
                    const type = arg.event.extendedProps.type || '';
                    
                    eventEl.innerHTML = `
                        <div class="event-title">${arg.event.title}</div>
                        <div class="event-details">
                            <span class="event-time">${time}</span>
                            <span class="event-type">${type}</span>
                        </div>
                    `;
                    return { domNodes: [eventEl] };
                },
                eventClick: function(info) {
                    const event = info.event;
                    appointmentDetailsEl.innerHTML = `
                        <h6 class="text-primary mb-3">${event.title}</h6>
                        <div class="detail-item">
                            <div class="detail-label">Type</div>
                            <div class="detail-value">${event.extendedProps.type}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date & Time</div>
                            <div class="detail-value">${event.start.toLocaleDateString()} at ${event.extendedProps.time}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Location</div>
                            <div class="detail-value">${event.extendedProps.place}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Attendees</div>
                            <div class="detail-value">${event.extendedProps.attendees}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Created By</div>
                            <div class="detail-value">${event.extendedProps.creator}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Notes</div>
                            <div class="detail-value">${event.extendedProps.notes || 'No notes'}</div>
                        </div>
                    `;
                }
            });

            calendar.render();

            // Handle window resize for better responsiveness
            function handleResize() {
                calendar.updateSize();
                setTimeout(() => {
                    calendar.updateSize();
                }, 300);
            }

            window.addEventListener('resize', handleResize);
            document.querySelector('.sidebar-toggle')?.addEventListener('click', function() {
                setTimeout(handleResize, 300);
            });

            document.getElementById('submitAppointment').addEventListener('click', function(e) {
                e.preventDefault();
                
                const title = document.getElementById('appointmentTitle').value;
                const type = document.getElementById('appointmentType').value;
                const attendees = Array.from(attendeesSelect.selectedOptions).map(option => option.value);
                const date = document.getElementById('appointmentDate').value;
                const time = document.getElementById('appointmentTime').value;
                const place = document.getElementById('appointmentPlace').value;
                const notes = document.getElementById('appointmentNotes').value;

                if (!title || !type || attendees.length === 0 || !date || !time || !place) {
                    alert('Please fill in all required fields.');
                    return;
                }

                // Combine date and time
                const startDateTime = new Date(`${date}T${time}`);
                
                calendar.addEvent({
                    title: title,
                    start: startDateTime,
                    allDay: false,
                    extendedProps: {
                        type: type,
                        attendees: attendees.join(', '),
                        time: formatTime(time),
                        place: place,
                        notes: notes,
                        creator: 'Admin'
                    },
                    backgroundColor: getEventColor(type),
                    borderColor: getEventColor(type)
                });

                alert('Appointment scheduled successfully!');
                addAppointmentForm.reset();
                attendeesDisplay.innerHTML = '<span class="no-attendees">Select attendees...</span>';
                addAppointmentModal.hide();
            });
            
            function formatTime(timeString) {
                const [hours, minutes] = timeString.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour % 12 || 12;
                return `${hour12}:${minutes} ${ampm}`;
            }
            
            function getEventColor(type) {
                const colors = {
                    'Briefing': '#4e73df',
                    'Team Meeting': '#1cc88a',
                    'One-on-One': '#f6c23e',
                    'Training Session': '#e74a3b'
                };
                return colors[type] || '#6c757d';
            }
            
            // Set default date to today
            document.getElementById('appointmentDate').valueAsDate = new Date();
        });
    </script>
</body>
</html>