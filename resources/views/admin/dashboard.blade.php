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

    @include('components.navbar')
    @include('components.sidebar')

    <div class="home-section">
    <div class="text-left">DASHBOARD</div>
      <div class="container-fluid text-center">
        
        <div class="row">
            <div class="col" style="background-color: yellow;">
                <div class="row" id="statistics">
                    <div class="col"  style="background-color: green;">
                    Caregiver Statistics
                    </div>
                    <div class="col" id="beneficiaryStats" style="background-color: blue;">
                    Beneficiary Statistics
                    </div>
                </div>
                <div class="row" id="recentReports" style="background-color: white;">
                    <div class="col">
                    Recent Reports
                    </div>
                </div>
            </div>
          <div class="col-4" style="background-color: red;">
            <div class="row" id="events">
              <div class="col" style="background-color: orange;">
                Upcoming Events
              </div>
            </div>
            <div class="row" id="performance">
              <div class="col" style="background-color: pink;">
                Caregiver Performance
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
   
    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>