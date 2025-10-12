<?php
session_start();
include 'db.php'; // DB connection
include 'navbar.php';

// Ensure cart session exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart (for AJAX requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $name  = $_POST['name'];
    $price = (float) $_POST['price'];
    $image = $_POST['image'] ?? '';

    // Check if item already exists
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['name'] === $name) {
            $cart_item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    unset($cart_item);

    // If not found, add new
    if (!$found) {
        $_SESSION['cart'][] = [
            'name'     => $name,
            'price'    => $price,
            'image'    => $image,
            'quantity' => 1
        ];
    }

    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'cart_count' => count($_SESSION['cart']),
        'message' => 'Item added to cart!'
    ]);
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
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Parisienne&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">

  <style>
    /* 1. MINIMALIST GLOBAL STYLES */
    body, html { 
      height: 100%; 
      margin: 0; 
      padding: 0; 
      background-color: #F8F8F8; /* Light gray/off-white background */
      font-family: 'Cormorant Garamond', serif; 
      color: #333;
    }
    .image-wrapper {
  position: relative;
  display: inline-block;
}

.add-cart-btn {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background-color: white;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: #333;
  text-decoration: none;
  
  transition: 0.3s ease;
}

.add-cart-btn:hover {
  background-color: #333;
  color: #fff;
  transform: scale(1.1);
}

    /* Remove the blurred background image */
    .login-bg-blur { display: none; }

    /* Remove the menu container box for a full-page content look */
    .menu-container {
      background-color: transparent; 
      border-radius: 0;
      padding: 20px 0; 
      box-shadow: none;
      max-width: 1400px; /* Wider container for spacious feel */
      margin: auto; /* Center the container */
    }
    
    /* 2. TOP HEADER BAR (Product Count) */
    .menu-header-bar {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px 0;
      border-top: 1px solid #EBEBEB;
      border-bottom: 1px solid #EBEBEB;
      margin-bottom: 40px;
    }
    .menu-header-bar h2 {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300; 
      font-size: 1.1rem;
      letter-spacing: 5px; 
      text-transform: uppercase;
      margin: 0;
    }

    /* 3. SUB HEADER BAR (Filters and Sort) */
    .sub-header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 40px;
      margin-bottom: 40px;
    }

    .filter-btns .dropdown-toggle, .sort-by .btn {
      font-size: 0.9rem;
      border: none;
      color: #333;
      font-weight: 400;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 5px 10px;
      background-color: transparent;
    }
    .filter-btns .dropdown-toggle::after {
        margin-left: 0.5em; /* Space out the dropdown arrow */
    }
    .filter-btns .dropdown-menu {
        border: 1px solid #EBEBEB;
        border-radius: 0;
    }
    .filter-btns .dropdown-item:hover {
        background-color: #F0F0F0;
    }


    /* 4. MINIMALIST PRODUCT CARD STYLES */
    .menu-card { 
      border: none; 
      background-color: transparent; 
      border-radius: 0;
      transition: none; /* Disable original hover effect */
    }
    .menu-card:hover { 
      transform: none; 
      box-shadow: none; 
    }
    .card-img-top {
      aspect-ratio: 4/5; /* Taller aspect ratio for a premium look */
      object-fit: cover;
      display: block;
      transition: opacity 0.3s;
    }
    .menu-card:hover .card-img-top {
        opacity: 0.9; /* Subtle hover visual effect */
    }
    .card-body {
      padding: 20px 0 0 0; 
      text-align: center;
    }
    
    /* Product Name */
    .card-title {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 400; /* Regular weight for clarity */
      font-size: 1.2rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 5px;
    }

    /* Price and Description */
    .card-text, .text-muted {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300; /* Light weight for elegance */
      font-size: 1rem;
      color: #555 !important;
      margin-top: 5px;
      margin-bottom: 0;
    }
    
    /* Hiding the 'Add to Cart' button to match the minimalist product gallery style */
    .add-to-cart { display: none; }

    /* Toast notification styles */
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1055;
    }
    .toast {
      background-color: #fff;
      border: 1px solid #dee2e6;
      border-radius: 0;
      font-family: 'Cormorant Garamond', serif;
    }

    .hidden { display: none !important; }
    .return-home { text-align: center; margin-top: 4rem; }
    .return-home .btn { border-radius: 0; padding: 10px 30px; }
  </style>
</head>
<body>

<div class="login-bg-blur"></div>

<!-- Toast Notification -->
<div class="toast-container">
  <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto">Success</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      Item added to cart successfully!
    </div>
  </div>
</div>

<section class="py-5">
  <div class="container menu-container">

    <div class="menu-header-bar">
      <h2 id="product-count">PRODUCTS</h2> 
    </div>

    <div class="sub-header-bar">
      <div class="filter-btns dropdown">
        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          FILTER BY CATEGORY
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item filter-btn" href="#" data-filter="all">ALL</a></li>
            <li><a class="dropdown-item filter-btn" href="#" data-filter="coffee">COFFEE</a></li>
            <li><a class="dropdown-item filter-btn" href="#" data-filter="food">FOOD</a></li>
            <li><a class="dropdown-item filter-btn" href="#" data-filter="dessert">DESSERT</a></li>
        </ul>
      </div>

      <div class="sort-by text-end">
        <button class="btn" type="button">
           <span class="ms-2"></span>
        </button>
      </div>
    </div>


    <div class="row g-5 px-4">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM menu_items WHERE availability='Available'");
      if (mysqli_num_rows($result) > 0) {
        $delay = 0;
        $product_count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
          $delay += 100;
          $product_count++;
          $category = strtolower($row['category']); 
          ?>
          <div class="col-6 col-md-4 col-lg-3 menu-item <?= $category ?>" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <div class="card menu-card">
              <div class="image-wrapper">
                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                
                <form class="add-to-cart-form" method="post" style="position: absolute; bottom: 10px; right: 10px;">
                  <input type="hidden" name="add_to_cart" value="1">
                  <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                  <input type="hidden" name="price" value="<?= htmlspecialchars($row['price']) ?>">
                  <input type="hidden" name="image" value="uploads/<?= htmlspecialchars($row['image']) ?>">
                  <button type="submit" class="add-cart-btn">+</button>
                </form>
              </div>
              
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                <p class="text-muted">RM <?= number_format($row['price'], 2) ?></p>
              </div>
            </div>
          </div>
          <?php
        }
        echo "<script>document.getElementById('product-count').textContent = '{$product_count} PRODUCTS';</script>";
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script> 
  AOS.init({ duration: 1000, once: true }); 

  // AJAX for adding to cart
document.addEventListener('DOMContentLoaded', function() {
  const toast = new bootstrap.Toast(document.getElementById('cartToast'));
  
  document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const submitBtn = this.querySelector('.add-cart-btn');
      
      // Store original button content
      const originalHTML = submitBtn.innerHTML;
      
      // Add loading state
      submitBtn.innerHTML = '...';
      submitBtn.disabled = true;
      
      fetch('menu.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
  if (data.success) {
    // Show success toast
    toast.show();
    
    // FIX: Signal cart refresh to navbar
    if (window.parent && window.parent !== window) {
      window.parent.postMessage("refresh-cart", "*");
    } else {
      // Try to refresh cart if it's open
      if (typeof refreshCart !== 'undefined') {
        refreshCart();
      }
    }
    
    // Also use storage event as backup
    sessionStorage.setItem('cartUpdated', new Date().getTime());
  }
})
      .catch(error => {
        console.error('Error:', error);
        // Optional: Show error message
      })
      .finally(() => {
        // Restore button
        submitBtn.innerHTML = originalHTML;
        submitBtn.disabled = false;
      });
    });
  });

  // ✅ ADD THIS FUNCTION: Refresh cart modal
  function refreshCartModal() {
    // Method 1: If cart is open in an iframe, refresh it
    const cartIframe = document.querySelector('iframe[src*="cart.php"]');
    if (cartIframe) {
      cartIframe.contentWindow.location.reload();
    }
    
    // Method 2: If cart is open in a popup/modal window
    if (window.cartWindow && !window.cartWindow.closed) {
      window.cartWindow.location.reload();
    }
    
    // Method 3: Post message to parent window (if cart is embedded)
    if (window.parent !== window) {
      window.parent.postMessage('refreshCart', '*');
    }
    
    // Method 4: Trigger custom event for any listening cart modals
    window.dispatchEvent(new CustomEvent('cartUpdated'));
  }
});
</script>

</body>
</html>