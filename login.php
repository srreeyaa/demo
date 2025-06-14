<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lib1db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT id, fname, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['fname'];
            $_SESSION['email'] = $user['email'];

            header("Location: view_categories.php");
            exit;
        } else {
            echo "<script>alert('❌ Wrong password.'); window.location.href='index.html';</script>";
        }
    } else {
        echo "<script>alert('❌ Email not registered.'); window.location.href='index.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #fdf7f7;
      font-family: Arial, sans-serif;
    }

    .container {
      background-color: lightgrey;
      width: 300px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      text-align: left;
    }

    h1 {
      text-align: center;
      background-color: rgb(229, 158, 111);
      padding: 10px;
      border-radius: 5px;
    }

    input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      background-color: rgb(229, 158, 111);
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #e48e44;
    }

    .admin-link {
      text-align: center;
      margin-top: 15px;
    }

    .admin-link a {
      color: #e48e44;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <form action="login.php" method="POST">
    <div class="container">
      <h1>Login Here</h1>

      <label for="email"><b>Email</b></label>
      <input type="text" placeholder="Enter Email" id="email" name="email" required><br><br>

      <label for="password"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" id="password" name="password" required><br><br>

      <button type="submit">Login</button>

      <div class="admin-link">
        Are you an admin? <a href="admin_login.php">Admin Login</a>
      </div>
    </div>
  </form>
</body>
</html>

