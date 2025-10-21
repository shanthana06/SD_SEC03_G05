<?php
session_start();
include 'db.php';
include 'navbar.php';

// Debugging - display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Check login ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo '<div class="text-center mt-5">⚠ Please log in as a customer to place an order.</div>';
    exit;
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<div class="alert alert-warning text-center">Your cart is empty. <a href="menu.php">Add items first</a>.</div>';
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_type'] ?? '';
    $email = $_POST['email'] ?? '';
    $details = "";

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate payment method
    if (empty($method)) {
        $errors[] = "Please select a payment method.";
    }

    // Payment method specific validation
    switch ($method) {
        case 'card':
            $name = $_POST['card_name'] ?? '';
            $number = $_POST['card_number'] ?? '';
            $expiry = $_POST['card_expiry'] ?? '';
            $cvv = $_POST['card_cvv'] ?? '';
            
            if (empty($name)) {
                $errors[] = "Cardholder name is required.";
            }
            
            $cleanedNumber = str_replace(' ', '', $number);
            if (empty($cleanedNumber) || strlen($cleanedNumber) !== 16 || !is_numeric($cleanedNumber)) {
                $errors[] = "Invalid card number. Must be 16 digits.";
            }
            
            if (empty($expiry)) {
                $errors[] = "Expiry date is required.";
            } else {
                $parts = explode('/', $expiry);
                if (count($parts) !== 2 || strlen($parts[0]) !== 2 || strlen($parts[1]) !== 2) {
                    $errors[] = "Invalid expiry date format. Use MM/YY";
                }
            }
            
            if (empty($cvv) || strlen($cvv) < 3 || strlen($cvv) > 4 || !is_numeric($cvv)) {
                $errors[] = "Invalid CVV. Must be 3 or 4 digits.";
            }
            
            $details = "Name: $name, Number: " . substr($cleanedNumber, -4) . "XXXXXX, Expiry: $expiry";
            break;
            
        case 'online':
            $bank = $_POST['bank'] ?? '';
            if (empty($bank)) {
                $errors[] = "Please select a bank.";
            } else {
                $details = "Bank: $bank";
            }
            break;
            
        case 'ewallet':
            $wallet = $_POST['ewallet'] ?? '';
            $phone = $_POST['ewallet_phone'] ?? '';
            
            if (empty($wallet)) {
                $errors[] = "Please select an e-wallet.";
            }
            
            if (empty($phone)) {
                $errors[] = "Phone number is required.";
            } else {
                $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
                if (strlen($cleanedPhone) < 10 || strlen($cleanedPhone) > 11) {
                    $errors[] = "Invalid phone number. Must be 10-11 digits starting with 01.";
                }
            }
            
            $details = "Wallet: $wallet, Phone: $phone";
            break;
            
        case 'cash':
            $details = "Cash on Delivery/Pickup";
            break;
    }

    // If no errors, process payment
    if (empty($errors)) {
        $user_id = $_SESSION['user_id'];
        $order_type = "Food/Drink";
        $order_note = "Paid via $method";

        // Calculate total from cart
        $total_amount = 0;
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }
        }

        // Simulate payment processing
        $payment_successful = true;
        
        if (!$payment_successful) {
            $errors[] = "Payment failed. Please try again.";
        } else {
            // Start transaction for data consistency
            $conn->begin_transaction();
            
            try {
                // Insert into orders
                $stmt_order = $conn->prepare("
                    INSERT INTO orders (user_id, order_type, order_note, status, total_amount, created_at)
                    VALUES (?, ?, ?, 'Pending', ?, NOW())
                ");
                
                if (!$stmt_order) {
                    throw new Exception("Order preparation failed: " . $conn->error);
                }
                
                $stmt_order->bind_param("issd", $user_id, $order_type, $order_note, $total_amount);
                
                if (!$stmt_order->execute()) {
                    throw new Exception("Order execution failed: " . $stmt_order->error);
                }
                
                $order_id = $conn->insert_id;
                $stmt_order->close();

                // Insert order items
                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    $stmt_items = $conn->prepare("
                        INSERT INTO order_items (order_id, product_name, quantity, price)
                        VALUES (?, ?, ?, ?)
                    ");
                    
                    if (!$stmt_items) {
                        throw new Exception("Order items preparation failed: " . $conn->error);
                    }
                    
                    foreach ($_SESSION['cart'] as $item) {
                        $stmt_items->bind_param("isid", $order_id, $item['name'], $item['quantity'], $item['price']);
                        if (!$stmt_items->execute()) {
                            throw new Exception("Order items execution failed: " . $stmt_items->error);
                        }
                    }
                    $stmt_items->close();
                }

                // Insert into payments
                $stmt_payment = $conn->prepare("
                    INSERT INTO payments (order_id, user_id, payment_type, payment_details, amount, payment_date)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                
                if (!$stmt_payment) {
                    throw new Exception("Payment preparation failed: " . $conn->error);
                }
                
                $stmt_payment->bind_param("isssd", $order_id, $user_id, $method, $details, $total_amount);

                if (!$stmt_payment->execute()) {
                    throw new Exception("Payment execution failed: " . $stmt_payment->error);
                }
                
                $stmt_payment->close();
                
                // Commit transaction
                $conn->commit();
                
                // Store order ID in session and clear cart
                $_SESSION['last_order_id'] = $order_id;
                $_SESSION['cart'] = [];
                
                // Debug: Check if redirect works
                error_log("Redirecting to receipt.php?order_id=" . $order_id);
                
                // Redirect to receipt
                header("Location: receipt.php?order_id=" . $order_id);
                exit;
                
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollback();
                $errors[] = "Database error: " . $e->getMessage();
                error_log("Payment Error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment | Arjuna n Co-ffee</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Parisienne&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    /* Your existing CSS styles here */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body, html { height: 100%; margin: 0; padding: 0; background-color: #F8F8F8; font-family: 'Cormorant Garamond', serif; }
    .payment-wrapper { max-width: 1200px; margin: 80px auto 40px; padding: 0 20px; display: grid; grid-template-columns: 1fr 400px; gap: 60px; }
    .payment-section, .summary-section { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .brand-title { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; margin-bottom: 30px; color: #333; }
    /* ... rest of your CSS styles ... */
  </style>
</head>
<body>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
  <div class="loading-spinner"></div>
  <p>Processing payment...</p>
</div>

<div class="payment-wrapper">
  <!-- Left Column - Payment Form -->
  <div class="payment-section">
    <div class="brand-title">Arjuna n Co-ffee</div>
    
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <h5>Payment Failed!</h5>
        <?php foreach ($errors as $error): ?>
          <div><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="payment.php" id="payment-form">
      <!-- Contact Section -->
      <div class="contact-section">
        <h3 class="section-heading">Contact</h3>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email for order updates" 
                 value="<?= htmlspecialchars($_SESSION['email'] ?? ($_POST['email'] ?? '')) ?>" required>
          <div class="security-note">Email me with news and offers</div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- Payment Method Section -->
      <div class="payment-method-section">
        <h3 class="section-heading">Payment</h3>
        <div class="security-note">All transactions are secure and encrypted.</div>
        
        <div class="mb-4">
          <label class="form-label">Payment Method</label>
          <div class="custom-select-wrapper">
            <select class="form-select" name="payment_type" id="payment-type" required>
              <option value="">-- Choose Payment Method --</option>
              <option value="card" <?= ($_POST['payment_type'] ?? '') === 'card' ? 'selected' : '' ?>>Credit / Debit Card</option>
              <option value="online" <?= ($_POST['payment_type'] ?? '') === 'online' ? 'selected' : '' ?>>Online Banking</option>
              <option value="ewallet" <?= ($_POST['payment_type'] ?? '') === 'ewallet' ? 'selected' : '' ?>>E-Wallet</option>
              <option value="cash" <?= ($_POST['payment_type'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
            </select>
          </div>
        </div>

        <!-- Payment method sections (keep your existing HTML) -->
        <div id="card-section" class="d-none">
          <!-- Card form fields -->
        </div>

        <div id="online-section" class="d-none">
          <!-- Online banking options -->
        </div>

        <div id="ewallet-section" class="d-none">
          <!-- E-wallet options -->
        </div>

        <div id="cash-section" class="d-none">
          <div class="alert alert-info">Please prepare exact change. Payment will be made upon pickup/delivery.</div>
        </div>

        <button type="submit" class="pay-now-btn" id="pay-button">Pay Now</button>
        <a href="cart.php" class="back-btn">← Back to Cart</a>
      </div>
    </form>
  </div>

  <!-- Right Column - Order Summary -->
  <div class="summary-section">
    <h3 class="section-heading">Order Summary</h3>
    
    <div class="order-items">
      <?php 
      $total_amount = 0;
      if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): 
        foreach ($_SESSION['cart'] as $item): 
          $item_total = $item['price'] * $item['quantity'];
          $total_amount += $item_total;
      ?>
        <div class="total-row">
          <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
          <span>RM <?= number_format($item_total, 2) ?></span>
        </div>
      <?php endforeach; ?>
      <?php else: ?>
        <div class="text-muted">No items in cart</div>
      <?php endif; ?>
    </div>

    <div class="total-section">
      <div class="total-row final-total">
        <span>Total</span>
        <span>RM <?= number_format($total_amount, 2) ?></span>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentType = document.getElementById('payment-type');
    const paymentForm = document.getElementById('payment-form');
    const payButton = document.getElementById('pay-button');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Show/hide payment sections
    function updatePaymentSections() {
        document.querySelectorAll('[id$="-section"]').forEach(section => {
            section.classList.add('d-none');
        });
        
        if (paymentType.value) {
            const selectedSection = document.getElementById(paymentType.value + '-section');
            if (selectedSection) {
                selectedSection.classList.remove('d-none');
            }
        }
    }

    // Form submission - SIMPLIFIED VERSION
    paymentForm.addEventListener('submit', function(e) {
        console.log('Form submission started');
        
        // Basic validation
        let isValid = true;
        const paymentMethod = paymentType.value;
        
        // Validate email
        const emailInput = document.querySelector('input[name="email"]');
        if (!emailInput.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
            isValid = false;
            alert('Please enter a valid email address');
            e.preventDefault();
            return;
        }
        
        // Validate payment method
        if (!paymentMethod) {
            isValid = false;
            alert('Please select a payment method');
            e.preventDefault();
            return;
        }
        
        if (isValid) {
            console.log('Validation passed - showing loading');
            // Show loading overlay
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }
            if (payButton) {
                payButton.disabled = true;
                payButton.textContent = 'Processing...';
            }
            // Allow form to submit normally
        }
    });

    // Event listeners
    if (paymentType) {
        paymentType.addEventListener('change', updatePaymentSections);
        updatePaymentSections();
    }

    console.log('Payment page loaded successfully');
});
</script>
</body>
</html>