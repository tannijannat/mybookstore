<?php
session_start();
include "db.php";

// Validate session & Stripe session ID
if (!isset($_GET['session_id']) || !isset($_SESSION['checkout_data'])) {
    header("Location: index.php");
    exit();
}

$data = $_SESSION['checkout_data'];
$user_id = $data['user_id'];
$address = $data['full_address'];
$total_price = $data['total_price'];
$cart = $data['cart'];
$payment_method = 'stripe';

// Save order
$stmt = $conn->prepare("INSERT INTO orders (user_id, address, total_price, payment_method, status) VALUES (?, ?, ?, ?, 'paid')");
$stmt->bind_param("ssds", $user_id, $address, $total_price, $payment_method);
$stmt->execute();
$order_id = $conn->insert_id;

// Save order items
foreach ($cart as $book_id => $qty) {
    $result = $conn->query("SELECT price FROM books WHERE id = $book_id");
    $row = $result->fetch_assoc();
    $price = $row['price'];

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $order_id, $book_id, $qty, $price);
    $stmt->execute();
}

// Clear session data
unset($_SESSION['cart']);
unset($_SESSION['checkout_data']);
?>

<?php include "header.php"; ?>

<div class="container text-center py-5">
    <h2 class="text-success mb-4">✅ Payment Successful!</h2>
    <p class="mb-4">Thank you for your purchase. Your order has been placed successfully.</p>
    <a href="orders.php" class="btn btn-primary btn-lg">📦 Go to Orders</a>
</div>

<?php include "footer.php"; ?>
