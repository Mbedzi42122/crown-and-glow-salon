// Toggle sections
function showSection(sectionId) {
    const sections = document.querySelectorAll(".section");
    sections.forEach((s) => s.classList.remove("active"));
    document.getElementById(sectionId).classList.add("active");
  
    const navItems = document.querySelectorAll(".nav-item");
    navItems.forEach((btn) => btn.classList.remove("active"));
    event.target.classList.add("active");
  
    document.getElementById("section-title").textContent =
      sectionId.charAt(0).toUpperCase() + sectionId.slice(1);
  
    // If BOOKINGS is selected, fetch bookings via AJAX
    if (sectionId === "bookings") {
      fetchBookings();
    }
  }
  
  // Fetch bookings for admin view
  function fetchBookings() {
    fetch("fetch_bookings.php")
      .then((res) => res.text())
      .then((html) => {
        document.getElementById("bookings-content").innerHTML = html;
      })
      .catch(() => {
        document.getElementById("bookings-content").innerHTML =
          "<p>Error loading bookings.</p>";
      });
  }
  
  // Account dropdown toggle
  const account = document.getElementById("account");
  const dropdown = document.getElementById("account-dropdown");
  
  account.addEventListener("click", () => {
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
  });
  
  document.addEventListener("click", (e) => {
    if (!account.contains(e.target)) {
      dropdown.style.display = "none";
    }
  });
  
  // Load bookings on page load
  document.addEventListener("DOMContentLoaded", fetchBookings);
  