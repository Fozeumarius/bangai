<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payments</title>
  <link rel="stylesheet" href="../css/payment.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="logo">
      <a href="index.html"><img src="../img/7.webp" alt="Apartment Logo"></a>
    </div>
    <ul class="nav-links">
      <li><a href="homepage.php">Home</a></li>
      <li><a href="payment.php">Payments</a></li>
      <li><a href="maintenance.php">Maintenance</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="index.php" class="login-btn">Login</a></li>
    </ul>
  </nav>

  <!-- Page Header -->
  <header class="page-header">
    <h1>PAYMENTS</h1>
    <p>Manage your rent and track payment history.</p>
  </header>

  <!-- Current Payment Section -->
  <section class="payment-section">
    <h2>Current Rent</h2>
    <div class="payment-card">
      <p><strong>Apartment:</strong> </p>
      <p><strong>Tenant:</strong> </p>
      <p><strong>Due Date:</strong> <span id="dueDate"></span></p>

      <!-- User Inputs -->
      <label for="accountName">Account Name:</label>
      <input type="text" id="accountName" placeholder="Enter your name" required>

      <label for="accountNumber">Account Number / Phone:</label>
      <input type="text" id="accountNumber" placeholder="Enter your number" required>

      <label for="amountInput">Amount (FCFA):</label>
      <input type="number" id="amountInput" placeholder="Enter amount" required>

      <!-- Payment Method Selection -->
      <label for="paymentMethod">Choose Payment Method:</label>
      <select id="paymentMethod" class="payment-method">
        <option value="mtn" class="mtn">MTN Mobile Money</option>
        <option value="orange" class="orange">Orange Money</option>
      </select>

      <button class="btn" id="payBtn">Pay Now</button>
    </div>
  </section>

  <!-- Payment History Section -->
  <section class="payment-history">
    <h2>Payment History</h2>
    <table id="historyTable">
      <thead>
        <tr>
          <th>Date</th>
          <th>Account</th>
          <th>Amount</th>
          <th>Payment Mode</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="historyBody">
        <!-- Rows will be added dynamically -->
      </tbody>
    </table>
  </section>

  <!-- Notification Popup -->
  <div id="popup" class="popup"></div>

  <!-- Footer -->
  <footer>
    <p>&copy; Bangue Apartment System — simplifying housing, payments, and maintenance for the Bangue community.</p>
  </footer>

  <!-- Integrated JavaScript -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const payBtn = document.getElementById("payBtn");
      const dueDate = document.getElementById("dueDate");
      const historyBody = document.getElementById("historyBody");
      const popup = document.getElementById("popup");

      // Load saved payments from localStorage
      let payments = JSON.parse(localStorage.getItem("payments")) || [];

      // Render payments into table
      function renderPayments() {
        historyBody.innerHTML = "";
        payments.forEach(payment => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${payment.date}</td>
            <td>${payment.accountName} (${payment.accountNumber})</td>
            <td>${payment.amount} FCFA</td>
            <td><span class="status ${payment.method}">Paid via ${payment.method.toUpperCase()}</span></td>
            <td><span class="status paid">Paid ✔</span></td>
          `;
          historyBody.appendChild(row);
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

      // Handle payment submission
      payBtn.addEventListener("click", () => {
        const accountName = document.getElementById("accountName").value.trim();
        const accountNumber = document.getElementById("accountNumber").value.trim();
        const amount = document.getElementById("amountInput").value.trim();
        const method = document.getElementById("paymentMethod").value;
        const date = new Date().toLocaleDateString();

        if (!accountName || !accountNumber || !amount) {
          showPopup("❌ Please fill in all fields before proceeding.", "error");
          return;
        }

        const newPayment = { date, accountName, accountNumber, amount, method };
        payments.push(newPayment);
        localStorage.setItem("payments", JSON.stringify(payments));

        renderPayments();
        showPopup("✅ Payment recorded successfully!", "success");

        // Reset form
        document.getElementById("accountName").value = "";
        document.getElementById("accountNumber").value = "";
        document.getElementById("amountInput").value = "";
        payBtn.disabled = true;
        payBtn.textContent = "Paid ✔";
        dueDate.textContent = "No payment due";
      });

      // Initial render
      renderPayments();
    });
  </script>
</body>
</html>
