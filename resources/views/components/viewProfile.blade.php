<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfile.css') }}">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    @include('components.userNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h4 class="text-center">USER PROFILE</h4>
                </div>
            </div>
            
            <div class="row" id="viewProfile">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="breadcrumb-container">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" data-section="personal"><a href="javascript:void(0)">Personal</a></li>
                                    <li class="breadcrumb-item" data-section="contact"><a href="javascript:void(0)">Contact</a></li>
                                    <li class="breadcrumb-item" data-section="professional"><a href="javascript:void(0)">Professional</a></li>
                                    <li class="breadcrumb-item" data-section="education"><a href="javascript:void(0)">Education</a></li>
                                    <li class="breadcrumb-item" data-section="settings"><a href="javascript:void(0)">Settings</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    
                    <!-- Profile Sections -->
                    <div class="profile-sections">
                        <!-- Personal Information Section -->
                        <div class="profile-section active" id="personal-section">
                            <div class="profile-header">
                                <h5>Personal Information</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <img src="{{ asset('images/defaultProfile.png') }}" alt="Profile Photo" class="profile-photo">
                                    <h5>John Doe</h5>
                                    <p class="text-muted">Member since: Jan 2020</p>
                                </div>
                                <div class="col-md-9">
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Full Name:</div>
                                        <div class="col-md-9">John Michael Doe</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Date of Birth:</div>
                                        <div class="col-md-9">January 15, 1985</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Gender:</div>
                                        <div class="col-md-9">Male</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Nationality:</div>
                                        <div class="col-md-9">American</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Marital Status:</div>
                                        <div class="col-md-9">Married</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Biography:</div>
                                        <div class="col-md-9">
                                            Experienced healthcare professional with over 10 years in patient care and administration. 
                                            Specialized in geriatric care and rehabilitation services.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information Section -->
                        <div class="profile-section" id="contact-section">
                            <div class="profile-header">
                                <h5>Contact Information</h5>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Email:</div>
                                <div class="col-md-9">john.doe@example.com</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Phone:</div>
                                <div class="col-md-9">(555) 123-4567</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Mobile:</div>
                                <div class="col-md-9">(555) 987-6543</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Address:</div>
                                <div class="col-md-9">
                                    123 Main Street<br>
                                    Apt 4B<br>
                                    Springfield, IL 62704<br>
                                    United States
                                </div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Emergency Contact:</div>
                                <div class="col-md-9">
                                    Jane Doe (Spouse)<br>
                                    (555) 234-5678
                                </div>
                            </div>
                        </div>
                        
                        <!-- Professional Information Section -->
                        <div class="profile-section" id="professional-section">
                            <div class="profile-header">
                                <h5>Professional Information</h5>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Position:</div>
                                <div class="col-md-9">Senior Care Coordinator</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Department:</div>
                                <div class="col-md-9">Geriatric Care</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Employee ID:</div>
                                <div class="col-md-9">HC-2020-0042</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Hire Date:</div>
                                <div class="col-md-9">March 15, 2020</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Specializations:</div>
                                <div class="col-md-9">
                                    <ul>
                                        <li>Geriatric Care</li>
                                        <li>Physical Rehabilitation</li>
                                        <li>Patient Care Coordination</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Certifications:</div>
                                <div class="col-md-9">
                                    <ul>
                                        <li>Certified Nursing Assistant (CNA)</li>
                                        <li>CPR/First Aid Certified</li>
                                        <li>Dementia Care Specialist</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Education Section -->
                        <div class="profile-section" id="education-section">
                            <div class="profile-header">
                                <h5>Education & Training</h5>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Highest Degree:</div>
                                <div class="col-md-9">Bachelor of Science in Nursing</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">University:</div>
                                <div class="col-md-9">University of Illinois at Chicago</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Graduation Year:</div>
                                <div class="col-md-9">2010</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Additional Training:</div>
                                <div class="col-md-9">
                                    <div class="mb-2">
                                        <strong>Advanced Geriatric Care</strong><br>
                                        American Geriatrics Society, 2015
                                    </div>
                                    <div class="mb-2">
                                        <strong>Palliative Care Certification</strong><br>
                                        Center to Advance Palliative Care, 2017
                                    </div>
                                    <div>
                                        <strong>Patient Safety Training</strong><br>
                                        Institute for Healthcare Improvement, 2019
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Settings Section -->
                        <div class="profile-section" id="settings-section">
                            <div class="profile-header">
                                <h5>Account Settings</h5>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Username:</div>
                                <div class="col-md-9">johndoe</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Account Status:</div>
                                <div class="col-md-9"><span class="badge bg-success">Active</span></div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Last Login:</div>
                                <div class="col-md-9">Today at 09:42 AM</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Two-Factor Auth:</div>
                                <div class="col-md-9">Enabled (SMS)</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Notification Preferences:</div>
                                <div class="col-md-9">
                                    <ul>
                                        <li>Email: On</li>
                                        <li>SMS: On (urgent only)</li>
                                        <li>Push Notifications: Off</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Language:</div>
                                <div class="col-md-9">English (US)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script>
        // Profile section navigation
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.profile-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show the selected section
            document.getElementById(sectionId + '-section').classList.add('active');
            
            // Update breadcrumb active state
            document.querySelectorAll('.breadcrumb-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.section === sectionId) {
                    item.classList.add('active');
                    
                    // Scroll the active breadcrumb item into view
                    setTimeout(() => {
                        const breadcrumbContainer = document.querySelector('.breadcrumb-container');
                        const activeItem = item;
                        
                        // Calculate scroll position to center the active item
                        const containerWidth = breadcrumbContainer.offsetWidth;
                        const itemLeft = activeItem.offsetLeft;
                        const itemWidth = activeItem.offsetWidth;
                        const scrollPosition = itemLeft - (containerWidth / 2) + (itemWidth / 2);
                        
                        // Smooth scroll to the position
                        breadcrumbContainer.scrollTo({
                            left: Math.max(0, scrollPosition),
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            });
            
            // Scroll to top of content
            window.scrollTo(0, 0);
        }
        
        // Breadcrumb navigation
        document.querySelectorAll('.breadcrumb-item').forEach(item => {
            item.addEventListener('click', function() {
                showSection(this.dataset.section);
            });
        });
    </script>

</body>
</html>