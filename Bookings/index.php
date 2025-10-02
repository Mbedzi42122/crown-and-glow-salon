<?php
session_start();
include '../API/db_connect.php'; // your database connection

if(!isset($_SESSION['user_id'])) {
    header("Location: ../Sign in/index.php");
    exit;
}

// Handle deletion of booking
if(isset($_POST['delete_booking_id'])){
    $delete_id = intval($_POST['delete_booking_id']);
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Fetch all bookings for this user
$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");

$stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date ASC, booking_time ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$upcoming = [];
$past = [];

while($row = $result->fetch_assoc()){
    if($row['booking_date'] >= $today){
        $upcoming[] = $row;
    } else {
        $past[] = $row;
    }
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Bookings - Crown & King Hair Salon</title>
  <link rel="stylesheet" href="book.css" />
  <style>
    /* Account dropdown CSS */
    .account { position: relative; display: flex; align-items: center; gap: 8px; color: #fff; font-weight: 700; cursor: pointer; }
    .account img { width: 35px; height: 35px; }
    .account-dropdown { display: none; position: absolute; top: 120%; right: 0; background: #222; border: 1px solid #555; padding: 10px; width: 200px; text-align: left; box-shadow: 0 2px 8px rgba(0,0,0,0.5); z-index: 1000; }
    .account-dropdown p { margin: 5px 0; font-size: 0.95rem; }
    .account-dropdown form { margin-top: 10px; }
    .account-dropdown button { width: 100%; padding: 8px 0; border: none; background: #cca46c; color: #222; font-weight: 700; cursor: pointer; border-radius: 5px; }
    .account-dropdown button:hover { background: #e0b865; }

    .booking-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    .booking-table th, .booking-table td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    .delete-btn { background: #f44336; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
    .delete-btn:hover { background: #d32f2f; }
  </style>
</head>
<body class="booking-page">

  <!-- HEADER -->
  <header>
    <div class="container nav">
      <img src="img/logo.png" alt="Logo" class="logo">
      <nav>
        <a href="../Home/index.php#home">Home</a>
        <a href="../Home/index.php#services">Service</a>
        <a href="../Home/index.php#about">About</a>
        <a href="../Home/index.php#contact">Contact</a>
        <a href="../Book/index.php">Book</a>
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

  <!-- BOOKINGS SECTION -->
  <section class="booking-hero">
    <div class="booking-form">
      <h1>My Bookings</h1>

      <!-- Upcoming Bookings -->
      <h2>Upcoming Bookings</h2>
      <?php if(count($upcoming) > 0): ?>
      <table class="booking-table">
        <thead>
          <tr>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Notes</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($upcoming as $booking): ?>
          <tr>
            <td><?php echo htmlspecialchars($booking['service']); ?></td>
            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
            <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
            <td><?php echo htmlspecialchars($booking['notes']); ?></td>
            <td>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="delete_booking_id" value="<?php echo $booking['id']; ?>">
                <button type="submit" class="delete-btn">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p>No upcoming bookings.</p>
      <?php endif; ?>

      <!-- Past Bookings -->
      <h2>Past Bookings</h2>
      <?php if(count($past) > 0): ?>
      <table class="booking-table">
        <thead>
          <tr>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($past as $booking): ?>
          <tr>
            <td><?php echo htmlspecialchars($booking['service']); ?></td>
            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
            <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
            <td><?php echo htmlspecialchars($booking['notes']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p>No past bookings.</p>
      <?php endif; ?>

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
