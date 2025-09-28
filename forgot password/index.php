<?php
// Include DB connection
include("../API/db_connect.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $mobile = $_POST["phone"];
    $password = $_POST["new-password"];
    $confirm_password = $_POST["confirm-password"];

    // Validate mobile format (South Africa - 10 digits)
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $message = "Mobile number must be 10 digits (South Africa format).";
    }
    // Check passwords match
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
            // Hash new password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Update password in database
            $updateSql = "UPDATE users SET password = ? WHERE email = ? AND mobile = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("sss", $hashedPassword, $email, $mobile);

            if ($updateStmt->execute()) {
                $message = "Password updated successfully!";
            } else {
                $message = "Error updating password!";
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crown & Glow | Forgot Password</title>
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Inknut+Antiqua:wght@600&family=Inder&display=swap");

      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: "Inder", sans-serif;
      }

      html, body {
        height: 100%;
        width: 100%;
        overflow-x: hidden;
        background: url("img/Background.png") center center/cover no-repeat fixed;
      }

      body {
        display: flex;
        align-items: flex-start;
        justify-content: center;
      }

      .container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        min-height: 100vh;
        background-color: rgba(0, 0, 0, 0.6);
        padding: 5% 8%;
        flex-wrap: wrap;
      }

      .logo-section {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        min-width: 280px;
        margin-top: 80px;
      }

      .logo-section img {
        width: 320px;
        height: auto;
        max-width: 100%;
      }

      .form-section {
        flex: 1;
        color: #fff;
        max-width: 500px;
        margin-top: 10px;
      }

      .form-section h1 {
        font-family: "Inknut Antiqua", serif;
        font-size: 40px;
        font-weight: 600;
        margin-bottom: 10px;
      }

      .form-section p {
        font-size: 18px;
        margin-bottom: 40px;
        color: #dcdcdc;
      }

      label {
        display: block;
        font-size: 18px;
        margin-bottom: 8px;
      }

      input {
        width: 100%;
        padding: 16px 20px;
        border: 1px solid #acacac;
        border-radius: 35px;
        font-size: 16px;
        outline: none;
        margin-bottom: 25px;
        background-color: #f2f2f2;
      }

      .sign-in-btn {
        width: 100%;
        padding: 15px;
        background-color: #c29d68;
        border: none;
        border-radius: 35px;
        font-size: 22px;
        font-weight: bold;
        color: #fff;
        cursor: pointer;
        transition: 0.3s;
      }

      .sign-in-btn:hover {
        background-color: #b08a4e;
      }

      .signup-text {
        text-align: center;
        margin-top: 30px;
        font-size: 18px;
      }

      .signup-text a {
        color: #cca46c;
        margin-left: 5px;
        text-decoration: none;
      }

      .message {
        text-align: center;
        margin-bottom: 20px;
        font-size: 18px;
      }

      .message.success {
        color: #90ee90;
      }

      .message.error {
        color: #ff6b6b;
      }

      @media (max-width: 900px) {
        body {
          align-items: flex-start;
          justify-content: flex-start;
        }

        .container {
          flex-direction: column;
          align-items: center;
          justify-content: flex-start;
          text-align: center;
          padding: 10% 6%;
          min-height: auto;
        }

        .logo-section {
          margin-top: 40px;
          margin-bottom: 30px;
        }

        .form-section {
          width: 100%;
          max-width: 400px;
          margin-top: 40px;
        }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <!-- Left Logo Section -->
      <div class="logo-section">
        <img src="img/logo.png" alt="Crown & Glow Hair Salon Logo" />
      </div>

      <!-- Right Form Section -->
      <div class="form-section">
        <h1>Forgot Password</h1>
        <p>Reset your password by entering your details below.</p>

        <!-- PHP message -->
        <?php if (!empty($message)) { ?>
          <p class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
          </p>
        <?php } ?>

        <form action="" method="post">
          <label for="email">Email Address.</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required
          />

          <label for="phone">Phone Number.</label>
          <input
            type="tel"
            id="phone"
            name="phone"
            placeholder="Enter your phone number"
            required
            maxlength="10"
            pattern="[0-9]{10}"
          />

          <label for="new-password">New Password.</label>
          <input
            type="password"
            id="new-password"
            name="new-password"
            placeholder="Enter new password"
            required
          />

          <label for="confirm-password">Confirm Password.</label>
          <input
            type="password"
            id="confirm-password"
            name="confirm-password"
            placeholder="Re-enter new password"
            required
          />

          <button type="submit" class="sign-in-btn">Reset Password</button>

          <div class="signup-text">
            Remembered your password?
            <a href="../Sign in/index.php">Go back to Sign in</a>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
