<?php
session_start();
include "db.php"; 
include "header.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Handle order deletion
if (isset($_GET['delete_id'])) {
    $order_id = intval($_GET['delete_id']);
    $user_id = $_SESSION["user_id"];

    // Check if the order belongs to the user
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        
        // Check the status of the order
        if ($order['status'] === 'completed') {
            echo "<div class='alert alert-warning text-center'>You cannot delete a completed order. Please contact an admin if you need assistance.</div>";
        } else {
            // Delete the order and its associated items with prepared statements
            $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();

            // Redirect back to orders page with a success message
            $_SESSION['message'] = "Order deleted successfully.";
            header("Location: orders.php");
            exit();
        }
    } else {
        echo "<div class='alert alert-danger text-center'>Order not found or you do not have permission to delete this order.</div>";
    }
}

$user_id = $_SESSION["user_id"];
$result = $conn->query("SELECT id, total_price, status, payment_method, transaction_id 
                        FROM orders 
                        WHERE user_id = {$user_id} 
                        ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            color: #333;
        }

        .container {
            margin-top: 50px;
        }

        h2 {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }

        .order-item {
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-item .order-details {
            flex-grow: 1;
        }

        .order-item .order-actions {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
        }

        .order-item .order-actions .btn {
            font-size: 14px;
            margin-top: 10px;
        }

        .order-item .order-actions .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }

        .order-item .order-actions .btn-info {
            background-color: #3498db;
            border-color: #3498db;
        }

        .alert {
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }

        .alert-info {
            background-color: #17a2b8;
            color: white;
        }

        .footer {
            background-color: #2d3436;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Your Order History</h2>

    <!-- Display success message if available -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); // Remove message after displaying it ?>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="order-item">
                <div class="order-details">
                    <p><strong>Order ID:</strong> #<?= $row['id'] ?></p>
                    <p><strong>Total Price:</strong> $<?= $row['total_price'] ?></p>
                    <p><strong>Status:</strong> <?= ucfirst($row['status']) ?></p>
                    <p><strong>Payment Method:</strong> <?= !empty($row['payment_method']) ? strtoupper($row['payment_method']) : "N/A" ?></p>
                    <p><strong>Transaction ID:</strong> <?= !empty($row['transaction_id']) ? $row['transaction_id'] : "N/A" ?></p>
                </div>
                <div class="order-actions">
                    <a href="order_details.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View Details</a>
                    <?php if ($row['status'] === 'pending' || $row['status'] === 'cancelled'): ?>
                        <a href="orders.php?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                    <?php else: ?>
                        <span class="text-muted">Cannot delete</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">You have no past orders.</div>
    <?php endif; ?>

</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
include "footer.php"; // Include your footer
?>
