<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
 <div class="sidebar">
      <div class="logo-details" id="logoToggle">
        <span class="logo_name">Menu</span>
      </div>
      <ul class="nav-links">
      <li>
          <a href="dashboard">
            <i class='bx bx-grid-alt'></i>
            <span class="link_name">Dashboard</span>
          </a>
          <ul class="sub-menu blank">
            <li><a class="link_name" href="#">Dashboard</a></li>
          </ul>
        </li>
        <li>
          <a href="reportsManagement">
            <i class='bx bx-group'></i>
            <span class="link_name">Reports Management</span>
          </a>
          <ul class="sub-menu blank">
            <li><a class="link_name" href="reportsManagement">Reports Management</a></li>
          </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a href="userManagement">
              <i class='bx bxs-user-account'></i>
              <span class="link_name" onclick="toggleDropdown(this)">User Management</span>
            </a>
            <i class='bx bxs-chevron-down arrow' onclick="toggleDropdown(this)"></i>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name">User Management</a></li>
            <li><a href="beneficiaryProfile">Beneficiary Profiles</a></li>
            <li><a href="familyProfile">Family or Relative Profiles</a></li>
            <li><a href="caregiverProfile">Caregiver Profiles</a></li>
            <li><a href="careManagerProfile">Care Manager Profiles</a></li>
            <li><a href="administratorProfile">Administrator Profiles</a></li>
          </ul>
        </li>
        <li>
          <a href="#">
            <i class='bx bx-map-alt'></i>
            <span class="link_name">Municipality</span>
          </a>
          <ul class="sub-menu blank">
            <li><a class="link_name" href="municipality">Municipality</a></li>
          </ul>
        </li>



      </ul>
    </div>