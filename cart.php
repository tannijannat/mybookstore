<?php
session_start();
include "db.php";

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the book is already in the cart
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id]++;
    } else {
        $_SESSION['cart'][$book_id] = 1;
    }

    header("Location: cart_view.php");
    exit();
}
?>
