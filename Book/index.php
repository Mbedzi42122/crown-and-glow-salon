<?php
session_start();
include '../API/db_connect.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../Sign in/index.php");
    exit;
}

// Config
$slot_duration = 60; // minutes
$start_hour = 8;
$end_hour = 18; // 6 PM
$services = ["Men’s Haircut", "Women’s Haircut", "Coloring", "Treatment", "Styling"];

// Pagination (1 day)
$day_offset = isset($_GET['day_offset']) ? intval($_GET['day_offset']) : 0;
if ($day_offset < 0) $day_offset = 0; // Prevent going back to past days
$current_date = date('Y-m-d', strtotime("+$day_offset days"));
$day_of_week = date('N', strtotime($current_date)); // 1=Mon, 7=Sun

// Handle booking
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'], $_POST['time'], $_POST['service'])){
    $date = $_POST['date'];
    $time = $_POST['time'];
    $service = $_POST['service'];

    // Prevent weekend booking
    $day_of_week = date('N', strtotime($date));
    if($day_of_week >= 6) {
        $errorMessage = "Salon is closed on weekends!";
    } else {
        $datetime = strtotime($date . ' ' . $time);
        if ($datetime < time()) {
            $errorMessage = "You cannot book a past date or time!";
        } else {
            // Check if slot already taken
            $check = $conn->prepare("SELECT id FROM bookings WHERE booking_date=? AND booking_time=? AND service=?");
            $check->bind_param("sss", $date, $time, $service);
            $check->execute();
            $check->store_result();

            if($check->num_rows > 0){
                $errorMessage = "That slot is already booked!";
            } else {
                $stmt = $conn->prepare("INSERT INTO bookings (user_id, service, booking_date, booking_time) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $_SESSION['user_id'], $service, $date, $time);
                if($stmt->execute()) $successMessage = "Booking confirmed!";
                else $errorMessage = "Failed to create booking.";
                $stmt->close();
            }
            $check->close();
        }
    }
}

// Fetch booked slots for that day
$booked = [];
$stmt = $conn->prepare("
    SELECT booking_time, service, u.username 
    FROM bookings b 
    JOIN users u ON b.user_id=u.id 
    WHERE booking_date=?
");
$stmt->bind_param("s", $current_date);
$stmt->execute();
$result = $stmt->get_result();
while($r = $result->fetch_assoc()){
    $booked[$r['service']][$r['booking_time']] = $r['username'];
}
$stmt->close();

function generate_times($start, $end, $duration){
    $times = [];
    for($h=$start*60; $h<$end*60; $h+=$duration){
        $times[] = sprintf("%02d:%02d:00", floor($h/60), $h%60);
    }
    return $times;
}
$times = generate_times($start_hour, $end_hour, $slot_duration);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Salon Booking Scheduler</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { background:#444; color:#fff; font-family:sans-serif; margin:0; }
header { background:#222; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; }
header img.logo { height:45px; }
header nav a { color:#fff; text-decoration:none; margin:0 10px; font-weight:bold; }
header nav a:hover { color:#cca46c; }
.account { position:relative; display:flex; align-items:center; gap:8px; color:#fff; font-weight:700; cursor:pointer; }
.account img { width:35px; height:35px; }
.account-dropdown { display:none; position:absolute; top:120%; right:0; background:#222; border:1px solid #555; padding:10px; width:200px; text-align:left; box-shadow:0 2px 8px rgba(0,0,0,0.5); z-index:1000; }
.account-dropdown p { margin:5px 0; font-size:0.95rem; }
.account-dropdown button { width:100%; padding:8px 0; border:none; background:#cca46c; color:#222; font-weight:700; cursor:pointer; border-radius:5px; }
.account-dropdown button:hover { background:#e0b865; }

.scheduler { overflow-x:auto; margin:20px; border:1px solid #666; border-radius:8px; background:#333; }
table { border-collapse:collapse; width:100%; min-width:800px; }
th, td { border:1px solid #555; padding:6px; text-align:center; }
th { background:#222; position:sticky; top:0; }
.slot { height:30px; cursor:pointer; border-radius:4px; }
.available { background:#4caf50; }
.booked { background:#f44336; cursor:not-allowed; }
.past { background:#777; cursor:not-allowed; }
.slot:hover.available { opacity:0.8; }
.legend { margin:10px 20px; }
.legend span { display:inline-block; margin-right:10px; padding:5px 10px; border-radius:4px; }
.available-swatch { background:#4caf50; }
.booked-swatch { background:#f44336; }
.past-swatch { background:#777; }
.pagination { text-align:center; margin-top:10px; }
.pagination a { color:#cca46c; text-decoration:none; margin:0 10px; font-weight:bold; }
.tooltip { position:absolute; background:#222; color:#fff; padding:5px 8px; border-radius:4px; font-size:12px; display:none; z-index:999; }
.success { color:#4caf50; text-align:center; }
.error { color:#f44336; text-align:center; }
.closed { background:#222; padding:20px; text-align:center; font-size:1.2rem; color:#cca46c; border:1px solid #555; border-radius:8px; margin:20px; }
</style>
</head>
<body>

<header>
  <img src="img/logo.png" alt="Logo" class="logo">
  <nav>
    <a href="../Home/index.php#home">Home</a>
    <a href="../Home/index.php#services">Service</a>
    <a href="../Home/index.php#about">About</a>
    <a href="../Home/index.php#contact">Contact</a>
    <a href="../Bookings/index.php">Bookings</a>
  </nav>
  <div class="account" id="account">
    <img src="img/profile.png" alt="Account Icon">
    <span>Hi <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    <div class="account-dropdown" id="account-dropdown">
      <p><strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
      <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
      <form action="../Logout/logout.php" method="POST">
        <button type="submit">Logout</button>
      </form>
    </div>
  </div>
</header>

<h1 style="text-align:center; margin-top:15px;">Book Your Salon Slot</h1>
<?php if(isset($successMessage)) echo "<p class='success'>$successMessage</p>"; ?>
<?php if(isset($errorMessage)) echo "<p class='error'>$errorMessage</p>"; ?>

<div class="pagination">
  <?php if($day_offset > 0): ?>
    <a href="?day_offset=<?php echo $day_offset-1; ?>">&laquo; Previous</a>
  <?php endif; ?>
  <strong><?php echo date('l, F j, Y', strtotime($current_date)); ?></strong>
  <a href="?day_offset=<?php echo $day_offset+1; ?>">Next &raquo;</a>
</div>

<form method="POST" id="bookingForm">
<input type="hidden" name="date" id="dateField">
<input type="hidden" name="time" id="timeField">
<input type="hidden" name="service" id="serviceField">

<?php if($day_of_week >= 6): ?>
  <div class="closed">Salon Closed on Weekends (Saturday & Sunday)</div>
<?php else: ?>
  <div class="scheduler">
    <table>
      <thead>
        <tr>
          <th>Service</th>
          <?php foreach($times as $t): ?>
            <th><?php echo date('g:i A', strtotime($t)); ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($services as $srv): ?>
          <tr>
            <td><?php echo htmlspecialchars($srv); ?></td>
            <?php 
            foreach($times as $t): 
              $isBooked = isset($booked[$srv][$t]);
              $isPast = false;
              if ($current_date == date('Y-m-d') && strtotime($t) <= time()) {
                  $isPast = true;
              }
            ?>
              <td>
                <?php if($isBooked): ?>
                  <div class="slot booked" title="Booked by <?php echo htmlspecialchars($booked[$srv][$t]); ?>"></div>
                <?php elseif($isPast): ?>
                  <div class="slot past" title="Past Time"></div>
                <?php else: ?>
                  <div class="slot available" onclick="bookSlot('<?php echo $current_date; ?>','<?php echo $t; ?>','<?php echo htmlspecialchars($srv); ?>')"></div>
                <?php endif; ?>
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
</form>

<div class="legend">
  <span class="available-swatch">Available</span>
  <span class="booked-swatch">Booked</span>
  <span class="past-swatch">Past</span>
</div>

<div class="tooltip" id="tooltip"></div>

<script>
const account = document.getElementById('account');
const dropdown = document.getElementById('account-dropdown');
account.addEventListener('click', () => {
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});
document.addEventListener('click', e => {
  if(!account.contains(e.target)) dropdown.style.display = 'none';
});

const tooltip = document.getElementById('tooltip');
document.querySelectorAll('.booked, .past').forEach(el=>{
  el.addEventListener('mouseenter',e=>{
    tooltip.innerText = el.title;
    tooltip.style.display = 'block';
    tooltip.style.left = (e.pageX+10)+'px';
    tooltip.style.top = (e.pageY+10)+'px';
  });
  el.addEventListener('mouseleave',()=> tooltip.style.display='none');
});

function bookSlot(date,time,service){
  if(confirm(`Book ${service} on ${date} at ${time}?`)){
    document.getElementById('dateField').value=date;
    document.getElementById('timeField').value=time;
    document.getElementById('serviceField').value=service;
    document.getElementById('bookingForm').submit();
  }
}
</script>

</body>
</html>
