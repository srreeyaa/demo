<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f4f8;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 100px auto;
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
    }

    h1 {
      color: #333;
      margin-bottom: 30px;
    }

    a {
      display: block;
      margin: 15px 0;
      padding: 10px;
      background-color: rgb(229, 158, 111);
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
    }

    a:hover {
      background-color:#e48e44;
    }

    .logout {
      background-color:#e48e44;
    }

    .logout:hover {
      background-color: #cc3f35;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Welcome <?php echo $_SESSION['admin_user']; ?>!</h1>

  <a href="manage_category.php"> Manage Book Categories</a>

</div>



</body>
</html>
