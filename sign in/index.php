<?php
// Start session
session_start();

// Include DB connection
include("../API/db_connect.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check in users table only
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user["password"])) {
            // Store user in session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = "user";

            // Redirect to homepage
            header("Location: ../HOME/index.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Email not found!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="desktop">
        
      

       
        <div class="container">
           

            <div class="form-section">
                <h1>Welcome</h1>
                <p class="subtitle">Sign in with your email address and password.</p>

                <!-- Show message -->
                <?php if (!empty($message)) { ?>
                    <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
                <?php } ?>

                <form class="form" method="POST" action="">
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="password" name="password" placeholder="Password" required>

                    <div class="form-options">
                        <label class="remember">
                            <input type="checkbox"> Remember me
                        </label>
                        <a href="../forgot password/index.php" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn">Sign In</button>
                </form>

                <p class="signup-text">
                    Don’t have an account? <a href="../create Account/index.php" class="signup-link">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
