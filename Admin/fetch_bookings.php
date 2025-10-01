<?php
session_start();
include '../API/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

// Delete booking if requested
if (isset($_POST['delete_booking_id'])) {
    $delete_id = intval($_POST['delete_booking_id']);
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all bookings (admin can see all)
$stmt = $conn->prepare("
  SELECT b.id, b.service, b.booking_date, b.booking_time, b.notes, u.username, u.email
  FROM bookings b
  JOIN users u ON b.user_id = u.id
  ORDER BY b.booking_date DESC, b.booking_time DESC
");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
  <table class="booking-table">
    <thead>
      <tr>
        <th>Client</th>
        <th>Email</th>
        <th>Service</th>
        <th>Date</th>
        <th>Time</th>
        <th>Notes</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['username']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>
        <td><?= htmlspecialchars($row['service']); ?></td>
        <td><?= htmlspecialchars($row['booking_date']); ?></td>
        <td><?= htmlspecialchars($row['booking_time']); ?></td>
        <td><?= htmlspecialchars($row['notes']); ?></td>
        <td>
          <form method="POST" onsubmit="return confirm('Delete this booking?');">
            <input type="hidden" name="delete_booking_id" value="<?= $row['id']; ?>">
            <button type="submit" class="delete-btn">Delete</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No bookings found.</p>
<?php endif; ?>
