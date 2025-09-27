<?php
// Start session so we can log in the user after registration
session_start();

// Include DB connection
include("../API/db_connect.php");

// Initialize message
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    //  Validate mobile number
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $message = "Mobile number must be exactly 10 digits and contain only numbers (South Africa format).";
    }
    //  Check passwords
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into database (removed dob)
        $sql = "INSERT INTO users (username, email, mobile, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $mobile, $hashedPassword);

        if ($stmt->execute()) {
            //  Account created successfully → log in user immediately
            $userId = $stmt->insert_id;

            $_SESSION["user_id"] = $userId;
            $_SESSION["username"] = $username;
            $_SESSION["email"] = $email;
            $_SESSION["role"] = "user";

            // Redirect to homepage
            header("Location: ../HOME/index.php");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
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
    <title>Create Account</title>
    
</head>
<body>
    <div class="desktop">
        
      
        <div class="container">
           

            <h1>Create Account</h1>

            <!-- Show message -->
            <?php if (!empty($message)) { ?>
                <p style="color:red;"><?php echo $message; ?></p>
            <?php } ?>

            <form class="form" method="POST" action="">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="tel" name="mobile" placeholder="Mobile Number (10 digits)" maxlength="10" pattern="[0-9]{10}" required>
                
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>

                <label class="terms">
                    <input type="checkbox" required>
                    By creating an account, I agree to the 
                    <a href="#">Terms of use</a> and understand that my information will be used as described on this page.
                </label>

                <button type="submit" class="btn">Create Account</button>
            </form>

            <p class="signin-text">
                Already have an account?
                <a href="../Sign in/index.php" class="signin-link">Sign in</a>
            </p>
        </div>
    </div>
</body>
</html>
