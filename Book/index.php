<?php
session_start();
include '../API/db_connect.php'; // your database connection

if(!isset($_SESSION['user_id'])) {
    header("Location: ../Sign in/index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $service = $_POST['service'];
  $date = $_POST['date'];
  $time = $_POST['time'];
  $notes = $_POST['notes'];

  $stmt = $conn->prepare("INSERT INTO bookings (user_id, service, booking_date, booking_time, notes) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param(
      "issss",
      $_SESSION['user_id'],
      $service,
      $date,
      $time,
      $notes
  );
  if($stmt->execute()){
      $successMessage = "Booking confirmed!";
  } else {
      $errorMessage = "Failed to save booking.";
  }
  $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book Now - Crown & King Hair Salon</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Account dropdown CSS (same as before) */
    .account { position: relative; display: flex; align-items: center; gap: 8px; color: #fff; font-weight: 700; cursor: pointer; }
    .account img { width: 35px; height: 35px; }
    .account-dropdown { display: none; position: absolute; top: 120%; right: 0; background: #222; border: 1px solid #555; padding: 10px; width: 200px; text-align: left; box-shadow: 0 2px 8px rgba(0,0,0,0.5); z-index: 1000; }
    .account-dropdown p { margin: 5px 0; font-size: 0.95rem; }
    .account-dropdown form { margin-top: 10px; }
    .account-dropdown button { width: 100%; padding: 8px 0; border: none; background: #cca46c; color: #222; font-weight: 700; cursor: pointer; border-radius: 5px; }
    .account-dropdown button:hover { background: #e0b865; }

    .success-message { color: #4caf50; font-weight: bold; margin-bottom: 10px; }
    .error-message { color: #f44336; font-weight: bold; margin-bottom: 10px; }
  </style>
</head>
<body class="booking-page">

  <!-- HEADER -->
  <header>
    <div class="container nav">
      <img src="img/logo.png" alt="Logo" class="logo">
      <nav>
        <a href="../Home/index.php">Home</a>
        <a href="../Home/index.php">Service</a>
        <a href="../Home/index.php">About</a>
        <a href="../Home/index.php">Contact</a>
        <a href="../Bookings/index.php">Bookings</a>
      </nav>

      <!-- ACCOUNT -->
      <div class="account" id="account">
        <img src="img/profile.png" alt="Account Icon">
        <span id="account-name">
          Hi <?php echo htmlspecialchars($_SESSION['username']); ?>
        </span>
        <div class="account-dropdown" id="account-dropdown">
          <p><strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
          <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
          <form action="../Logout/logout.php" method="POST">
            <button type="submit">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </header>

  <!-- BOOKING FORM SECTION -->
  <section class="booking-hero">
    <div class="booking-form">
      <h1>Book an Appointment</h1>

      <?php if(isset($successMessage)): ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
      <?php elseif(isset($errorMessage)): ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-grid">
          <div class="form-group">
            <label for="service">Service</label>
            <select id="service" name="service" required>
              <option value="">Select Service</option>
              <option value="men-haircut">Men’s Haircut</option>
              <option value="women-haircut">Women’s Haircut</option>
              <option value="coloring">Hair Coloring</option>
              <option value="treatment">Hair Treatment</option>
              <option value="styling">Styling</option>
            </select>
          </div>

          <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>
          </div>

          <div class="form-group">
            <label for="time">Time</label>
            <input type="time" id="time" name="time" required>
          </div>

          <div class="form-group">
            <label for="notes">Notes (Optional)</label>
            <textarea id="notes" name="notes" rows="3"></textarea>
          </div>
        </div>

        <button type="submit" class="btn">Confirm Booking</button>
      </form>
    </div>
  </section>

  <script>
    const account = document.getElementById('account');
    const dropdown = document.getElementById('account-dropdown');

    account.addEventListener('click', () => {
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
      if(!account.contains(e.target)){
        dropdown.style.display = 'none';
      }
    });
  </script>

</body>
</html>
