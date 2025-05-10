<?php
session_start();
include "db.php"; // Include your database connection file

// Include header
include "header.php"; 

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Calculate total price from the cart
$total_price = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $book_id => $qty) {
        $result = $conn->query("SELECT price FROM books WHERE id = $book_id");
        if ($row = $result->fetch_assoc()) {
            $total_price += $row["price"] * $qty;
        }
    }
} else {
    echo "<p class='alert alert-warning text-center'>Your cart is empty!</p>";
    include "footer.php"; // Include footer
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $phone = $_POST["phone"];
    $division = $_POST["division"];
    $city = $_POST["city"];
    $address_detail = $_POST["address"];
    $payment_method = $_POST["payment_method"];

    $full_address = "$full_name, $phone, $address_detail, $city, $division";
    $user_id = $_SESSION["user_id"];

    if ($payment_method === "cod") {
        // Place order immediately for Cash on Delivery
        $stmt = $conn->prepare("INSERT INTO orders (user_id, address, total_price, payment_method, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ssds", $user_id, $full_address, $total_price, $payment_method);
        $stmt->execute();
        $order_id = $conn->insert_id;

        foreach ($_SESSION['cart'] as $book_id => $qty) {
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

        unset($_SESSION['cart']);
        header("Location: orders.php");
        exit();
    } else {
        // Redirect to dummy_payment.php for online payment
        $_SESSION['checkout_data'] = [
            'user_id' => $user_id,
            'full_address' => $full_address,
            'total_price' => $total_price,
            'payment_method' => $payment_method,
            'cart' => $_SESSION['cart']
        ];
        header("Location: gateway_payment.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .checkout-container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .checkout-form h3 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .checkout-form .form-group {
            margin-bottom: 15px;
        }

        .checkout-form label {
            display: block;
            margin-bottom: 5px;
        }

        .checkout-form input, .checkout-form select, .checkout-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .checkout-form .btn-confirm {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .checkout-form .btn-confirm:hover {
            background-color: #218838;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #333;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="checkout-form">
        <h3>Checkout</h3>
        <form method="post" action="checkout.php" onsubmit="return validateForm();">

          
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" required placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" required placeholder="e.g. 01XXXXXXXXX">
            </div>
            <div class="form-group">
                <label for="division">Division</label>
                <select name="division" id="division" class="form-control" required>
                    <option value="">Select Division</option>
                    <option value="Dhaka">Dhaka</option>
                    <option value="Chattogram">Chattogram</option>
                    <option value="Khulna">Khulna</option>
                    <option value="Rajshahi">Rajshahi</option>
                    <option value="Barishal">Barishal</option>
                    <option value="Sylhet">Sylhet</option>
                    <option value="Rangpur">Rangpur</option>
                    <option value="Mymensingh">Mymensingh</option>
                </select>
            </div>
            <div class="form-group">
                <label for="city">City / District</label>
                <input type="text" name="city" id="city" class="form-control" required placeholder="e.g. Dhaka">
            </div>
            <div class="form-group">
                <label for="address">Full Address</label>
                <textarea name="address" id="address" rows="3" class="form-control" required placeholder="House no, Road no, Area"></textarea>
            </div>

            <h5 class="section-title">Payment Method</h5>
            <div class="form-group">
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="">Choose Payment Method</option>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="stripe">Stripe (Card Payment)</option>
                    <option value="cod">Cash on Delivery</option>
                </select>
            </div>

            <input type="hidden" name="total_price" value="<?= $total_price ?>">

            <button type="submit" class="btn-confirm">✅ Confirm & Place Order (৳<?= $total_price ?>)</button>
        </form>
    </div>
</div>

<script>
function validateForm() {
    const name = document.getElementById('full_name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();

    if (name.length < 2 || phone.length < 11 || address.length < 10) {
        alert("Please fill in all fields correctly.");
        return false;
    }
    return true;
}
</script>

<?php
// Include footer
include "footer.php";
?>

</body>
</html>
