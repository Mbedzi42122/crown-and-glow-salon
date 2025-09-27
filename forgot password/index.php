<?php
// Include DB connection
include("../API/db_connect.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    //  Validate mobile format (South Africa - 10 digits)
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $message = "Mobile number must be 10 digits (South Africa format).";
    }
    //  Check passwords match
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE email = ? AND mobile = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $mobile);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            //  Hash new password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            //  Update password in database
            $updateSql = "UPDATE users SET password = ? WHERE email = ? AND mobile = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("sss", $hashedPassword, $email, $mobile);

            if ($updateStmt->execute()) {
                $message = " Password updated successfully!";
            } else {
                $message = " Error updating password!";
            }

            $updateStmt->close();
        } else {
            $message = "No account found with that email and mobile number!";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="desktop">
   

        
        <div class="container">
       

            <div class="form-section">
                <h1>Forgot Password</h1>
                <p class="subtitle">Enter your details to reset your password.</p>

                <!-- Show message -->
                <?php if (!empty($message)) { ?>
                    <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
                <?php } ?>

                <form class="form" method="POST" action="">
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="tel" name="mobile" placeholder="Mobile Number (10 digits)" maxlength="10" pattern="[0-9]{10}" required>
                    <input type="password" name="password" placeholder="New Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>

                    <button type="submit" class="btn">Change Password</button>
                </form>

                <p class="signin-text">
                    <a href="../Sign in/index.php" class="signin-link">Back to Login</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
