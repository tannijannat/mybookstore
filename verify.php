<?php
include "db.php";

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE verification_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<div class='alert alert-success'>Email verified! <a href='login.php'>Login now</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Invalid or expired verification link.</div>";
    }
}
?>
