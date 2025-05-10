<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookBuddy</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #1a237e;
      padding: 10px 20px;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar .logo img {
      max-height: 60px; /* Adjust this value for logo size */
      width: auto;  /* Keep aspect ratio */
    }

    .navbar ul {
      display: flex;
      list-style: none;
    }

    .navbar ul li {
      margin: 0 15px;
    }

    .navbar ul li a {
      color: white;
      text-decoration: none;
      font-size: 16px;
      font-weight: 500;
      transition: color 0.3s;
    }

    .navbar ul li a:hover {
      color: #ffc107;
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: white;
      min-width: 200px;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      z-index: 1;
    }

    .dropdown-content div, .dropdown-content a {
      color: #333;
      padding: 10px;
      text-decoration: none;
      display: block;
      border-bottom: 1px solid #ddd;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    .dropdown-content a:hover {
      background-color: #f0f0f0;
    }

    @media screen and (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar ul {
        flex-direction: column;
        width: 100%;
      }

      .navbar ul li {
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>

<header class="navbar">
  <div class="logo">
    <a href="index.php"><img src="images/sitelogo.png" alt="BookBuddy"></a>
  </div>

  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="team.php">Team</a></li>

    <?php if (isset($_SESSION["user_id"])): ?>
      <?php if ($_SESSION["role"] === "admin"): ?>
        <li><a href="admin_panel.php">Admin Panel</a></li>
      <?php else: ?>
        <li><a href="cart_view.php">Cart</a></li>
        <li><a href="orders.php">My Orders</a></li>
      <?php endif; ?>

      <li class="dropdown">
        <a href="#">Welcome, <?= $_SESSION["role"] === "admin" ? "Admin " : ""; ?><?= htmlspecialchars($_SESSION["user_name"]); ?> &#9662;</a>
        <div class="dropdown-content">
          <div>Username: <?= htmlspecialchars($_SESSION["user_name"]); ?></div>
          <div>Email: <?= htmlspecialchars($_SESSION["email"] ?? 'Not Available'); ?></div>
          <a href="logout.php" style="color: red;">Logout</a>
        </div>
      </li>
    <?php else: ?>
      <li><a href="login.php">Login</a></li>
      <li><a href="register.php">Register</a></li>
    <?php endif; ?>
  </ul>
</header>

</body>
</html>
