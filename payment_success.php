<?php
session_start();
include "db.php";
include "header.php";

// Check if checkout data exists
if (!isset($_SESSION['checkout_data'])) {
    echo "<p class='alert alert-danger text-center'>Payment failed or session expired.</p>";
    include "footer.php";
    exit();
}

$data = $_SESSION['checkout_data'];
$user_id = $data['user_id'];
$full_address = $data['full_address'];
$total_price = $data['total_price'];
$payment_method = $_POST['payment_method'] ?? $data['payment_method'] ?? 'N/A';
$cart = $data['cart'];

// Get transaction ID and mobile from payment form
$trxid = $_POST['trxid'] ?? 'N/A';
$mobile = $_POST['mobile'] ?? 'N/A';

// Insert order into database
$stmt = $conn->prepare("INSERT INTO orders (user_id, address, total_price, payment_method, transaction_id, mobile, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("ssdsss", $user_id, $full_address, $total_price, $payment_method, $trxid, $mobile);
$stmt->execute();
$order_id = $conn->insert_id;

// Insert each cart item into order_items
foreach ($cart as $book_id => $qty) {
    $stmt = $conn->prepare("SELECT price FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $price = $book['price'];

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $order_id, $book_id, $qty, $price);
    $stmt->execute();
}

// Clear cart and checkout session
unset($_SESSION['cart']);
unset($_SESSION['checkout_data']);

// Show confirmation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
            background-color: white;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h4 {
            font-size: 2rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .alert {
            font-size: 1.1rem;
            padding: 20px;
            border-radius: 8px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert p {
            margin: 10px 0;
        }

        .btn {
            padding: 10px 20px;
            font-size: 1rem;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 50px;
        }

        footer a {
            color: #f8f9fa;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class='container mt-5'>
    <div class='alert alert-success text-center'>
        <h4>Payment Successful!</h4>
        <p>Your order has been placed successfully.</p>
        <p><strong>Payment Method:</strong> <?php echo $payment_method; ?></p>
        <p><strong>Transaction ID:</strong> <?php echo $trxid; ?></p>
        <p><strong>Mobile:</strong> <?php echo $mobile; ?></p>
        <a href='orders.php' class='btn btn-primary mt-3'>Go to Orders</a>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>© 2025 BookStore. All rights reserved.</p>
    <a href="contact.php">Contact Us</a>
</footer>

</body>
</html>

<?php
include "footer.php";
?>
