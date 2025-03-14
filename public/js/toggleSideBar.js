function toggleDropdown(element) {
    const dropdownParent = element.closest("li");
    dropdownParent.classList.toggle("showMenu");
  }
      let sidebar = document.querySelector(".sidebar");
      let logoToggle = document.getElementById("logoToggle");
      logoToggle.addEventListener("click", () => {
        sidebar.classList.toggle("close");
      });