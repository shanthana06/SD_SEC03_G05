<?php
session_start();
include 'db.php'; // DB connection

// Ensure cart session exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $name  = $_POST['name'];
    $price = (float) $_POST['price'];

    // Check if item already exists
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['name'] === $name) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    unset($item);

    // If not found, add new
    if (!$found) {
        $_SESSION['cart'][] = [
            'name'     => $name,
            'price'    => $price,
            'quantity' => 1
        ];
    }

    // Redirect straight to cart
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Menu | Arjuna n Co-ffee</title>
  <link href="style.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    body, html { height: 100%; margin: 0; padding: 0; }
    .login-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px);
      position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
    }
    .menu-container {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      padding: 40px 30px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .section-title { text-align: center; font-size: 2.2rem; margin-bottom: 1.5rem; color: #333; }
    .menu-card { transition: transform 0.3s ease; }
    .menu-card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
    .add-to-cart { transition: all 0.3s ease; }
    .add-to-cart:hover { background-color: #dcb78d !important; color: white; transform: scale(1.05); }
    .filter-btns { text-align: center; margin-bottom: 2rem; }
    .filter-btns .btn { margin: 0 10px 10px 10px; }
    .hidden { display: none !important; }
    .return-home { text-align: center; margin-top: 2rem; }
  </style>
</head>
<body>

<div id="navbar-placeholder"></div>
<?php include 'navbar.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script> AOS.init({ duration: 1000, once: true }); </script>

<div class="login-bg-blur"></div>

<section class="py-5">
  <div class="container menu-container">
    <h2 class="section-title">Our Menu</h2>

    <!-- Filter Buttons -->
    <div class="filter-btns">
      <button class="btn btn-outline-secondary filter-btn" data-filter="all">All</button>
      <button class="btn btn-outline-secondary filter-btn" data-filter="coffee">Coffee</button>
      <button class="btn btn-outline-secondary filter-btn" data-filter="food">Food</button>
      <button class="btn btn-outline-secondary filter-btn" data-filter="dessert">Dessert</button>
    </div>

    <div class="row g-4">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM menu_items WHERE availability='Available'");
      if (mysqli_num_rows($result) > 0) {
        $delay = 0;
        while ($row = mysqli_fetch_assoc($result)) {
          $delay += 100;
          $category = strtolower($row['category']); 
          ?>
          <div class="col-md-4 menu-item <?= $category ?>" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <div class="card menu-card">
              <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
              <div class="card-body text-center">
                <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                <p class="text-muted"><strong>RM <?= number_format($row['price'], 2) ?></strong></p>
                <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                <form method="post" action="menu.php">
                  <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                  <input type="hidden" name="price" value="<?= $row['price'] ?>">
                  <button type="submit" name="add_to_cart" class="btn btn-secondary add-to-cart">Add to Cart</button>
                </form>
              </div>
            </div>
          </div>
          <?php
        }
      } else {
        echo "<p class='text-center'>No menu items available yet.</p>";
      }
      ?>
    </div>

    <div class="return-home">
      <a href="index.php" class="btn btn-outline-dark mt-4">Return to Home</a>
    </div>
  </div>
</section>

<script>
  const filterBtns = document.querySelectorAll('.filter-btn');
  const menuItems = document.querySelectorAll('.menu-item');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.getAttribute('data-filter');
      menuItems.forEach(item => {
        if (filter === 'all') {
          item.classList.remove('hidden');
        } else {
          item.classList.toggle('hidden', !item.classList.contains(filter));
        }
      });
    });
  });
</script>
</body>
</html>
