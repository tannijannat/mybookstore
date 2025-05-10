<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $total_price = 0;

    foreach ($_SESSION['cart'] as $book_id => $qty) {
        $book = $conn->query("SELECT price FROM books WHERE id = $book_id")->fetch_assoc();
        $total_price += $book['price'] * $qty;
    }

    // Insert into orders
    $conn->query("INSERT INTO orders (user_id, address, payment_method, total_price) VALUES (
        $user_id, '$address', '$payment_method', $total_price
    )");
    $order_id = $conn->insert_id;

    // Insert order items
    foreach ($_SESSION['cart'] as $book_id => $qty) {
        $book = $conn->query("SELECT price FROM books WHERE id = $book_id")->fetch_assoc();
        $price = $book['price'];
        $conn->query("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (
            $order_id, $book_id, $qty, $price
        )");
    }

    unset($_SESSION['cart']); // Clear cart after order
    echo "<div class='container'><h4>✅ Order placed successfully! Order ID: $order_id</h4></div>";
} else {
    echo "❌ Invalid request.";
}
?>
