<?php
session_start();

// Handle Add to Cart (for AJAX requests) - PUT THIS AT THE VERY TOP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    include 'db.php'; // DB connection
    
    // Ensure cart session exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $name  = $_POST['name'] ?? '';
    $price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
    $image = $_POST['image'] ?? '';

    // Validate input
    if (empty($name) || $price <= 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid item data'
        ]);
        exit();
    }

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
        'message' => 'Item added to cart!',
        'item_name' => $name
    ]);
    exit();
}

// Regular page load continues below
include 'db.php';
include 'navbar.php';

// Ensure cart session exists for regular page load
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
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
    /* Your existing CSS styles remain the same */
    body, html { 
      height: 100%; 
      margin: 0; 
      padding: 0; 
      background-color: #F8F8F8;
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
      border: none;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .add-cart-btn:hover {
      background-color: #333;
      color: #fff;
      transform: scale(1.1);
    }

    .add-cart-btn.loading {
      background-color: #666;
      color: #fff;
      cursor: not-allowed;
    }

    .login-bg-blur { display: none; }

    .menu-container {
      background-color: transparent; 
      border-radius: 0;
      padding: 20px 0; 
      box-shadow: none;
      max-width: 1400px;
      margin: auto;
    }
    
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
        margin-left: 0.5em;
    }
    
    .filter-btns .dropdown-menu {
        border: 1px solid #EBEBEB;
        border-radius: 0;
    }
    
    .filter-btns .dropdown-item:hover {
        background-color: #F0F0F0;
    }

    .menu-card { 
      border: none; 
      background-color: transparent; 
      border-radius: 0;
      transition: none;
    }
    
    .menu-card:hover { 
      transform: none; 
      box-shadow: none; 
    }
    
    .card-img-top {
      aspect-ratio: 4/5;
      object-fit: cover;
      display: block;
      transition: opacity 0.3s;
    }
    
    .menu-card:hover .card-img-top {
        opacity: 0.9;
    }
    
    .card-body {
      padding: 20px 0 0 0; 
      text-align: center;
    }
    
    .card-title {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 400;
      font-size: 1.2rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 5px;
    }

    .card-text, .text-muted {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300;
      font-size: 1rem;
      color: #555 !important;
      margin-top: 5px;
      margin-bottom: 0;
    }
    
    .add-to-cart { display: none; }

    /* Enhanced Toast Notification Styles */
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1055;
    }

    .toast {
      background-color: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      font-family: 'Cormorant Garamond', serif;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .toast-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #e0e0e0;
      padding: 12px 16px;
    }

    .toast-header strong {
      font-weight: 600;
      color: #2c5530;
      font-size: 1.1rem;
    }

    .toast-body {
      padding: 16px;
      color: #333;
      font-size: 1.05rem;
      font-weight: 500;
    }

    .btn-close {
      font-size: 0.8rem;
    }

    /* Success state for toast */
    .toast-success .toast-header {
      background-color: #d4edda;
      border-bottom-color: #c3e6cb;
    }

    .toast-success .toast-header strong {
      color: #155724;
    }

    /* Error state for toast */
    .toast-error .toast-header {
      background-color: #f8d7da;
      border-bottom-color: #f5c6cb;
    }

    .toast-error .toast-header strong {
      color: #721c24;
    }

    /* Animation for toast */
    .toast {
      animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    /* Pulse animation for cart button feedback */
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }

    .add-cart-btn.pulse {
      animation: pulse 0.3s ease-in-out;
    }

    .hidden { display: none !important; }
    .return-home { text-align: center; margin-top: 4rem; }
    .return-home .btn { border-radius: 0; padding: 10px 30px; }
  </style>
</head>
<body>

<div class="login-bg-blur"></div>

<!-- Enhanced Toast Notification -->
<div class="toast-container">
  <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
    <div class="toast-header">
      <strong class="me-auto">Success!</strong>
      <small class="text-muted">Just now</small>
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
            <li><a class="dropdown-item filter-btn" href="#" data-filter="drinks">DRINKS</a></li>
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
                  <button type="submit" class="add-cart-btn position-relative">
                    +
                  </button>
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

  // AJAX for adding to cart with enhanced error handling
  document.addEventListener('DOMContentLoaded', function() {
    const toastEl = document.getElementById('cartToast');
    const toast = new bootstrap.Toast(toastEl);
    
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
        submitBtn.classList.add('loading');
        
        // Debug: Log what we're sending
        console.log('Sending form data:', {
          name: formData.get('name'),
          price: formData.get('price'),
          image: formData.get('image')
        });
        
        fetch('menu.php', {
          method: 'POST',
          body: formData,
          headers: {
            'Accept': 'application/json',
          },
        })
        .then(response => {
          console.log('Response status:', response.status);
          console.log('Response headers:', response.headers);
          
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          
          const contentType = response.headers.get('content-type');
          if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON');
          }
          
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          
          if (data.success) {
            // Update toast for success
            toastEl.classList.remove('toast-error');
            toastEl.classList.add('toast-success');
            document.querySelector('.toast-header strong').textContent = 'Success!';
            document.querySelector('.toast-body').textContent = `"${data.item_name}" added to cart successfully!`;
            
            // Show success toast
            toast.show();
            
            // Add pulse animation to button
            submitBtn.classList.remove('loading');
            submitBtn.classList.add('pulse');
            setTimeout(() => submitBtn.classList.remove('pulse'), 300);
            
            // Signal cart refresh to navbar and parent window
            if (window.parent !== window) {
              window.parent.postMessage("refresh-cart", "*");
            }
            
            // Update cart count in navbar if exists
            updateCartCount(data.cart_count);
            
            // Also use storage event as backup
            sessionStorage.setItem('cartUpdated', new Date().getTime());
          } else {
            throw new Error(data.message || 'Unknown error occurred');
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          
          // Update toast for error
          toastEl.classList.remove('toast-success');
          toastEl.classList.add('toast-error');
          document.querySelector('.toast-header strong').textContent = 'Error';
          document.querySelector('.toast-body').textContent = 'Error adding item to cart. Please try again.';
          toast.show();
        })
        .finally(() => {
          // Restore button after a brief delay for better UX
          setTimeout(() => {
            submitBtn.innerHTML = originalHTML;
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
          }, 500);
        });
      });
    });

    // Function to update cart count in navbar
    function updateCartCount(count) {
      // Update navbar cart count if the element exists
      const cartCountElements = document.querySelectorAll('.cart-count, #cart-count');
      cartCountElements.forEach(element => {
        element.textContent = count;
        element.style.display = count > 0 ? 'inline' : 'none';
      });
      
      // Also update parent window if in iframe
      if (window.parent !== window) {
        window.parent.postMessage({type: 'cartUpdate', count: count}, '*');
      }
    }

    // Listen for cart updates from other windows
    window.addEventListener('message', function(event) {
      if (event.data && event.data.type === 'cartUpdate') {
        updateCartCount(event.data.count);
      }
    });
  });

  // Filter functionality
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const category = this.getAttribute('data-filter');
      const items = document.querySelectorAll('.menu-item');

      items.forEach(item => {
        if (category === 'all' || item.classList.contains(category)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });

      // Update active filter state
      document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>

</body>
</html>