<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
include "header.php";
include "db.php";

// Handle adding a new book
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    $title = trim($_POST["title"]);
    $author = trim($_POST["author"]);
    $price = (float) trim($_POST["price"]);
    $stock = (int) trim($_POST["stock"]);
    $category_id = (int) trim($_POST["category_id"]);
    $description = trim($_POST["description"]);

    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $imagePath = 'images/' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $imagePath);
    }

    $stmt = $conn->prepare("INSERT INTO books (title, author, price, stock, category_id, image, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdisss", $title, $author, $price, $stock, $category_id, $imagePath, $description);
    $stmt->execute();
    header("Location: manage_books.php");
    exit();
}

// Edit book logic
if (isset($_GET['edit'])) {
    $book_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_book'])) {
        $title = trim($_POST["title"]);
        $author = trim($_POST["author"]);
        $price = (float) trim($_POST["price"]);
        $stock = (int) trim($_POST["stock"]);
        $category_id = (int) trim($_POST["category_id"]);
        $description = trim($_POST["description"]);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $imagePath = 'images/' . basename($image['name']);
            move_uploaded_file($image['tmp_name'], $imagePath);
            $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, price = ?, stock = ?, category_id = ?, image = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssdisssi", $title, $author, $price, $stock, $category_id, $imagePath, $description, $book_id);
        } else {
            $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, price = ?, stock = ?, category_id = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssdissi", $title, $author, $price, $stock, $category_id, $description, $book_id);
        }

        $stmt->execute();
        header("Location: manage_books.php");
        exit();
    }
}

// Delete book logic
if (isset($_GET['delete'])) {
    $book_id = $_GET['delete'];
    $conn->query("DELETE FROM order_details WHERE book_id = $book_id");
    $conn->query("DELETE FROM books WHERE id = $book_id");
    header("Location: manage_books.php");
    exit();
}

// Fetch books
$result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Books</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 1000px;
        margin: 30px auto;
        background: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }
    form input,
    form select,
    form textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    form button {
        background: #28a745;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    form button:hover {
        background: #218838;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
    }
    table th, table td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: center;
    }
    table th {
        background: #f0f0f0;
    }
    .btn {
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        color: white;
        font-size: 14px;
    }
    .btn-warning { background: #ffc107; }
    .btn-danger { background: #dc3545; }
</style>
</head>
<body>

<div class="container">
    <h2>Manage Books</h2>

    <!-- Add Book Form -->
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" required placeholder="Book Title">
        <input type="text" name="author" required placeholder="Author">
        <input type="number" name="price" required step="0.01" placeholder="Price">
        <input type="number" name="stock" required placeholder="Stock">
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php
            $cat_result = $conn->query("SELECT * FROM categories");
            while ($cat = $cat_result->fetch_assoc()) {
                echo "<option value='{$cat['category_id']}'>" . htmlspecialchars($cat['category_name']) . "</option>";
            }
            ?>
        </select>
        <textarea name="description" rows="4" required placeholder="Description"></textarea>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="add_book">Add Book</button>
    </form>

    <!-- Book Table -->
    <table>
        <thead>
            <tr><th>ID</th><th>Title</th><th>Author</th><th>Price</th><th>Stock</th><th>Image</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row["id"] ?></td>
                <td><?= htmlspecialchars($row["title"]) ?></td>
                <td><?= htmlspecialchars($row["author"]) ?></td>
                <td>$<?= number_format($row["price"], 2) ?></td>
                <td><?= $row["stock"] ?></td>
                <td><?php if ($row["image"]): ?><img src="<?= $row["image"] ?>" width="50"><?php else: ?>No Image<?php endif; ?></td>
                <td>
                    <a href="?edit=<?= $row["id"] ?>" class="btn btn-warning">Edit</a>
                    <a href="?delete=<?= $row["id"] ?>" class="btn btn-danger" onclick="return confirm('Delete this book?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Edit Form -->
    <?php if (isset($_GET['edit'])): ?>
        <h3 style="margin-top: 40px;">Edit Book</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="title" required value="<?= htmlspecialchars($book['title']) ?>">
            <input type="text" name="author" required value="<?= htmlspecialchars($book['author']) ?>">
            <input type="number" name="price" step="0.01" required value="<?= htmlspecialchars($book['price']) ?>">
            <input type="number" name="stock" required value="<?= htmlspecialchars($book['stock']) ?>">
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php
                $cat_result = $conn->query("SELECT * FROM categories");
                while ($cat = $cat_result->fetch_assoc()) {
                    $selected = $cat['category_id'] == $book['category_id'] ? 'selected' : '';
                    echo "<option value='{$cat['category_id']}' $selected>" . htmlspecialchars($cat['category_name']) . "</option>";
                }
                ?>
            </select>
            <textarea name="description" rows="4" required><?= htmlspecialchars($book['description']) ?></textarea>
            <input type="file" name="image" accept="image/*">
            <button type="submit" name="update_book">Update Book</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

<?php include "footer.php"; ?>