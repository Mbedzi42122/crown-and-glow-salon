<?php
// Start session
session_start();

// Include DB connection
include("../API/db_connect.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // === 1️⃣ Try to log in as ADMIN first ===
    $sql_admin = "SELECT * FROM admins WHERE email = ?";
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("s", $email);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows === 1) {
        $admin = $result_admin->fetch_assoc();

        // ❗Admin passwords are NOT hashed
        if ($password === $admin["password"]) {
            // ✅ Successful admin login
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["username"];
            $_SESSION["admin_email"] = $admin["email"];
            $_SESSION["role"] = "admin";

            header("Location: ../Admin/index.php");
            exit();
        } else {
            $message = "Invalid admin password!";
        }
    } else {
        // === 2️⃣ Try to log in as REGULAR USER ===
        $sql_user = "SELECT * FROM users WHERE email = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("s", $email);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows === 1) {
            $user = $result_user->fetch_assoc();

            // ✅ Users still use hashed passwords
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = "user";

                header("Location: ../HOME/index.php");
                exit();
            } else {
                $message = "Invalid user password!";
            }
        } else {
            $message = "Email not found!";
        }
        $stmt_user->close();
    }

    $stmt_admin->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crown & Glow | Sign In</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="container">
      <!-- Left Logo Section -->
      <div class="logo-section">
        <img src="img/logo.png" alt="Crown & Glow Hair Salon Logo" />
      </div>

      <!-- Right Form Section -->
      <div class="form-section">
        <h1>Welcome</h1>
        <p>Sign in with your email address and password.</p>

        <!-- PHP Error Message -->
        <?php if (!empty($message)): ?>
          <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="">
          <label for="email">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required
          />

          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
          />

          <div class="options">
            <label><input type="checkbox" id="rememberMe" /> Remember me</label>
            <a href="../forgot password/index.php">Forgot Password?</a>
          </div>

          <button type="submit" class="sign-in-btn">Sign In</button>

          <div class="signup-text">
            Don’t have an account? <a href="../create Account/index.php">Sign up</a>
          </div>
        </form>
      </div>
    </div>

    <script>
      // Optional: "Remember Me" using localStorage
      const rememberedUser = localStorage.getItem("rememberedEmail");
      if (rememberedUser) {
        document.getElementById("email").value = rememberedUser;
        document.getElementById("rememberMe").checked = true;
      }

      document.getElementById("loginForm").addEventListener("submit", function () {
        const email = document.getElementById("email").value.trim();
        const rememberMe = document.getElementById("rememberMe").checked;

        if (rememberMe) {
          localStorage.setItem("rememberedEmail", email);
        } else {
          localStorage.removeItem("rememberedEmail");
        }
      });
    </script>
  </body>
</html>
