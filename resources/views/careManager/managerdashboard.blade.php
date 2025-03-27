<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    @include('components.userNavbar')
    @include('components.careManagerSidebar')

     <!-- Welcome Back Modal -->
     <div class="modal fade" id="welcomeBackModal" tabindex="-1" aria-labelledby="welcomeBackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="welcomeBackModalLabel">Welcome Back!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class='bx bxs-party display-4 text-primary mb-3'></i>
                    <h4>Hello, {{ auth()->user()->name }}!</h4>
                    <p>Welcome back to your Care Manager Dashboard.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Get Started</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
      <div class="text-left">DASHBOARD CARE MANAGER</div>
        <div class="container-fluid text-center">
        <div class="row">
              <!-- Statistics Row -->
              <div class="col-12 col-lg-8" style="background-color: yellow;">
                  <div class="row" id="statistics">
                      <div class="col-6" id="caregiverStats" style="background-color: green;">
                          Caregiver Statistics
                      </div>
                      <div class="col-6" id="beneficiaryStats" style="background-color: blue;">
                          Beneficiary Statistics
                      </div>
                  </div>
                  <div class="row" id="recentReports" style="background-color: white;">
                      <div class="col-12" style="background-color: cyan;">
                          Recent Reports
                      </div>
                  </div>
              </div>
              <!-- Events and Performance Column -->
              <div class="col-12 col-lg-4 mt-3 mt-lg-0" style="background-color: red;">
                  <div class="row" id="events">
                      <div class="col-12" style="background-color: orange;">
                          Upcoming Events
                      </div>
                  </div>
                  <div class="row" id="performance">
                      <div class="col-12" style="background-color: pink;">
                          Caregiver Performance
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
   
    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Always show the welcome modal when the dashboard loads
            var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeBackModal'));
            welcomeModal.show();
        });
    </script>
</body>
</html>