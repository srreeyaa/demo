<?php
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

// Get form data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user1 = $_POST['fname'];
    $user2 = $_POST['lname'];
    $email = $_POST['email'];
    $gen = $_POST['gender'];
    $dob = $_POST['dob'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // Fix: $_POST not $_post

    // Prepare and bind (Fix: column name typo and parameter count)
    $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, gender, dob, PASSWORD) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $user1, $user2, $email, $gen, $dob, $pass); // Fix: used "ssssss"

    if ($stmt->execute()) {
        echo "Registration successful!";
        echo "<p><a href='login.html'>Click here to login</a></p>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color:  #fdf7f7;
      font-family: Arial, sans-serif;
    }

    div {
      background-color: lightgrey;
      width: 320px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      border-radius: 10px;
    }

    h1 {
      text-align: center;
      background-color: rgb(229, 158, 111);
      padding: 10px;
      border-radius: 5px;
    }

    label {
      font-weight: bold;
    }

    input[type="text"],
    input[type="password"],
    input[type="date"] {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: rgb(229, 158, 111);
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }

    button a {
      text-decoration: none;
      color: black;
    }

    button:hover {
      background-color: #e48e44;
    }

    p {
      text-align: center;
    }
  </style>
</head>
<body>
  <form action="register.php" method="POST">
  <div>
    
    <h1>Register</h1>
    <p style="font-size: small;"><i>Please fill in this form to create an account.</i></p>

    <label for="fname">First Name</label>
    <input type="text" placeholder="Enter First Name" id="fname" name="fname" required>

    <label for="lname">Last Name</label>
    <input type="text" placeholder="Enter Last Name" id="lname" name="lname" required>

    <label for="email">Email</label>
    <input type="text" placeholder="Enter Email" id="email" name="email" required>

    <label>Gender:</label><br>
    <label><input type="radio" name="gender" value="male"> Male</label><br>
    <label><input type="radio" name="gender" value="female"> Female</label><br>
    <label><input type="radio" name="gender" value="others"> Others</label><br><br>

    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" required>

    <label for="psw">Password</label>
    <input type="password" placeholder="Enter Password" id="psw" name="password" required>

    <label for="psw-repeat">Repeat Password</label>
    <input type="password" placeholder="Repeat Password" id="psw-repeat" name="password_repeat" required>

   <button type="submit">Register</button>
  </div>
  </form>
</body>
</html>
