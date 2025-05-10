<?php
session_start();
include "db.php";
include "header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_name"] = $row["name"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["role"] = $row["role"] ?? "user";

            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Email not found.";
    }
}
?>

<style>
    body {
        background-color: #f2f4f7;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .form-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 40px;
        max-width: 450px;
        margin: 50px auto;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .form-card h2 {
        font-size: 30px;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 5px;
    }

    .form-card p {
        color: #4b5563;
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .btn-login {
        width: 100%;
        background-color: #10b981;
        color: #fff;
        font-weight: bold;
        border: none;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-login:hover {
        background-color: #059669;
    }

    .google-btn {
        width: 100%;
        background-color: #4f46e5;
        color: #fff;
        font-weight: 600;
        padding: 10px;
        border-radius: 8px;
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
    }

    .google-btn:hover {
        background-color: #4338ca;
    }

    .redirect-links {
        text-align: center;
        margin-top: 15px;
    }

    .redirect-links a {
        color: #10b981;
        font-weight: bold;
        text-decoration: none;
        margin-left: 5px;
    }

    .redirect-links a:hover {
        text-decoration: underline;
    }

    .form-icon {
        font-size: 3rem;
        color: #1f2937;
        margin-bottom: 10px;
    }
</style>

<div class="form-card text-center">
    <div class="form-icon">
        <i class="fas fa-user-circle"></i>
    </div>
    <h2>Sign In</h2>
    <p>Welcome back! Please login to continue</p>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="text-start">
            <label for="email" class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" placeholder="example@email.com" required>

            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">Login</button>
    </form>

    <div class="redirect-links">
        <small>Don’t have an account?</small>
        <a href="register.php">Register here</a>
    </div>


      
      <button type="button" onclick="window.location.href='google_login.php'" class="btn google-btn">
        <i class="fab fa-google"></i> Sign Up with Google
    
    
   
</div>

<?php include "footer.php"; ?>
