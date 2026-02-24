<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Maintenance Requests</title>
  <link rel="stylesheet" href="../css/maintenance.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="logo">
      <a href="homepage.html"><img src="../img/7.webp" alt="Apartment Logo"></a>
    </div>
    <ul class="nav-links">
      <li><a href="homepage.php">Home</a></li>
      <li><a href="payments.php">Payments</a></li>
      <li><a href="maintenance.php">Maintenance</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="index.php" class="login-btn">Login</a></li>
    </ul>
  </nav>

  <!-- Page Title -->
  <header class="page-header">
    <h1>MAINTENANCE REQUEST</h1>
    <p>Submit issues and track their progress.</p>
  </header>

  <!-- Request Form -->
  <section class="request-form">
    <h2>Submit a New Request</h2>
    <form id="maintenanceForm">
      <label>Problem :</label>
      <input type="text" id="issueTitle" required>

      <label>Description:</label>
      <textarea id="issueDesc" required></textarea>

      <label>Apartment Number:</label>
      <input type="text" id="apartmentNum" required>

      <label>Urgency:</label>
      <select id="urgency">
        <option>Low</option>
        <option>Medium</option>
        <option>High</option>
      </select>

      <button type="submit">Submit Request</button>
    </form>
  </section>

  <!-- Tenant Requests -->
  <section class="tenant-requests">
    <h2>Your Requests</h2>
    <table id="tenantTable">
      <tr>
        <th>Issue</th>
        <th>Description</th>
        <th>Apartment</th>
        <th>Urgency</th>
      </tr>
      <!-- Rows will be added dynamically -->
    </table>
  </section>

  <!-- Popup Notification -->
  <div id="popup" class="popup"></div>

  <!-- Footer -->
  <footer>
    <p>&copy; Bangue Apartment System — simplifying housing, payments, and maintenance for the Bangue community.</p>
  </footer>

  <!-- Integrated JavaScript -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.getElementById("maintenanceForm");
      const tenantTable = document.getElementById("tenantTable");
      const popup = document.getElementById("popup");

      // Load saved requests from localStorage
      let requests = JSON.parse(localStorage.getItem("requests")) || [];

      // Function to render requests in table
      function renderRequests() {
        tenantTable.innerHTML = `
          <tr>
            <th>Issue</th>
            <th>Description</th>
            <th>Apartment</th>
            <th>Urgency</th>
          </tr>
        `;
        requests.forEach((req) => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${req.title}</td>
            <td>${req.desc}</td>
            <td>${req.apartment}</td>
            <td>${req.urgency}</td>
          `;
          tenantTable.appendChild(row);
        });
      }

      // Popup function
      function showPopup(message, type="success") {
        popup.textContent = message;
        popup.className = "popup " + type;
        popup.style.display = "flex";
        popup.style.opacity = "1";

        setTimeout(() => {
          popup.style.opacity = "0";
          setTimeout(() => { popup.style.display = "none"; }, 500);
        }, 5000);
      }

      // Handle form submission
      form.addEventListener("submit", (e) => {
        e.preventDefault();

        const title = document.getElementById("issueTitle").value.trim();
        const desc = document.getElementById("issueDesc").value.trim();
        const apartment = document.getElementById("apartmentNum").value.trim();
        const urgency = document.getElementById("urgency").value;

        if (!title || !desc || !apartment) {
          showPopup("❌ Please fill in all fields before submitting.", "error");
          return;
        }

        const newRequest = { title, desc, apartment, urgency };
        requests.push(newRequest);
        localStorage.setItem("requests", JSON.stringify(requests));

        renderRequests();
        form.reset();
        showPopup("✅ Your maintenance request has been submitted successfully!");
      });

      // Initial render
      renderRequests();
    });
  </script>
</body>
</html>
