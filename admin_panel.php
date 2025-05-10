<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
include "header.php";
?>

<style>
    body {
        background-color: #f3f4f6;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .admin-panel {
        max-width: 600px;
        margin: 60px auto;
        background-color: #ffffff;
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .admin-panel h2 {
        font-size: 28px;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 30px;
    }

    .list-group a {
        display: block;
        background-color: #10b981;
        color: white;
        padding: 15px;
        border-radius: 10px;
        font-weight: 600;
        margin-bottom: 15px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .list-group a:hover {
        background-color: #059669;
    }

    .list-group a:last-child {
        margin-bottom: 0;
    }
</style>

<div class="admin-panel">
    <h2>Admin Panel</h2>
    <div class="list-group">
        <a href="manage_books.php">📚 Book Management</a>
        <a href="manage_orders.php">🛒 Order Management</a>
        <a href="manage_users.php">👥 User Management</a>
    </div>
</div>

<?php include "footer.php"; ?>
