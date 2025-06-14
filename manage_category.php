<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

$conn = new mysqli("localhost", "root", "", "lib1db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Add new book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
  $book = $_POST['book'];
  $author = $_POST['author'];
  $category = $_POST['category'];
  $due = $_POST['due'];
  $availability = $_POST['availability'];

  if (!empty($book) && !empty($author) && !empty($category) && !empty($due) && isset($availability)) {
    $stmt = $conn->prepare("INSERT INTO books (book_name, author, category, due_date, availability) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $book, $author, $category, $due, $availability);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('✅ Book added successfully'); window.location='manage_category.php';</script>";
  }
}

// Update book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_book'])) {
  $book_id = $_POST['book_id'];
  $book = $_POST['book'];
  $author = $_POST['author'];
  $category = $_POST['category'];
  $due = $_POST['due'];
  $availability = $_POST['availability'];

  $stmt = $conn->prepare("UPDATE books SET book_name=?, author=?, category=?, due_date=?, availability=? WHERE id=?");
  $stmt->bind_param("sssssi", $book, $author, $category, $due, $availability, $book_id);
  $stmt->execute();
  $stmt->close();
  echo "<script>alert('✅ Book updated successfully'); window.location='manage_category.php';</script>";
}

// Delete book
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];
  $conn->query("DELETE FROM books WHERE id = $delete_id");
  echo "<script>alert('🗑️ Book deleted successfully'); window.location='manage_category.php';</script>";
}

// Get all books
$bookResult = $conn->query("SELECT * FROM books");

// If editing a book
$editing = false;
if (isset($_GET['edit'])) {
  $editing = true;
  $edit_id = $_GET['edit'];
  $edit_result = $conn->query("SELECT * FROM books WHERE id = $edit_id");
  $edit_data = $edit_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Books</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      padding: 30px;
    }

    h2, h3 {
      color: #333;
    }

    form {
      background-color: #fff;
      padding: 20px;
      max-width: 500px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      padding: 10px 15px;
      background-color: rgb(229, 158, 111);
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #e48e44;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: rgb(229, 158, 111);
      color: white;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    a {
      display: inline-block;
      margin-top: 20px;
      color: #555;
      text-decoration: none;
      font-weight: bold;
    }

    a:hover {
      color: #e48e44;
    }

    .actions a {
      margin-right: 10px;
    }
  </style>
</head>
<body>

  <h2>➕ Add New Book</h2>
  <form method="POST">
    <label><strong>Book Name:</strong></label>
    <input type="text" name="book" placeholder="Enter book name" required>

    <label><strong>Author Name:</strong></label>
    <input type="text" name="author" placeholder="Enter author name" required>

    <label><strong>Category:</strong></label>
    <input type="text" name="category" placeholder="Enter category" required>

    <label><strong>Due Date:</strong></label>
    <input type="date" name="due" required>

    <label><strong>Availability:</strong></label>
    <select name="availability" required>
      <option value="yes">Yes</option>
      <option value="no">No</option>
    </select>

    <button type="submit" name="add_book">Add Book</button>
  </form>

  <?php if ($editing): ?>
    <h2>📝 Update Book</h2>
    <form method="POST">
      <input type="hidden" name="book_id" value="<?php echo $edit_data['id']; ?>">

      <label><strong>Book Name:</strong></label>
      <input type="text" name="book" required value="<?php echo $edit_data['book_name']; ?>">

      <label><strong>Author Name:</strong></label>
      <input type="text" name="author" required value="<?php echo $edit_data['author']; ?>">

      <label><strong>Category:</strong></label>
      <input type="text" name="category" required value="<?php echo $edit_data['category']; ?>">

      <label><strong>Due Date:</strong></label>
      <input type="date" name="due" required value="<?php echo $edit_data['due_date']; ?>">

      <label><strong>Availability:</strong></label>
      <select name="availability" required>
        <option value="yes" <?php if ($edit_data['availability'] == 'yes') echo 'selected'; ?>>Yes</option>
        <option value="no" <?php if ($edit_data['availability'] == 'no') echo 'selected'; ?>>No</option>
      </select>

      <button type="submit" name="update_book">Update Book</button>
    </form>
  <?php endif; ?>

  <hr style="margin: 40px 0;">

  <h3>📚 All Books:</h3>
  <table>
    <tr>
      <th>Book Name</th>
      <th>Author</th>
      <th>Category</th>
      <th>Due Date</th>
      <th>Availability</th>
      <th>Actions</th>
    </tr>
    <?php while ($book = $bookResult->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($book['book_name']); ?></td>
        <td><?php echo htmlspecialchars($book['author']); ?></td>
        <td><?php echo htmlspecialchars($book['category']); ?></td>
        <td><?php echo htmlspecialchars($book['due_date']); ?></td>
        <td><?php echo htmlspecialchars($book['availability']); ?></td>
        <td class="actions">
          <a href="manage_category.php?edit=<?php echo $book['id']; ?>">✏️ Update</a>
          <a href="manage_category.php?delete=<?php echo $book['id']; ?>" onclick="return confirm('Are you sure you want to delete this book?');">❌ Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <a href="admin_dashboard.php">← Back to Dashboard</a>

  <div style="text-align: right; margin-top: 20px;">
    <a href="logout.php" style="padding: 10px 20px; background-color: #e48e44; color: white; border-radius: 5px; text-decoration: none; font-weight: bold;">Logout</a>
  </div>
</body>
</html>

<?php $conn->close(); ?>
