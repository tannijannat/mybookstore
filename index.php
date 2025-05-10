<?php
session_start();
include "db.php";
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap + Font Awesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9f9f9;
      color: #333;
    }

    .card {
      transition: transform 0.2s;
      border: none;
      border-radius: 1rem;
      overflow: hidden;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .card-img-top {
      height: 220px;
      object-fit: cover;
    }

    .star-rating {
      color: #f1c40f;
      font-size: 1rem;
    }

    .card-title a {
      color: #2c3e50;
      text-decoration: none;
    }

    .card-title a:hover {
      color: #007bff;
    }

    .swiper {
      padding: 1rem 0;
    }

    .swiper-slide {
      width: 300px;
    }

    .form-control, .btn {
      border-radius: 0.5rem;
    }

    .modal-body img {
      max-height: 80vh;
      object-fit: contain;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center">Welcome, <?= htmlspecialchars($_SESSION["user_name"] ?? "Guest"); ?>!</h2>
  <p class="text-center mb-4">Explore our collection of books and shop your favorites.</p>

  <!-- Search/Filter -->
  <form method="GET" action="index.php" class="mb-4">
    <div class="form-row">
      <div class="col-md-3 mb-2">
        <input type="text" name="search" class="form-control" placeholder="🔍 Search title or author" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
      </div>
      <div class="col-md-2 mb-2">
        <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" min="0" step="0.01">
      </div>
      <div class="col-md-2 mb-2">
        <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" min="0" step="0.01">
      </div>
      <div class="col-md-2 mb-2">
        <select name="rating" class="form-control">
          <option value="">Rating</option>
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?= $i ?>" <?= ($_GET['rating'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?> Star<?= $i > 1 ? 's' : '' ?> & up</option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-2 mb-2">
        <select name="category" class="form-control">
          <option value="">All Categories</option>
          <?php
          $catResult = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");
          while ($cat = $catResult->fetch_assoc()):
            $catName = $cat['category_name'];
          ?>
            <option value="<?= htmlspecialchars($catName) ?>" <?= ($_GET['category'] ?? '') === $catName ? 'selected' : '' ?>>
              <?= htmlspecialchars($catName) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-1 mb-2">
        <button class="btn btn-primary w-100" type="submit"><i class="fas fa-search"></i></button>
      </div>
    </div>
  </form>

  <!-- Book Slider -->
  <div class="swiper">
    <div class="swiper-wrapper">
      <?php
      $search = trim($_GET['search'] ?? '');
      $minPrice = is_numeric($_GET['min_price'] ?? '') ? (float) $_GET['min_price'] : null;
      $maxPrice = is_numeric($_GET['max_price'] ?? '') ? (float) $_GET['max_price'] : null;
      $ratingFilter = isset($_GET['rating']) ? (int) $_GET['rating'] : 0;
      $categoryFilter = $_GET['category'] ?? '';

      $query = "SELECT b.*, IFNULL(AVG(r.rating), 0) as avg_rating FROM books b 
                LEFT JOIN reviews r ON b.id = r.book_id 
                WHERE (b.title LIKE ? OR b.author LIKE ?)";
      $types = "ss";
      $params = ['%' . $search . '%', '%' . $search . '%'];

      if ($minPrice !== null) {
        $query .= " AND b.price >= ?";
        $types .= "d";
        $params[] = $minPrice;
      }

      if ($maxPrice !== null) {
        $query .= " AND b.price <= ?";
        $types .= "d";
        $params[] = $maxPrice;
      }

      if (!empty($categoryFilter)) {
        $query .= " AND b.category = ?";
        $types .= "s";
        $params[] = $categoryFilter;
      }

      $query .= " GROUP BY b.id";
      if ($ratingFilter > 0) {
        $query .= " HAVING avg_rating >= ?";
        $types .= "i";
        $params[] = $ratingFilter;
      }

      $stmt = $conn->prepare($query);
      $stmt->bind_param($types, ...$params);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
          $avg = $row['avg_rating'];
      ?>
        <div class="swiper-slide">
          <div class="card shadow-sm h-100">
            <img src="<?= htmlspecialchars($row['image'] ?? 'Lore.jpg'); ?>" class="card-img-top thumbnail" alt="<?= htmlspecialchars($row['title']); ?>" data-toggle="modal" data-target="#imageModal" data-image="<?= htmlspecialchars($row['image']); ?>">
            <div class="card-body">
              <h5 class="card-title">
                <a href="description.php?id=<?= $row['id']; ?>"><?= htmlspecialchars($row["title"]); ?></a>
              </h5>
              <p class="card-text">Author: <?= htmlspecialchars($row["author"]); ?></p>
              <p class="card-text">Price: $<?= htmlspecialchars($row["price"]); ?></p>
              <p class="card-text mb-2">
                <strong>Rating:</strong>
                <?php
                if ($avg == 0) {
                  echo "No ratings yet.";
                } else {
                  for ($i = 1; $i <= 5; $i++) {
                    echo $i <= round($avg)
                      ? '<span class="star-rating">&#9733;</span>'
                      : '<span class="star-rating">&#9734;</span>';
                  }
                }
                ?>
              </p>
              <?php if (isset($_SESSION["user_id"])): ?>
                <a href="cart.php?id=<?= $row["id"]; ?>" class="btn btn-primary btn-sm">Add to Cart</a>
              <?php else: ?>
                <button class="btn btn-warning btn-sm" onclick="redirectToLogin();">Add to Cart</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <div class="swiper-slide text-center">
          <img src="no-books.svg" alt="No Books" style="max-width: 150px; opacity: 0.7;">
          <p>No books found matching your criteria.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Book Image</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <img id="modalImage" src="" class="img-fluid" alt="Book Image">
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script>
function redirectToLogin() {
  alert("You are not logged in. Please log in first.");
  window.location.href = "login.php";
}

document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll(".thumbnail").forEach(img => {
    img.addEventListener("click", () => {
      document.getElementById("modalImage").src = img.dataset.image;
    });
  });
});
</script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
 const swiper = new Swiper(".swiper", {
  slidesPerView: "auto",
  spaceBetween: 20,
  freeMode: true,
  grabCursor: true,
  keyboard: { enabled: true },
  autoplay: {
    delay: 1000,
    disableOnInteraction: false
  },
  speed: 600
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<?php include "footer.php"; ?>
</body>
</html>
