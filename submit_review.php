<?php
session_start();
include "db.php"; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review = isset($_POST['review']) ? trim($_POST['review']) : '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate inputs
    if ($book_id === 0 || $rating === 0 || empty($review)) {
        echo "All fields are required.";
        exit;
    }

    if ($rating < 1 || $rating > 5) {
        echo "Rating must be between 1 and 5.";
        exit;
    }

    // Step 1: Count how many times user completed orders of this book
    $query = "SELECT COUNT(*) as completed_orders 
              FROM orders o 
              JOIN order_items oi ON o.id = oi.order_id 
              WHERE o.user_id = ? AND oi.book_id = ? AND o.status = 'completed'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $completed_orders = $result->fetch_assoc()['completed_orders'] ?? 0;

    // Step 2: Count how many reviews user already gave for this book
    $query = "SELECT COUNT(*) as review_count FROM reviews WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $review_count = $result->fetch_assoc()['review_count'] ?? 0;

    // Step 3: Allow review only if remaining allowed reviews > 0
    if ($review_count >= $completed_orders) {
        echo "You have already submitted all reviews for this book based on your completed orders.";
        exit;
    }

    // Step 4: Insert the review into the database
    $query = "INSERT INTO reviews (book_id, user_id, rating, review) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $book_id, $user_id, $rating, $review);

    if ($stmt->execute()) {
        header("Location: description.php?id=" . $book_id); // Redirect back to book description page
        exit;
    } else {
        echo "There was an error submitting your review: " . $stmt->error;
    }
}
?>
