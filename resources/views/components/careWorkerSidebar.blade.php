<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

<div class="sidebar">
  <div class="logo-details" id="logoToggle">
    <i class='bx bx-menu'></i>
    <span class="logo_name">Menu</span>
  </div>
  <ul class="nav-links">
    <li>
      <a href="/admin/dashboard" class="{{ Request::routeIs('admin/dashboard') ? 'active' : '' }}">
        <i class='bx bx-grid-alt'></i>
        <span class="link_name">Dashboard</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="/admin/dashboard" >Dashboard</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.reports') }}" class="{{ Request::routeIs('admin.reports') ? 'active' : '' }}">
        <i class='bx bx-file'></i>
        <span class="link_name">Reports Management</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.reports') }}">Reports Management</a></li>
      </ul>
    </li>
    <li>
      <div class="iocn-link">
        <a class="{{ Request::is('admin/beneficiaries*') || Request::is('admin/families*') || Request::is('admin/care-workers*') || Request::is('admin/care-managers*') || Request::is('admin/administrators*') ? 'active' : '' }}">
          <i class='bx bxs-user-account'></i>
          <span class="link_name" onclick="toggleDropdown(this)">User Management</span>
          <i class='bx bxs-chevron-down arrow' onclick="toggleDropdown(this)"></i>
        </a>
      </div>
      <ul class="sub-menu">
        <li><a class="link_name">User Management</a></li>
        <li><a href="{{ route('admin.beneficiaries.index') }}" class="{{ Request::routeIs('admin.beneficiaryProfile') || Request::routeIs('admin.addBeneficiary') ? 'active' : '' }}">Beneficiary Profiles</a></li>
        <li><a href="{{ route('admin.families.index') }}" class="{{ Request::routeIs('admin.familyProfile') || Request::routeIs('admin.addFamily') ? 'active' : '' }}">Family or Relative Profiles</a></li>
        <li><a href="{{ route('admin.careworkers.index') }}" class="{{ Request::routeIs('admin.careWorkerProfile') || Request::routeIs('admin.addCareWorker*') ? 'active' : '' }}">Care Worker Profiles</a></li>
        <li><a href="{{ route('admin.caremanagers.index') }}" class="{{ Request::routeIs('admin.careManagerProfile') || Request::routeIs('admin.addCareManager') ? 'active' : '' }}">Care Manager Profiles</a></li>
        <li>
          <a href="{{ route('admin.administrators.index') }}" class="{{ Request::routeIs('administratorProfile') || Request::routeIs('admin.addAdministrator') ? 'active' : '' }}">
            Administrator Profiles
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.locations.index') }}" class="{{ Request::routeIs('admin.locations.*') ? 'active' : '' }}">
        <i class='bx bx-map-alt'></i>
        <span class="link_name">Municipality</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.locations.index') }}">Municipality</a></li>
      </ul>
    </li>
    <li>
      <a href="{{ route('admin.weeklycareplans.create') }}" class="{{ Request::routeIs('admin.weeklycareplans.*') ? 'active' : '' }}">
        <i class='bx bx-task'></i>
        <span class="link_name">Weekly Care Plan</span>
      </a>
      <ul class="sub-menu blank">
        <li><a class="link_name" href="{{ route('admin.weeklycareplans.create') }}">Weekly Care Plan</a></li>
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

  function toggleDropdown(element) {
    const parent = element.closest('li');
    parent.classList.toggle('showMenu');
  }
</script>