<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lib1db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $pass = $_POST['password'];

  $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();

  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();

    if ($pass === $admin['password']) {
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_user'] = $admin['username'];
      header("Location: admin_dashboard.php");
      exit;
    } else {
      echo "<script>alert('❌ Wrong password'); window.location.href='admin_login.php';</script>";
    }
  } else {
    echo "<script>alert('❌ Admin not found'); window.location.href='admin_login.php';</script>";
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
   <link rel="stylesheet" href="style.css">
  <title>Admin Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-box {
      background-color: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 300px;
      text-align: center;
    }

    .login-box img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    h2 {
      margin-bottom: 25px;
      color: #333;
    }

    label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
      color: #555;
      text-align: left;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: rgb(229, 158, 111);
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #e48e44;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <img src="user.jpg" alt="Admin Image">
    <h2>Admin Login</h2>
    <form method="POST" action="admin_login.php">
      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
