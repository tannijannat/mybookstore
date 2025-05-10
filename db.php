<?php
$host = "localhost";
$user = "root";
$pass = ""; // Use your MySQL root password if set
$db = "book_storee";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

