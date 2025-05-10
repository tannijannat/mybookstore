<?php
session_start();
include "db.php"; // Include your database connection

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle updating user details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    if ($stmt->execute()) {
        $_SESSION["user_name"] = $name; // Update session variable
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>User Profile</h2>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
    </form>
    <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
</div>
</body>
</html>