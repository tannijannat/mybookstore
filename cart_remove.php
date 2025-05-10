<?php
session_start();

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    unset($_SESSION['cart'][$book_id]);
}

header("Location: cart_view.php");
exit();
?>
