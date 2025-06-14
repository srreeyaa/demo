<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$conn = new mysqli("localhost", "root", "", "lib1db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Handle book add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
  $book_id = $_POST['book_id'];

  $check = $conn->prepare("SELECT * FROM issued_books WHERE user_id = ? AND book_id = ?");
  $check->bind_param("ii", $user_id, $book_id);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO issued_books (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    echo "<script>alert('📘 Book added to your profile');</script>";
  } else {
    echo "<script>alert('⚠️ You already added this book');</script>";
  }
}

// Handle book remove
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_book_id'])) {
  $remove_book_id = $_POST['remove_book_id'];
  $stmt = $conn->prepare("DELETE FROM issued_books WHERE user_id = ? AND book_id = ?");
  $stmt->bind_param("ii", $user_id, $remove_book_id);
  $stmt->execute();
  echo "<script>alert('❌ Book removed from your profile');</script>";
}

// Fetch all books grouped by category
$sql = "SELECT * FROM books ORDER BY category";
$result = $conn->query($sql);
$categories = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $categories[$row['category']][] = $row;
  }
}

// Fetch user's added books
$myBooksQuery = "
  SELECT b.id, b.book_name, b.author 
  FROM issued_books i
  JOIN books b ON i.book_id = b.id
  WHERE i.user_id = ?
";
$stmt = $conn->prepare($myBooksQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$myBooksResult = $stmt->get_result();

$myBooks = [];
while ($row = $myBooksResult->fetch_assoc()) {
  $myBooks[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Books</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      padding: 30px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .profile-box {
      position: relative;
      background-color: #fff3e9;
      padding: 10px 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      background-color: white;
      border: 1px solid #ccc;
      width: 230px;
      max-height: 250px;
      overflow-y: auto;
      display: none;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      z-index: 1;
      border-radius: 5px;
    }

    .profile-box:hover .dropdown {
      display: block;
    }

    .dropdown form {
      padding: 10px;
      margin: 0;
      border-bottom: 1px solid #eee;
    }

    .dropdown form:last-child {
      border-bottom: none;
    }

    .logout {
      background-color: red;
      color: white;
      padding: 6px 10px;
      border-radius: 5px;
      font-weight: bold;
      text-decoration: none;
      margin-left: 15px;
    }

    h2 {
      margin-top: 30px;
      color: #333;
    }

    .category-section {
      background-color: #fff;
      border-left: 6px solid rgb(229, 158, 111);
      margin-top: 20px;
      padding: 15px 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    .book {
      margin-left: 20px;
      padding: 8px 0;
      border-bottom: 1px solid #ddd;
    }

    .book:last-child {
      border-bottom: none;
    }

    form {
      display: inline;
    }

    button {
      background-color: rgb(229, 158, 111);
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 5px;
    }

    button:hover {
      background-color: #e48e44;
    }

    .remove-btn {
      background-color: red;
      margin-left: 5px;
    }

    .status-text {
      font-style: italic;
      color: grey;
    }
  </style>
</head>
<body>

<div class="header">
  <h1>📚 Library Books</h1>
  <div class="profile-box">
    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
    <div class="dropdown">
      <?php if (count($myBooks) > 0): ?>
        <?php foreach ($myBooks as $book): ?>
          <form method="POST">
            <p>
              📘 <?php echo htmlspecialchars($book['book_name']); ?><br>
              <small>✍️ <?php echo htmlspecialchars($book['author']); ?></small><br>
              <input type="hidden" name="remove_book_id" value="<?php echo $book['id']; ?>">
              <button type="submit" class="remove-btn">❌ Remove this book</button>
            </p>
          </form>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="padding:10px;">No books added yet.</p>
      <?php endif; ?>
      <p><a href="logout.php" class="logout">Logout</a></p>
    </div>
  </div>
</div>

<h2>📂 Books by Category</h2>

<?php if (!empty($categories)): ?>
  <?php foreach ($categories as $categoryName => $books): ?>
    <div class="category-section">
      <h3>📁 <?php echo htmlspecialchars($categoryName); ?></h3>
      <?php foreach ($books as $book): ?>
        <div class="book">
          📖 <strong><?php echo htmlspecialchars($book['book_name']); ?></strong><br>
          ✍️ Author: <?php echo htmlspecialchars($book['author']); ?><br>
          📅 Due Date: <?php echo htmlspecialchars($book['due_date']); ?><br>
          ✅ Availability:
          <span style="color:<?php echo ($book['availability'] == 'Yes') ? 'green' : 'red'; ?>">
            <?php echo htmlspecialchars($book['availability']); ?>
          </span><br>
          <?php if ($book['availability'] === 'No'): ?>
            <p class="status-text">⛔ Not available</p>
          <?php endif; ?>
          <form method="POST">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            <button type="submit">➕ Add to My Books</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>No books available.</p>
<?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
