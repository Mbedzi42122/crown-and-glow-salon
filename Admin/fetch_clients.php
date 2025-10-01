<?php
session_start();
include '../API/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

// Fetch clients (users with at least one booking)
$stmt = $conn->prepare("
    SELECT DISTINCT u.id, u.username, u.email, u.mobile
    FROM users u
    JOIN bookings b ON u.id = b.user_id
    ORDER BY u.username ASC
");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
    <table class="client-table">
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Total Bookings</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): 
                // Count total bookings per client
                $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_id = ?");
                $stmt_count->bind_param("i", $row['id']);
                $stmt_count->execute();
                $count_result = $stmt_count->get_result()->fetch_assoc();
                $stmt_count->close();
            ?>
            <tr>
                <td><?= htmlspecialchars($row['username']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['mobile']); ?></td>
                <td><?= $count_result['total']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No clients found.</p>
<?php endif; ?>
