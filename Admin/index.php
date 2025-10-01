<?php
session_start();
include '../API/db_connect.php'; // database connection file

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../SignIn/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crown & Glow Admin Dashboard</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="dashboard">
      <!-- Sidebar -->
      <aside class="sidebar">
        <div class="logo-container">
          <img src="img/logo.png" alt="Crown & Glow Logo" class="logo" />
          
        </div>
        <nav class="nav">
          <button class="nav-item active" onclick="showSection('bookings')">BOOKINGS</button>
          <button class="nav-item" onclick="showSection('clients')">CLIENTS</button>
          <button class="nav-item" onclick="showSection('services')">SERVICES</button>
          <button class="nav-item" onclick="showSection('settings')">SETTINGS</button>
        </nav>
      </aside>

      <!-- Main Content -->
      <main class="content">
        <header class="topbar">
          <h2 id="section-title">BOOKINGS</h2>
          <div class="account" id="account">
            <img src="img/profile.png" alt="Profile" class="profile" />
            <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <div class="account-dropdown" id="account-dropdown">
              <p><strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong></p>
              <p><?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
              <form action="../Logout/logout.php" method="POST">
                <button type="submit">Logout</button>
              </form>
            </div>
          </div>
        </header>

        <!-- Sections -->
        <section id="bookings" class="section active">
          <div id="bookings-content">Loading bookings...</div>
        </section>

        <section id="clients" class="section">
          <p>Manage clients here.</p>
        </section>

        <section id="services" class="section">
          <p>Manage salon services and pricing here.</p>
        </section>

        <section id="settings" class="section">
          <p>Admin settings and preferences.</p>
        </section>
      </main>
    </div>

    <script src="script.js"></script>
  </body>
</html>
