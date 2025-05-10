<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
include "header.php";
include "db.php";

// Update order status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
}

// Delete order
if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    header("Location: manage_orders.php");
    exit();
}

// Fetch orders
$result = $conn->query("SELECT o.id AS order_id, u.name AS user_name, o.total_price, o.status FROM orders o JOIN users u ON o.user_id = u.id");
?>

<style>
.manage-orders-container {
    max-width: 1000px;
    margin: 40px auto;
    background: #fdfdfd;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
}
.manage-orders-container h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
    color: #333;
}
.orders-table {
    width: 100%;
    border-collapse: collapse;
}
.orders-table thead {
    background-color: #333;
    color: white;
}
.orders-table th, .orders-table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}
.orders-table select {
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
.orders-table .btn {
    padding: 6px 10px;
    font-size: 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin: 2px;
}
.btn-primary {
    background-color: #2e86de;
    color: white;
}
.btn-danger {
    background-color: #e74c3c;
    color: white;
}
.btn:hover {
    opacity: 0.9;
}
@media (max-width: 768px) {
    .orders-table th, .orders-table td {
        font-size: 13px;
        padding: 8px;
    }
    .btn {
        font-size: 12px;
        padding: 5px 8px;
    }
}
</style>

<div class="manage-orders-container">
    <h2>Manage Orders</h2>
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["order_id"]); ?></td>
                <td><?= htmlspecialchars($row["user_name"]); ?></td>
                <td>$<?= number_format($row["total_price"], 2); ?></td>
                <td>
                    <form method="post" style="display:flex; gap:6px; justify-content:center;">
                        <select name="status" required>
                            <option value="pending" <?= $row["status"] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?= $row["status"] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="canceled" <?= $row["status"] == 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                        </select>
                        <input type="hidden" name="order_id" value="<?= $row["order_id"]; ?>">
                        <button type="submit" name="update_order" class="btn btn-primary">Update</button>
                    </form>
                </td>
                <td>
                    <a href="?delete=<?= $row["order_id"]; ?>" class="btn btn-danger" onclick="return confirm('Delete this order?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>
