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

    // Validate mobile number
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $message = "Mobile number must be exactly 10 digits and contain only numbers (South Africa format).";
    }
    // Check passwords
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into database
        $sql = "INSERT INTO users (username, email, mobile, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $mobile, $hashedPassword);

        if ($stmt->execute()) {
            // Account created successfully → log in user immediately
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account | Crown & Glow Hair Salon</title>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inknut+Antiqua:wght@600&family=Inder&display=swap");

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Inder", sans-serif;
    }

    body {
      background: url("img/Background.png") no-repeat center center/cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #fff;
      position: relative;
    }

    .logo {
      position: absolute;
      top: 30px;
      left: 50px;
      width: 130px;
    }

    .container {
      background-color: rgba(0, 0, 0, 0);
      border-radius: 20px;
      padding: 50px 70px;
      width: 90%;
      max-width: 1100px;
    }

    h1 {
      text-align: center;
      font-family: "Inknut Antiqua", serif;
      font-size: 40px;
      margin-bottom: 30px;
    }

    form {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 30px 50px;
      justify-content: center;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    label {
      margin-bottom: 10px;
      font-size: 18px;
      color: #f2f2f2;
    }

    input {
      border: 1px solid #acacac;
      border-radius: 35px;
      padding: 14px 20px;
      font-size: 16px;
      background: #f2f2f2;
      color: #333;
      outline: none;
      transition: 0.3s;
    }

    input:focus {
      border-color: #cba765;
      box-shadow: 0 0 5px #cba765;
    }

    .terms {
      grid-column: span 2;
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 16px;
      margin-top: 10px;
      line-height: 1.4;
    }

    .terms input {
      width: 18px;
      height: 18px;
      accent-color: #cca46c;
      margin-top: 3px;
    }

    .terms a {
      color: #cca46c;
      text-decoration: none;
    }

    .btn {
      grid-column: span 2;
      justify-self: center;
      background-color: #cba765;
      color: white;
      border: none;
      padding: 14px 60px;
      border-radius: 30px;
      font-size: 20px;
      cursor: pointer;
      transition: background 0.3s ease, box-shadow 0.3s ease;
      margin-top: 20px;
    }

    .btn:hover {
      background-color: #b59050;
    }

    .signin {
      grid-column: span 2;
      text-align: center;
      margin-top: 15px;
      font-size: 18px;
    }

    .signin a {
      color: #cba765;
      text-decoration: none;
    }

    .error-message {
      grid-column: span 2;
      text-align: center;
      color: red;
      margin-bottom: 10px;
    }

    @media (max-width: 900px) {
      form {
        grid-template-columns: 1fr;
      }
      .btn {
        width: 100%;
      }
      .container {
        padding: 40px 30px;
      }
    }
  </style>
</head>
<body>
  <img src="img/logo.png" alt="Crown & Glow Logo" class="logo" />

  <div class="container">
    <h1>Create Account</h1>

    <?php if (!empty($message)) { ?>
      <p class="error-message"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST" action="">
  
      <div class="form-group">
        <label for="username">UserName</label>
        <input type="text" name="username" id="username" placeholder="Enter Username" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter Password" required />
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" placeholder="Enter Email Address" required />
      </div>

      <div class="form-group">
        <label for="confirm-password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm-password" placeholder="Confirm Password" required />
      </div>


      <div class="form-group">
        <label for="mobile">Mobile Number</label>
        <input type="tel" name="mobile" id="mobile" placeholder="Enter Mobile Number" maxlength="10" pattern="[0-9]{10}" required />
      </div>

      <div class="terms">
        <input type="checkbox" id="agree" required />
        <label for="agree">
          By creating an account, I agree to the
          <a href="#">Terms of Use</a> and understand that my information will be used as described on this page.
        </label>
      </div>

      <button type="submit" class="btn">Create Account</button>

      <div class="signin">
        Already have an account? <a href="../Sign in/index.php">Sign in</a>
      </div>
    </form>
  </div>
</body>
</html>
