<?php
// payment.php - COMPLETELY FIXED VERSION
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

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

// ToyyibPay Configuration
define('TOYYIBPAY_USER_SECRET_KEY', '5ym2mcoj-yc0r-6dx4-7984-h6yrha3f21pn');
define('TOYYIBPAY_CATEGORY_CODE', 'ijmycgdi');
define('TOYYIBPAY_BASE_URL', 'https://toyyibpay.com/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_type'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $details = "";

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate phone
    if (empty($phone)) {
        $errors[] = "Please enter your phone number.";
    } else {
        $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleanedPhone) < 10 || strlen($cleanedPhone) > 11) {
            $errors[] = "Invalid phone number. Must be 10-11 digits starting with 01.";
        } else {
            $_SESSION['phone'] = $cleanedPhone;
        }
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
            $ewallet_phone = $_POST['ewallet_phone'] ?? '';
            
            if (empty($wallet)) {
                $errors[] = "Please select an e-wallet.";
            }
            
            if (empty($ewallet_phone)) {
                $errors[] = "E-wallet phone number is required.";
            } else {
                $cleanedEwalletPhone = preg_replace('/[^0-9]/', '', $ewallet_phone);
                if (strlen($cleanedEwalletPhone) < 10 || strlen($cleanedEwalletPhone) > 11) {
                    $errors[] = "Invalid e-wallet phone number. Must be 10-11 digits starting with 01.";
                }
            }
            
            $details = "Wallet: $wallet, Phone: $ewallet_phone";
            break;
            
        case 'toyyibpay':
            $details = "ToyyibPay Gateway";
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

        // Handle ToyyibPay separately
        if ($method === 'toyyibpay') {
            if ($conn instanceof mysqli) {
                $conn->begin_transaction();
            }
            
            try {
                // Insert into orders with pending status
                $stmt_order = $conn->prepare("
                    INSERT INTO orders (user_id, order_type, order_note, status, total_amount, created_at)
                    VALUES (?, ?, ?, 'Pending Payment', ?, NOW())
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

                // Insert into payments with pending status
                $stmt_payment = $conn->prepare("
                    INSERT INTO payments (order_id, user_id, payment_type, payment_details, amount, status, payment_date)
                    VALUES (?, ?, ?, ?, ?, 'pending', NOW())
                ");
                
                if (!$stmt_payment) {
                    throw new Exception("Payment preparation failed: " . $conn->error);
                }
                
                $stmt_payment->bind_param("isssd", $order_id, $user_id, $method, $details, $total_amount);

                if (!$stmt_payment->execute()) {
                    throw new Exception("Payment execution failed: " . $stmt_payment->error);
                }
                
                $payment_id = $conn->insert_id;
                $stmt_payment->close();
                
                // FIXED: Update user phone using correct column name (user_id)
                if (!empty($phone)) {
                    $stmt_update_phone = $conn->prepare("UPDATE users SET phone = ? WHERE user_id = ?");
                    $stmt_update_phone->bind_param("si", $phone, $user_id);
                    $stmt_update_phone->execute();
                    $stmt_update_phone->close();
                }
                
                // Commit transaction
                if ($conn instanceof mysqli) {
                    $conn->commit();
                }
                
                // Store order ID in session
                $_SESSION['toyyibpay_order_id'] = $order_id;
                $_SESSION['toyyibpay_payment_id'] = $payment_id;
                $_SESSION['email'] = $email;
                $_SESSION['phone'] = $phone;
                
                // Redirect to ToyyibPay
                header("Location: toyyibpay-process.php?order_id=" . $order_id);
                exit;
                
            } catch (Exception $e) {
                if ($conn instanceof mysqli) {
                    $conn->rollback();
                }
                $errors[] = "Database error: " . $e->getMessage();
                error_log("ToyyibPay Order Error: " . $e->getMessage());
            }
        } else {
            // Process other payment methods
            $payment_successful = true;
            
            if (!$payment_successful) {
                $errors[] = "Payment failed. Please try again.";
            } else {
                if ($conn instanceof mysqli) {
                    $conn->begin_transaction();
                }
                
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

                    // FIXED: Update user phone using correct column name (user_id)
                    if (!empty($phone)) {
                        $stmt_update_phone = $conn->prepare("UPDATE users SET phone = ? WHERE user_id = ?");
                        $stmt_update_phone->bind_param("si", $phone, $user_id);
                        $stmt_update_phone->execute();
                        $stmt_update_phone->close();
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
                    
                    // Commit transaction if supported
                    if ($conn instanceof mysqli) {
                        $conn->commit();
                    }
                    
                    // Store order ID in session and clear cart
                    $_SESSION['last_order_id'] = $order_id;
                    $_SESSION['cart'] = [];
                    
                    // Redirect to receipt
                    header("Location: receipt.php?order_id=" . $order_id);
                    exit;
                    
                } catch (Exception $e) {
                    // Rollback transaction if supported
                    if ($conn instanceof mysqli) {
                        $conn->rollback();
                    }
                    $errors[] = "Database error: " . $e->getMessage();
                    error_log("Payment Error: " . $e->getMessage());
                }
            }
        }
    }
}

// Now include navbar after all PHP processing
include 'navbar.php';
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
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body, html { height: 100%; margin: 0; padding: 0; background-color: #F8F8F8; font-family: 'Cormorant Garamond', serif; }
    .payment-wrapper { max-width: 1200px; margin: 80px auto 40px; padding: 0 20px; display: grid; grid-template-columns: 1fr 400px; gap: 60px; }
    .payment-section, .summary-section { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .brand-title { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; margin-bottom: 30px; color: #333; }
    .section-heading { font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    .security-note { font-size: 0.85rem; color: #666; margin-top: 5px; }
    .divider { height: 1px; background: #eee; margin: 30px 0; }
    .form-label { font-weight: 500; margin-bottom: 8px; display: block; color: #333; }
    .form-control, .form-select { padding: 12px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
    .pay-now-btn { background: #2a9d8f; color: white; border: none; padding: 15px 30px; border-radius: 4px; font-size: 1.1rem; cursor: pointer; width: 100%; margin: 20px 0 10px; }
    .pay-now-btn:hover { background: #24867a; }
    .back-btn { color: #666; text-decoration: none; display: inline-block; margin-top: 10px; }
    .order-items { margin-bottom: 20px; }
    .total-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
    .final-total { font-weight: bold; font-size: 1.2rem; border-bottom: none; margin-top: 10px; }
    .loading-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); color: white; justify-content: center; align-items: center; flex-direction: column; z-index: 9999; }
    .loading-spinner { border: 4px solid #f3f3f3; border-top: 4px solid #2a9d8f; border-radius: 50%; width: 50px; height: 50px; animation: spin 2s linear infinite; margin-bottom: 20px; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .d-none { display: none; }
    .toyyibpay-info { background: #f8f9fa; border-left: 4px solid #2a9d8f; padding: 15px; margin: 15px 0; }
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
          <label class="form-label">Email *</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email for order updates" 
                 value="<?= htmlspecialchars($_SESSION['email'] ?? ($_POST['email'] ?? '')) ?>" required>
          <div class="security-note">Email me with news and offers</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone Number *</label>
          <input type="tel" name="phone" class="form-control" placeholder="01X-XXXX XXXX" 
                 value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
          <div class="security-note">Required for order updates and payment verification</div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- Payment Method Section -->
      <div class="payment-method-section">
        <h3 class="section-heading">Payment</h3>
        <div class="security-note">All transactions are secure and encrypted.</div>
        
        <div class="mb-4">
          <label class="form-label">Payment Method *</label>
          <div class="custom-select-wrapper">
            <select class="form-select" name="payment_type" id="payment-type" required>
              <option value="">-- Choose Payment Method --</option>
              <option value="card" <?= ($_POST['payment_type'] ?? '') === 'card' ? 'selected' : '' ?>>Credit / Debit Card</option>
              <option value="online" <?= ($_POST['payment_type'] ?? '') === 'online' ? 'selected' : '' ?>>Online Banking</option>
              <option value="ewallet" <?= ($_POST['payment_type'] ?? '') === 'ewallet' ? 'selected' : '' ?>>E-Wallet</option>
              <option value="toyyibpay" <?= ($_POST['payment_type'] ?? '') === 'toyyibpay' ? 'selected' : '' ?>>ToyyibPay (FPX & E-Wallet)</option>
              <option value="cash" <?= ($_POST['payment_type'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
            </select>
          </div>
        </div>

        <!-- Card Payment Section -->
        <div id="card-section" class="d-none">
          <div class="mb-3">
            <label class="form-label">Cardholder Name *</label>
            <input type="text" name="card_name" class="form-control" placeholder="Name on card" 
                   value="<?= htmlspecialchars($_POST['card_name'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Card Number *</label>
            <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" 
                   value="<?= htmlspecialchars($_POST['card_number'] ?? '') ?>">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Expiry Date *</label>
              <input type="text" name="card_expiry" class="form-control" placeholder="MM/YY" 
                     value="<?= htmlspecialchars($_POST['card_expiry'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">CVV *</label>
              <input type="text" name="card_cvv" class="form-control" placeholder="123" 
                     value="<?= htmlspecialchars($_POST['card_cvv'] ?? '') ?>">
            </div>
          </div>
        </div>

        <!-- Online Banking Section -->
        <div id="online-section" class="d-none">
          <div class="mb-3">
            <label class="form-label">Select Bank *</label>
            <select class="form-select" name="bank">
              <option value="">-- Choose Bank --</option>
              <option value="maybank" <?= ($_POST['bank'] ?? '') === 'maybank' ? 'selected' : '' ?>>Maybank2u</option>
              <option value="cimb" <?= ($_POST['bank'] ?? '') === 'cimb' ? 'selected' : '' ?>>CIMB Clicks</option>
              <option value="public" <?= ($_POST['bank'] ?? '') === 'public' ? 'selected' : '' ?>>Public Bank</option>
              <option value="rhb" <?= ($_POST['bank'] ?? '') === 'rhb' ? 'selected' : '' ?>>RHB Now</option>
            </select>
          </div>
          <div class="alert alert-info">
            You will be redirected to your bank's secure payment gateway.
          </div>
        </div>

        <!-- E-Wallet Section -->
        <div id="ewallet-section" class="d-none">
          <div class="mb-3">
            <label class="form-label">Select E-Wallet *</label>
            <select class="form-select" name="ewallet">
              <option value="">-- Choose E-Wallet --</option>
              <option value="touchngo" <?= ($_POST['ewallet'] ?? '') === 'touchngo' ? 'selected' : '' ?>>Touch 'n Go</option>
              <option value="grabpay" <?= ($_POST['ewallet'] ?? '') === 'grabpay' ? 'selected' : '' ?>>GrabPay</option>
              <option value="boost" <?= ($_POST['ewallet'] ?? '') === 'boost' ? 'selected' : '' ?>>Boost</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">E-Wallet Phone Number *</label>
            <input type="tel" name="ewallet_phone" class="form-control" placeholder="01X-XXXX XXXX" 
                   value="<?= htmlspecialchars($_POST['ewallet_phone'] ?? '') ?>">
          </div>
        </div>

        <!-- ToyyibPay Section -->
        <div id="toyyibpay-section" class="d-none">
          <div class="toyyibpay-info">
            <h6>Secure Payment via ToyyibPay</h6>
            <p class="mb-1">• FPX Online Banking</p>
            <p class="mb-1">• Credit/Debit Cards</p>
            <p class="mb-0">• Multiple E-Wallets</p>
          </div>
          <div class="alert alert-info">
            You will be redirected to ToyyibPay's secure payment gateway to complete your transaction.
          </div>
        </div>

        <!-- Cash Section -->
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

    // Run on page load
    updatePaymentSections();

    // Update when user changes payment method
    paymentType.addEventListener('change', updatePaymentSections);

    // Show loading overlay on submit
    paymentForm.addEventListener('submit', function() {
        loadingOverlay.style.display = 'flex';
    });
});
</script>
</body>
</html>
