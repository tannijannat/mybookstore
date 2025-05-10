<?php
session_start();

// Include database config first
include "db.php";

// Set up connection manually (you already have $conn from db.php, so this can be skipped if it's already connected)
$host = "localhost";
$user = "root";
$password = "";
$dbname = "book_storee";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get book ID
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($book_id <= 0) {
    echo "Invalid book ID.";
    exit;
}

// Fetch book data
$query = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Book not found.";
    exit;
}
$book = $result->fetch_assoc();

// Check if user can review
$can_review = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $query = "SELECT COUNT(*) as completed_orders 
              FROM orders o 
              JOIN order_items oi ON o.id = oi.order_id 
              WHERE o.user_id = ? AND oi.book_id = ? AND o.status = 'completed'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $completed_orders = $result->fetch_assoc()['completed_orders'] ?? 0;

    $query = "SELECT COUNT(*) as user_reviews FROM reviews WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_reviews = $result->fetch_assoc()['user_reviews'] ?? 0;

    if ($completed_orders > $user_reviews) {
        $can_review = true;
    }
}

// Include header after session + logic setup
include "header.php";
?>

<!-- Page content starts -->
<div class="container mt-5">
    <h2 class="text-center mb-4"><?= htmlspecialchars($book["title"]); ?></h2>

    <div class="row">
        <div class="col-md-6">
            <img src="<?= htmlspecialchars($book['image'] ?? 'Lore.jpg'); ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']); ?>">
        </div>
        <div class="col-md-6">
            <p><strong>Author:</strong> <?= htmlspecialchars($book["author"]); ?></p>
            <p><strong>Price:</strong> $<?= htmlspecialchars($book["price"]); ?></p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($book["description"])); ?></p>

            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="cart.php?id=<?= $book["id"]; ?>" class="btn btn-primary">Add to Cart</a>
            <?php else: ?>
                <button class="btn btn-warning" onclick="redirectToLogin();">Add to Cart</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-5">
        <h4><i class="fas fa-comments"></i> Reviews</h4>
        <div class="row">
        <?php
        $query = "SELECT r.rating, r.review, u.name AS username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.book_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($review = $result->fetch_assoc()) {
                echo "<div class='col-md-6'>";
                echo "<div class='card review-card shadow-sm mb-3'>";
                echo "<div class='card-body'>";
                echo "<div class='d-flex align-items-center'>";
                echo "<i class='fas fa-user-circle profile-icon'></i>";
                echo "<h5 class='card-title'>" . htmlspecialchars($review["username"]) . "</h5>";
                echo "</div>";
                echo "<h6 class='card-subtitle mb-2 text-muted'>Rating: " . str_repeat('⭐', $review["rating"]) . "</h6>";
                echo "<p class='card-text'><strong>Comment:</strong> " . nl2br(htmlspecialchars($review["review"])) . "</p>";
                echo "</div></div></div>";
            }
        } else {
            echo "<p class='col-12'>No reviews yet.</p>";
        }
        ?>
        </div>
    </div>

    <?php if ($can_review): ?>
        <div class="mt-5">
            <h4><i class="fas fa-pen"></i> Leave a Review</h4>
            <div class="card shadow">
                <div class="card-body">
                    <form action="submit_review.php" method="POST">
                        <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_id); ?>">
                        <div class="form-group">
                            <label for="rating">Rating (1 to 5 stars):</label>
                            <input type="number" id="rating" name="rating" min="1" max="5" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="review">Review:</label>
                            <textarea id="review" name="review" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p class="mt-3 text-muted">You must complete the book delivery before submitting a new review or you’ve already reviewed all your purchases.</p>
    <?php endif; ?>
</div>

<script>
function redirectToLogin() {
    alert("You are not logged in. Please log in first.");
    window.location.href = "login.php";
}
</script>

<!-- Bootstrap & JS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<?php include "footer.php"; ?>
