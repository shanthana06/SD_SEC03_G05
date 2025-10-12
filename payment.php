<?php
session_start();
include 'db.php';
include 'navbar.php';

// --- Check login ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo '<div class="text-center mt-5">⚠ Please log in as a customer to place an order.</div>';
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

    // SIMPLIFIED VALIDATION
    switch ($method) {
        case 'card':
            $name = $_POST['card_name'] ?? '';
            $number = $_POST['card_number'] ?? '';
            $expiry = $_POST['card_expiry'] ?? '';
            $cvv = $_POST['card_cvv'] ?? '';
            
            // SIMPLIFIED Card validation
            if (empty($name)) {
                $errors[] = "Cardholder name is required.";
            }
            
            // Remove spaces and validate
            $cleanedNumber = str_replace(' ', '', $number);
            if (empty($cleanedNumber) || strlen($cleanedNumber) !== 16 || !is_numeric($cleanedNumber)) {
                $errors[] = "Invalid card number. Must be 16 digits.";
            }
            
            if (empty($expiry)) {
                $errors[] = "Expiry date is required.";
            } else {
                // Simple expiry validation
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
                // Simplified phone validation
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

        // --- Calculate total from cart ---
        $total_amount = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }
        }

        // Simulate payment processing
        $payment_successful = true;
        
        if (!$payment_successful) {
            $errors[] = "Payment failed. Please try again.";
        } else {
            // --- Insert into orders ---
            $stmt_order = $conn->prepare("
                INSERT INTO orders (user_id, order_type, order_note, status, total_amount, created_at)
                VALUES (?, ?, ?, 'Pending', ?, NOW())
            ");
            $stmt_order->bind_param("issd", $user_id, $order_type, $order_note, $total_amount);
            
            if ($stmt_order->execute()) {
                $order_id = $stmt_order->insert_id;
                $stmt_order->close();

                // --- Insert into payments ---
                $stmt_payment = $conn->prepare("
                    INSERT INTO payments (order_id, user_id, payment_type, payment_details, amount, payment_date)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt_payment->bind_param("isssd", $order_id, $user_id, $method, $details, $total_amount);

                if ($stmt_payment->execute()) {
                    // Clear cart after successful payment
                    $_SESSION['cart'] = [];
                    $success = true;
                } else {
                    $errors[] = "Payment processing failed. Please try again.";
                }
                $stmt_payment->close();
            } else {
                $errors[] = "Order creation failed. Please try again.";
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
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body, html { 
      height: 100%; 
      margin: 0; 
      padding: 0; 
      background-color: #F8F8F8;
      font-family: 'Cormorant Garamond', serif;
    }
    .payment-wrapper {
      max-width: 1200px;
      margin: 80px auto 40px;
      padding: 0 20px;
      display: grid;
      grid-template-columns: 1fr 400px;
      gap: 60px;
    }
    .payment-section {
      background: white;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .summary-section {
      background: white;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      height: fit-content;
    }
    .brand-title {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 30px;
      color: #333;
    }
    .section-heading {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: #333;
    }
    .form-label {
      font-weight: 500;
      margin-bottom: 8px;
      color: #333;
      display: block;
    }
    .form-control, .form-select {
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 12px 15px;
      font-size: 16px;
      font-family: 'Cormorant Garamond', serif;
      width: 100%;
      display: block;
    }
    .form-control:focus, .form-select:focus {
      border-color: #333;
      box-shadow: 0 0 0 2px rgba(51, 51, 51, 0.1);
      outline: none;
    }
    .form-control.is-invalid {
      border-color: #dc3545;
    }
    .invalid-feedback {
      display: block;
      color: #dc3545;
      font-size: 14px;
      margin-top: 5px;
    }
    .payment-method-section {
      margin-top: 30px;
    }
    .divider {
      height: 1px;
      background: #eee;
      margin: 30px 0;
    }
    .total-section {
      border-top: 1px solid #eee;
      padding-top: 20px;
      margin-top: 20px;
    }
    .total-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      font-size: 16px;
    }
    .final-total {
      font-weight: 600;
      font-size: 18px;
      color: #333;
    }
    .discount-section {
      display: flex;
      gap: 10px;
      margin: 20px 0;
    }
    .discount-input {
      flex: 1;
    }
    .apply-btn {
      background: #333;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-family: 'Cormorant Garamond', serif;
      font-size: 16px;
    }
    .apply-btn:hover {
      background: #555;
    }
    .bank-options {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .bank-option {
      position: relative;
    }
    .bank-radio {
      position: absolute;
      opacity: 0;
      width: 0;
      height: 0;
    }
    .bank-label {
      display: flex;
      align-items: center;
      padding: 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      background: white;
    }
    .bank-label:hover {
      border-color: #ccc;
      background: #f9f9f9;
    }
    .bank-radio:checked + .bank-label {
      border-color: #333;
      background: #f8f8f8;
    }
    .bank-logo {
      width: 40px;
      height: 30px;
      object-fit: contain;
      margin-right: 12px;
      border-radius: 4px;
    }
    .bank-label span {
      font-family: 'Cormorant Garamond', serif;
      font-size: 16px;
      font-weight: 500;
      color: #333;
    }
    .ewallet-options {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .ewallet-option {
      position: relative;
    }
    .ewallet-radio {
      position: absolute;
      opacity: 0;
      width: 0;
      height: 0;
    }
    .ewallet-label {
      display: flex;
      align-items: center;
      padding: 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      background: white;
    }
    .ewallet-label:hover {
      border-color: #ccc;
      background: #f9f9f9;
    }
    .ewallet-radio:checked + .ewallet-label {
      border-color: #333;
      background: #f8f8f8;
    }
    .ewallet-logo {
      width: 40px;
      height: 30px;
      object-fit: contain;
      margin-right: 12px;
      border-radius: 4px;
    }
    .ewallet-label span {
      font-family: 'Cormorant Garamond', serif;
      font-size: 16px;
      font-weight: 500;
      color: #333;
    }
    .pay-now-btn {
      background: #333;
      color: white;
      border: none;
      padding: 15px;
      width: 100%;
      border-radius: 4px;
      font-size: 18px;
      font-weight: 500;
      margin-top: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: 'Cormorant Garamond', serif;
      display: block;
    }
    .pay-now-btn:hover {
      background: #555;
      transform: translateY(-2px);
    }
    .pay-now-btn:active {
      transform: translateY(0);
    }
    .pay-now-btn:disabled {
      background: #ccc;
      cursor: not-allowed;
      transform: none;
    }
    .back-btn {
      background: transparent;
      color: #333;
      border: 1px solid #ddd;
      padding: 15px;
      width: 100%;
      border-radius: 4px;
      font-size: 16px;
      margin-top: 10px;
      text-decoration: none;
      display: block;
      text-align: center;
      transition: all 0.3s ease;
      font-family: 'Cormorant Garamond', serif;
    }
    .back-btn:hover {
      background: #f5f5f5;
      text-decoration: none;
      color: #333;
      transform: translateY(-2px);
    }
    .security-note {
      font-size: 14px;
      color: #666;
      margin-top: 10px;
    }
    .d-none { display: none !important; }
    .alert {
      font-family: 'Cormorant Garamond', serif;
      border-radius: 4px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .custom-select-wrapper {
      position: relative;
      display: block;
    }
    .custom-select-wrapper .form-select {
      padding-right: 50px;
      appearance: none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
      background-position: right 12px center;
      background-repeat: no-repeat;
      background-size: 16px;
    }
    .payment-option-icons {
      position: absolute;
      right: 40px;
      top: 50%;
      transform: translateY(-50%);
      display: flex;
      gap: 5px;
      pointer-events: none;
    }
    .payment-icon {
      width: 30px;
      height: 20px;
      object-fit: contain;
      border-radius: 3px;
    }
    .form-select option {
      padding: 8px 12px;
    }
    .card-logos {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
    .card-logo {
      width: 50px;
      height: 30px;
      object-fit: contain;
      border: 1px solid #eee;
      border-radius: 4px;
      padding: 3px;
      background: white;
    }
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.95);
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

    /* Responsive design */
    @media (max-width: 768px) {
      .payment-wrapper {
        grid-template-columns: 1fr;
        gap: 30px;
        margin: 60px auto 20px;
      }
      
      .payment-section, .summary-section {
        padding: 25px;
      }
      
      .brand-title {
        font-size: 1.5rem;
      }
    }
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
    
    <?php if (!$success): ?>
    
    <!-- Display Errors -->
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
          <div><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="payment.php" id="payment-form">
  <div class="contact-section">
    <h3 class="section-heading">Contact</h3>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" placeholder="Enter your email for order updates" 
             value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required>
      <div class="security-note">Email me with news and offers</div>
    </div>
  </div>

    <div class="divider"></div>

    <form method="POST" action="payment.php" id="payment-form">
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

        <!-- Card -->
        <div id="card-section" class="d-none">
          <div class="mb-3">
            <label class="form-label">Card number</label>
            <input type="text" name="card_number" class="form-control" placeholder="4111 1111 1111 1111" maxlength="19" value="<?= htmlspecialchars($_POST['card_number'] ?? '') ?>">
            <div class="invalid-feedback" id="card-number-error"></div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label class="form-label">Expiration date (MM / YY)</label>
              <input type="text" name="card_expiry" class="form-control" placeholder="12/25" value="<?= htmlspecialchars($_POST['card_expiry'] ?? '') ?>">
              <div class="invalid-feedback" id="card-expiry-error"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Security code</label>
              <input type="password" name="card_cvv" class="form-control" placeholder="123" maxlength="4" value="<?= htmlspecialchars($_POST['card_cvv'] ?? '') ?>">
              <div class="invalid-feedback" id="card-cvv-error"></div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Name on card</label>
            <input type="text" name="card_name" class="form-control" placeholder="John Doe" value="<?= htmlspecialchars($_POST['card_name'] ?? '') ?>">
            <div class="invalid-feedback" id="card-name-error"></div>
          </div>
        </div>

        <!-- Online Banking -->
        <div id="online-section" class="d-none">
          <label class="form-label">Select Bank</label>
          <div class="bank-options">
            <?php
            $banks = [
                'maybank' => 'Maybank',
                'cimb' => 'CIMB', 
                'rhb' => 'RHB',
                'publicbank' => 'Public Bank'
            ];
            foreach ($banks as $key => $bank): 
                $checked = ($_POST['bank'] ?? '') === $bank ? 'checked' : '';
            ?>
            <div class="bank-option">
              <input type="radio" id="<?= $key ?>" name="bank" value="<?= $bank ?>" class="bank-radio" <?= $checked ?> required>
              <label for="<?= $key ?>" class="bank-label">
                <img src="images/<?= $key ?>.png" alt="<?= $bank ?>" class="bank-logo" onerror="this.style.display='none'">
                <span><?= $bank ?></span>
              </label>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="invalid-feedback" id="bank-error" style="display: block;"></div>
        </div>

        <!-- E-Wallet -->
        <div id="ewallet-section" class="d-none">
          <label class="form-label">Select E-Wallet</label>
          <div class="ewallet-options">
            <?php
            $ewallets = [
                'tng' => "Touch 'n Go",
                'boost' => 'Boost',
                'grabpay' => 'GrabPay'
            ];
            foreach ($ewallets as $key => $wallet): 
                $checked = ($_POST['ewallet'] ?? '') === $wallet ? 'checked' : '';
            ?>
            <div class="ewallet-option">
              <input type="radio" id="<?= $key ?>" name="ewallet" value="<?= $wallet ?>" class="ewallet-radio" <?= $checked ?> required>
              <label for="<?= $key ?>" class="ewallet-label">
                <img src="images/<?= $key ?>.png" alt="<?= $wallet ?>" class="ewallet-logo" onerror="this.style.display='none'">
                <span><?= $wallet ?></span>
              </label>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="mb-3 mt-3">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="ewallet_phone" class="form-control" placeholder="012-3456789" value="<?= htmlspecialchars($_POST['ewallet_phone'] ?? '') ?>">
            <div class="invalid-feedback" id="ewallet-phone-error"></div>
          </div>
          <div class="invalid-feedback" id="ewallet-error" style="display: block;"></div>
        </div>

        <!-- Cash -->
        <div id="cash-section" class="d-none">
          <div class="alert alert-info">Please prepare exact change. Payment will be made upon pickup/delivery.</div>
        </div>

        <button type="submit" class="pay-now-btn" id="pay-button">Pay Now</button>
        <a href="cart.php" class="back-btn">← Back to Cart</a>
      </div>
    </form>
    
    <?php else: ?>
      <!-- Success Message -->
      <div class="alert alert-success text-center">
        <h4>✅ Payment Successful!</h4>
        <p>Thank you for your purchase. Your order has been confirmed.</p>
      </div>
      <div class="text-center mt-4">
        <a href="index.php" class="btn btn-outline-dark me-2" style="padding: 12px 24px;">Return to Home</a>
        <a href="receipt.php" class="btn btn-outline-dark" style="padding: 12px 24px;">View Receipt</a>
      </div>
    <?php endif; ?>
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

    <div class="divider"></div>

    <div class="discount-section">
      <input type="text" class="form-control discount-input" placeholder="Discount code">
      <button type="button" class="apply-btn">Apply</button>
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

    // Format card number
    function formatCardNumber(input) {
        let value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = '';
        
        for (let i = 0; i < value.length && i < 16; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }
        
        input.value = formattedValue;
    }

    // Clear all validation errors
    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }

    // Form submission
    paymentForm.addEventListener('submit', function(e) {
        console.log('Form submit triggered for:', paymentType.value);
        
        clearValidationErrors();
        let isValid = true;
        const paymentMethod = paymentType.value;
        
        // Basic payment method validation
        if (!paymentMethod) {
            isValid = false;
            paymentType.classList.add('is-invalid');
            alert('Please select a payment method');
            e.preventDefault();
            return;
        }
        
        // Validate based on payment method
        switch (paymentMethod) {
            case 'card':
                const cardName = document.querySelector('input[name="card_name"]');
                const cardNumber = document.querySelector('input[name="card_number"]');
                const cardExpiry = document.querySelector('input[name="card_expiry"]');
                const cardCVV = document.querySelector('input[name="card_cvv"]');
                
                if (!cardName.value.trim()) {
                    isValid = false;
                    cardName.classList.add('is-invalid');
                    document.getElementById('card-name-error').textContent = 'Cardholder name is required';
                }
                
                const cleanedNumber = cardNumber.value.replace(/\s+/g, '');
                if (!cleanedNumber || cleanedNumber.length !== 16 || !/^\d+$/.test(cleanedNumber)) {
                    isValid = false;
                    cardNumber.classList.add('is-invalid');
                    document.getElementById('card-number-error').textContent = 'Invalid card number. Must be 16 digits';
                }
                
                if (!cardExpiry.value || !cardExpiry.value.includes('/')) {
                    isValid = false;
                    cardExpiry.classList.add('is-invalid');
                    document.getElementById('card-expiry-error').textContent = 'Invalid expiry date. Format: MM/YY';
                }
                
                if (!cardCVV.value || cardCVV.value.length < 3 || cardCVV.value.length > 4 || !/^\d+$/.test(cardCVV.value)) {
                    isValid = false;
                    cardCVV.classList.add('is-invalid');
                    document.getElementById('card-cvv-error').textContent = 'Invalid CVV. Must be 3 or 4 digits';
                }
                break;
                
            case 'online':
                const bankSelected = document.querySelector('input[name="bank"]:checked');
                if (!bankSelected) {
                    isValid = false;
                    document.getElementById('bank-error').textContent = 'Please select a bank';
                }
                break;
                
            case 'ewallet':
                const ewalletSelected = document.querySelector('input[name="ewallet"]:checked');
                const ewalletPhone = document.querySelector('input[name="ewallet_phone"]');
                
                if (!ewalletSelected) {
                    isValid = false;
                    document.getElementById('ewallet-error').textContent = 'Please select an e-wallet';
                }
                
                const cleanedPhone = ewalletPhone.value.replace(/[^0-9]/g, '');
                if (!cleanedPhone || cleanedPhone.length < 10 || cleanedPhone.length > 11) {
                    isValid = false;
                    ewalletPhone.classList.add('is-invalid');
                    document.getElementById('ewallet-phone-error').textContent = 'Invalid phone number. Must be 10-11 digits';
                }
                break;
        }
        
        if (!isValid) {
            e.preventDefault();
            console.log('Validation failed - preventing submission');
        } else {
            // Show loading overlay
            console.log('Validation passed - submitting form');
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

    // Card number formatting
    const cardNumberInput = document.querySelector('input[name="card_number"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            formatCardNumber(this);
        });
    }

   // Make all payment method labels clickable and responsive
document.querySelectorAll('.cash-label, .bank-label, .ewallet-label, .card-label').forEach(label => {
  label.addEventListener('click', function() {
    const radio = this.previousElementSibling;
    if (radio) {
      radio.checked = true;
    }
  });
});


    console.log('Payment page loaded successfully');
});
</script>
</body>
</html>