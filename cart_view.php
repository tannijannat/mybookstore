<?php
session_start();
include "db.php";
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Shopping Cart</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    table th, table td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }

    table th {
      background-color: #f0f0f0;
      font-weight: bold;
    }

    .btn-remove {
      background-color: crimson;
      color: white;
      padding: 6px 12px;
      text-decoration: none;
      border-radius: 5px;
      font-size: 13px;
    }

    .btn-remove:hover {
      background-color: darkred;
    }

    .btn-checkout {
      display: inline-block;
      background-color: green;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
    }

    .btn-checkout:hover {
      background-color: darkgreen;
    }

    .empty-cart {
      text-align: center;
      font-size: 18px;
      color: gray;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Shopping Cart</h2>

  <?php
  if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
      echo "<p class='empty-cart'>Your cart is empty.</p>";
  } else {
      echo '<table>';
      echo '<tr><th>Title</th><th>Quantity</th><th>Price</th><th>Total</th><th>Action</th></tr>';
      
      $total_price = 0;
      foreach ($_SESSION['cart'] as $book_id => $qty) {
          $result = $conn->query("SELECT * FROM books WHERE id = $book_id");
          if ($row = $result->fetch_assoc()) {
              $total = $row["price"] * $qty;
              $total_price += $total;
              echo "<tr>
                      <td>{$row['title']}</td>
                      <td>$qty</td>
                      <td>\${$row['price']}</td>
                      <td>\$" . number_format($total, 2) . "</td>
                      <td><a href='cart_remove.php?id=$book_id' class='btn-remove'>Remove</a></td>
                    </tr>";
          }
      }
      echo "<tr><td colspan='3'><strong>Total</strong></td><td><strong>\$" . number_format($total_price, 2) . "</strong></td><td></td></tr>";
      echo '</table>';
      
      // Checkout Button
      echo '<div style="text-align:right;"><a href="checkout.php" class="btn-checkout">Proceed to Checkout</a></div>';
  }
  ?>
</div>

</body>
</html>

<?php include "footer.php"; ?>
