<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function calculateTotal() {
    $total = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // UPDATE QUANTITY (increment / decrement)
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $index = $_POST['index'] ?? null;
        $change = $_POST['quantity_change'] ?? null;

        if ($index !== null && isset($_SESSION['cart'][$index])) {
            if ($change === 'increment') {
                $_SESSION['cart'][$index]['quantity'] += 1;
            } elseif ($change === 'decrement') {
                $_SESSION['cart'][$index]['quantity'] = max(1, $_SESSION['cart'][$index]['quantity'] - 1);
            }
        }
        header('Location: cart.php');
        exit();
    }

    // REMOVE ITEM
    if (isset($_POST['action']) && $_POST['action'] === 'remove') {
        $index = $_POST['index'] ?? null;
        if ($index !== null && isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
        }
        header('Location: cart.php');
        exit();
    }

    // CLEAR CART
    if (isset($_POST['action']) && $_POST['action'] === 'clear') {
        $_SESSION['cart'] = [];
        header('Location: cart.php');
        exit();
    }

    // CHECKOUT ACTION
    if (isset($_POST['action']) && $_POST['action'] === 'checkout') {
        // Redirect to payment page
        header('Location: payment.php');
        exit();
    }
}

$dummy_image_url = 'https://via.placeholder.com/120x120?text=Item';
$total = calculateTotal();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart | Arjuna n Co-ffee</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Global font styling */
    body, p, button, input, a, form, .item-name, .item-price, .cart-info-text, .checkout-btn-container, .remove-link {
      font-family: 'Cormorant Garamond', serif;
      letter-spacing: 0.3px;
    }

    h1, h2, h3, h4 {
      font-family: 'Playfair Display', serif;
    }

    .cart-title {
      font-family: 'Lora', serif;
      font-size: 28px;
      font-weight: 500;
      letter-spacing: 2px;
      margin: 0;
      text-transform: uppercase;
    }

    .cart-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px);
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
    }

    .cart-modal {
      position: fixed;
      top: 0;
      right: 0;
      width: 100%;
      max-width: 450px;
      height: 100%;
      background-color: #f7f7f7;
      box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
      overflow-y: auto;
      z-index: 1050;
      display: flex;
      flex-direction: column;
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 30px;
      border-bottom: 1px solid #eee;
      flex-shrink: 0;
    }

    .cart-close-btn {
      font-size: 30px;
      line-height: 1;
      border: none;
      background: none;
      cursor: pointer;
      color: #333;
      padding: 0;
    }

    .cart-body {
      padding: 30px;
      flex-grow: 1;
      overflow-y: auto;
    }

    .cart-item {
      display: flex;
      gap: 20px;
      padding-bottom: 20px;
      margin-bottom: 20px;
      border-bottom: 1px solid #eee;
    }

    .item-image-container {
      flex-shrink: 0;
      width: 120px;
      height: 120px;
      border: 1px solid #eee;
    }

    .item-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .item-details {
      flex-grow: 1;
    }

    .item-name {
      font-size: 16px;
      font-weight: 500;
      line-height: 1.5;
      margin-bottom: 5px;
      text-transform: uppercase;
      color: #333;
    }

    .item-price {
      font-size: 18px;
      margin-bottom: 15px;
      color: #555;
    }

    .quantity-controls {
      display: flex;
      align-items: center;
      border: 1px solid #ccc;
      width: fit-content;
    }

    .quantity-controls button {
      border: none;
      background: none;
      height: 35px;
      width: 35px;
      text-align: center;
      cursor: pointer;
      font-weight: 700;
      font-size: 18px;
    }

    .quantity-controls input {
      border: none;
      background: none;
      height: 35px;
      width: 40px;
      text-align: center;
      border-left: 1px solid #ccc;
      border-right: 1px solid #ccc;
    }

    .remove-btn {
      background: none;
      border: none;
      color: #888;
      text-decoration: underline;
      cursor: pointer;
      font-size: 14px;
      transition: color 0.2s;
      padding: 0;
      margin-left: 15px;
    }

    .remove-btn:hover {
      color: #d9534f;
    }

    .item-action-row {
      display: flex;
      align-items: center;
      margin-top: 10px;
    }

    .cart-footer {
      background-color: #f7f7f7;
      border-top: 1px solid #eee;
      flex-shrink: 0;
      padding: 0;
    }

    .checkout-btn {
      display: block;
      background-color: #000;
      color: #fff;
      text-decoration: none;
      padding: 15px 30px;
      font-size: 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      text-transform: uppercase;
      font-weight: 500;
      letter-spacing: 1px;
      transition: background-color 0.3s;
      border: none;
      width: 100%;
      cursor: pointer;
      font-family: 'Cormorant Garamond', serif;
    }

    .checkout-btn:hover {
      background-color: #333;
      color: #fff;
    }

    .checkout-link {
      display: block;
      background-color: #000;
      color: #fff;
      text-decoration: none;
      padding: 15px 30px;
      font-size: 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      text-transform: uppercase;
      font-weight: 500;
      letter-spacing: 1px;
      transition: background-color 0.3s;
      width: 100%;
      font-family: 'Cormorant Garamond', serif;
    }

    .checkout-link:hover {
      background-color: #333;
      color: #fff;
      text-decoration: none;
    }

    .empty-cart-actions {
      text-align: center;
      padding: 20px;
    }

    .btn-continue-shopping {
      background-color: #000;
      color: #fff;
      padding: 12px 30px;
      text-decoration: none;
      border: none;
      font-family: 'Cormorant Garamond', serif;
      font-size: 16px;
      letter-spacing: 1px;
      transition: background-color 0.3s;
    }

    .btn-continue-shopping:hover {
      background-color: #333;
      color: #fff;
    }

    /* Loading overlay */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.9);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      flex-direction: column;
    }

    .loading-spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #000;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin-bottom: 15px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>

<body>
<div class="cart-bg-blur"></div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
  <div class="loading-spinner"></div>
  <p>Redirecting to checkout...</p>
</div>

<div class="cart-modal">
  <div class="cart-header">
    <h1 class="cart-title">CART</h1>
    <button class="cart-close-btn">&times;</button>
  </div>

  <div class="cart-body">
    <?php if (!empty($_SESSION['cart'])): ?>
      <?php foreach ($_SESSION['cart'] as $index => $item): ?>
        <div class="cart-item">
          <div class="item-image-container">
            <img src="<?= htmlspecialchars($item['image'] ?? $dummy_image_url) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
          </div>

          <div class="item-details">
            <p class="item-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="item-price">RM <?= number_format($item['price'], 2) ?></p>

            <div class="item-action-row">
              <form method="post" class="quantity-controls">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="index" value="<?= $index ?>">
                <button type="submit" name="quantity_change" value="decrement">-</button>
                <input type="text" value="<?= $item['quantity'] ?>" readonly>
                <button type="submit" name="quantity_change" value="increment">+</button>
              </form>

              <form method="post" style="display: inline;">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="index" value="<?= $index ?>">
                <button type="submit" class="remove-btn">Remove</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center py-5" style="color: #555;">
        <p>Your cart is currently empty.</p>
        <div class="empty-cart-actions">
       <a href="menu.php" class="btn-continue-shopping" target="_top">Continue Shopping</a>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($_SESSION['cart'])): ?>
    <div class="cart-footer">
      <!-- OPTION 1: Direct link (most reliable) -->
      <a href="payment.php" class="checkout-link" id="directCheckoutLink">
        <span>CHECKOUT</span>
        <span>RM <?= number_format($total, 2) ?></span>
      </a>
      
      <!-- OPTION 2: Form method (backup) -->
      <form method="post" id="checkoutForm" style="display: none;">
        <input type="hidden" name="action" value="checkout">
        <button type="submit" class="checkout-btn" id="checkoutButton">
          <span>CHECKOUT</span>
          <span>RM <?= number_format($total, 2) ?></span>
        </button>
      </form>
    </div>
  <?php endif; ?>
</div>

<script>
// Close cart functionality
document.querySelector(".cart-close-btn").addEventListener("click", function() {
  if (window.parent !== window) {
    // If in iframe, tell parent to close
    window.parent.postMessage("close-cart", "*");
  } else {
    // If standalone page, redirect to menu
    window.location.href = "menu.php";
  }
});

// CHECKOUT FUNCTIONALITY - GUARANTEED TO WORK
document.addEventListener('DOMContentLoaded', function() {
  const directCheckoutLink = document.getElementById('directCheckoutLink');
  const checkoutForm = document.getElementById('checkoutForm');
  const loadingOverlay = document.getElementById('loadingOverlay');
  
  // Method 1: Direct link with loading overlay
  if (directCheckoutLink) {
    directCheckoutLink.addEventListener('click', function(e) {
      console.log('Direct checkout link clicked');
      
      // Show loading overlay
      if (loadingOverlay) {
        loadingOverlay.style.display = 'flex';
      }
      
      // If in iframe, we need to handle navigation differently
      if (window.parent !== window) {
        e.preventDefault();
        console.log('In iframe - redirecting parent window');
        
        // Redirect parent window to payment page
        setTimeout(function() {
          window.parent.location.href = 'payment.php';
        }, 500);
      } else {
        // Let the natural link navigation happen
        console.log('Standalone - allowing natural navigation');
      }
    });
  }
  
  // Method 2: Form submission (backup)
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function(e) {
      console.log('Checkout form submitted');
      
      // Show loading overlay
      if (loadingOverlay) {
        loadingOverlay.style.display = 'flex';
      }
      
      // If in iframe, we need to handle this differently
      if (window.parent !== window) {
        e.preventDefault();
        console.log('In iframe - handling form submission');
        
        // Submit via AJAX and then redirect parent
        fetch('cart.php', {
          method: 'POST',
          body: new FormData(checkoutForm)
        }).then(() => {
          window.parent.location.href = 'payment.php';
        });
      }
      // Otherwise, let the form submit normally
    });
  }
  
  // Debug information
  console.log('Cart page loaded successfully');
  console.log('Cart items:', <?= json_encode($_SESSION['cart']) ?>);
  console.log('Total: RM', <?= $total ?>);
  console.log('Is in iframe?', window.parent !== window);
  console.log('Current URL:', window.location.href);
});

// Listen for messages from parent (if in iframe)
window.addEventListener('message', function(event) {
  console.log('Cart received message:', event.data);
  
  if (event.data === 'close-cart') {
    if (window.parent !== window) {
      window.parent.postMessage("close-cart", "*");
    }
  }
  
  if (event.data === 'refresh-cart') {
    window.location.reload();
  }
  
  // Handle checkout message from parent
  if (event.data === 'checkout') {
    const checkoutLink = document.getElementById('directCheckoutLink');
    if (checkoutLink) {
      checkoutLink.click();
    }
  }
});

// Auto-trigger checkout if URL parameter exists
if (window.location.search.includes('checkout=true')) {
  console.log('Auto-triggering checkout from URL parameter');
  const checkoutLink = document.getElementById('directCheckoutLink');
  if (checkoutLink) {
    checkoutLink.click();
  }
}

// Ensure page is properly loaded
window.addEventListener('load', function() {
  console.log('Cart page fully loaded');
});
</script>

</body>
</html>