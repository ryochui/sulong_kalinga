<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <div class="sidebar">
      <div class="logo-details" id="logoToggle">
        <i class='bx bx-menu'></i>
        <span class="logo_name">Menu</span>
      </div>
      <ul class="nav-links">
        <li>
          <a href="/manager/dashboard" class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <i class='bx bx-grid-alt'></i>
            <span class="link_name">Dashboard</span>
          </a>
          <ul class="sub-menu blank">
            <li><a class="link_name" href="dashboard">Dashboard</a></li>
          </ul>
        </li>
        <li>
        <a href="{{ route('reports') }}" class="{{ Request::routeIs('reports') ? 'active' : '' }}">
            <i class='bx bx-file'></i>
            <span class="link_name">Reports Management</span>
        </a>
        <ul class="sub-menu blank">
            <li><a class="link_name" href="{{ route('reports') }}">Reports Management</a></li>
        </ul>
        </li>
        <li>
          <div class="iocn-link">
            <a class="{{ Request::routeIs('beneficiaryProfile') || Request::routeIs('addBeneficiary') || Request::routeIs('familyProfile') || Request::routeIs('addFamily') || Request::routeIs('caregiverProfile') || Request::routeIs('addCareworker') || Request::routeIs('careManagerProfile') || Request::routeIs('addCareManager') || Request::routeIs('administratorProfile') || Request::routeIs('addAdministrator') ? 'active' : '' }}">
              <i class='bx bxs-user-account'></i>
              <span class="link_name" onclick="toggleDropdown(this)">User Management</span>
              <i class='bx bxs-chevron-down arrow' onclick="toggleDropdown(this)"></i>
            </a>
          </div>
          <ul class="sub-menu">
            <li><a class="link_name">User Management</a></li>
            <li><a href="beneficiaryProfile" class="{{ Request::routeIs('beneficiaryProfile') || Request::routeIs('addBeneficiary') ? 'active' : '' }}">Beneficiary Profiles</a></li>
            <li><a href="familyProfile" class="{{ Request::routeIs('familyProfile') || Request::routeIs('addFamily') ? 'active' : '' }}">Family or Relative Profiles</a></li>
            <li><a href="careWorkerProfile" class="{{ Request::routeIs('careWorkerProfile') || Request::routeIs('addCareworker') ? 'active' : '' }}">Care Worker Profiles</a></li>
            <li><a href="careManagerProfile" class="{{ Request::routeIs('careManagerProfile') || Request::routeIs('addCareManager') ? 'active' : '' }}">Care Manager Profiles</a></li>
          </ul>
        </li>
        <li>
          <a href="municipality" class="{{ Request::routeIs('municipality') ? 'active' : '' }}">
            <i class='bx bx-map-alt'></i>
            <span class="link_name">Municipality</span>
          </a>
          <ul class="sub-menu blank">
            <li><a class="link_name" href="municipality">Municipality</a></li>
          </ul>
        </li>
      </ul>
    </div>

 <script>
  function handleSidebarState() {
      const sidebar = document.querySelector(".sidebar");
      const contentSection = document.getElementById("content-section");

      if (window.innerWidth <= 768) {
          sidebar.classList.add("close");
      } else {
          sidebar.classList.remove("close");
      }
  }
  window.addEventListener("load", handleSidebarState);
  window.addEventListener("resize", handleSidebarState);
 </script>   