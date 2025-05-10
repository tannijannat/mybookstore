<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    die("Unauthorized access.");
}

include "db.php";

if (isset($_GET["id"])) {
    $order_id = $_GET["id"];
    $conn->query("DELETE FROM orders WHERE order_id = '$order_id'");
}

header("Location: manage_orders.php");
exit();
?>
