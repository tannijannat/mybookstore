<?php
session_start();
include "db.php";
include "header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $success = "Registration successful! <a href='login.php'>Login now</a>";
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register | BookBuddy</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to right, #f3f4f6, #e5e7eb);
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .register-card {
      background: white;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      color: #111827;
      max-width: 420px;
      width: 100%;
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
      from {
        transform: translateY(20px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .register-card h2 {
      font-weight: bold;
      margin-bottom: 10px;
    }

    .register-card p {
      margin-bottom: 20px;
      color: #4b5563;
    }

    .form-label {
      font-weight: 600;
      margin-bottom: 6px;
      display: block;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #d1d5db;
      border-radius: 10px;
      background-color: #f9fafb;
      margin-bottom: 16px;
      font-size: 14px;
    }

    input:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
    }

    .btn {
      display: inline-block;
      width: 100%;
      padding: 12px;
      font-weight: 600;
      border-radius: 12px;
      border: none;
      cursor: pointer;
    }

    .register-btn {
      background-color: #198754;
      color: white;
      margin-bottom: 12px;
    }

    .register-btn:hover {
      background-color: #157347;
    }

    .google-btn {
      background-color: #4f46e5;
      color: white;
      margin-top: 12px;
    }

    .google-btn i {
      margin-right: 8px;
    }

    .google-btn:hover {
      background-color: #3730a3;
    }

    .login-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 8px 0 12px 0;
    }

    .btn-login {
      padding: 8px 16px;
      background-color: #dc3545;
      color: white;
      font-weight: bold;
      border-radius: 10px;
      text-decoration: none;
    }

    .btn-login:hover {
      background-color: #c82333;
    }

    .alert {
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
    }

    .alert-success {
      background-color: #d1e7dd;
      color: #0f5132;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #842029;
    }

    a {
      color: #4338ca;
      font-weight: bold;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<main>
  <div class="register-card text-center">
    <div class="mb-4 text-center">
      <i class="fas fa-user-plus fa-3x mb-2"></i>
      <h2>Create Account</h2>
      <p>Join us to explore amazing books</p>
    </div>

    <?php if (!empty($success)) : ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (!empty($error)) : ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
      <label for="name" class="form-label">Full Name</label>
      <input type="text" name="name" id="name" required placeholder="Your full name">

      <label for="email" class="form-label">Email address</label>
      <input type="email" name="email" id="email" required placeholder="example@email.com">

      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" id="password" required placeholder="Choose a strong password">

      <button type="submit" class="btn register-btn">Register</button>

      <div class="login-link">
        <small>Already have an account?</small>
        <a href="login.php" class="btn-login">Login here</a>
      </div>

      <button type="button" onclick="window.location.href='google_login.php'" class="btn google-btn">
        <i class="fab fa-google"></i> Sign Up with Google
      </button>
    </form>
  </div>
</main>

<?php include "footer.php"; ?>
</body>
</html>
