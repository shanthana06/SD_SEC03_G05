<?php
session_start();
include 'db.php';
include 'navbar.php';

// --- Check login ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo '<div class="text-center mt-5">⚠ Please log in as a customer to place an order.</div>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_type'];
    $details = "";

    switch ($method) {
        case 'card':
            $name = $_POST['card_name'];
            $number = $_POST['card_number'];
            $expiry = $_POST['card_expiry'];
            $cvv = $_POST['card_cvv'];
            $details = "Name: $name, Number: $number, Expiry: $expiry, CVV: $cvv";
            break;
        case 'online':
            $bank = $_POST['bank'];
            $details = "Bank: $bank";
            break;
        case 'ewallet':
            $wallet = $_POST['ewallet'];
            $phone = $_POST['ewallet_phone'];
            $details = "Wallet: $wallet, Phone: $phone";
            break;
        case 'cash':
            $details = "Cash on Delivery/Pickup";
            break;
    }

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

    // --- Insert into orders ---
    $stmt_order = $conn->prepare("
        INSERT INTO orders (user_id, order_type, order_note, status, total_amount, created_at)
        VALUES (?, ?, ?, 'Pending', ?, NOW())
    ");
    $stmt_order->bind_param("issd", $user_id, $order_type, $order_note, $total_amount);
    $stmt_order->execute();
    $order_id = $stmt_order->insert_id; // get order ID
    $stmt_order->close();

    // --- Insert into payments ---
   $stmt_payment = $conn->prepare("
    INSERT INTO payments (order_id, user_id, payment_type, payment_details, amount, payment_date)
    VALUES (?, ?, ?, ?, ?, NOW())
");
$stmt_payment->bind_param("isssd", $order_id, $user_id, $method, $details, $total_amount);


    $stmt_payment->execute();
    $stmt_payment->close();

    $success = true;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Payment | Arjuna n Co-ffee</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html { height: 100%; margin: 0; padding: 0; }
    .payment-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px);
      position: fixed; top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
    }
    .payment-container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 40px; border-radius: 12px;
      max-width: 600px; margin: 60px auto;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }
    .section-title { text-align: center; margin-bottom: 1.5rem; font-size: 2rem; color: #333; }
    .d-none { display: none !important; }
  </style>
</head>
<body>

<div class="payment-bg-blur"></div>

<div class="payment-container">
  <h2 class="section-title">Choose Payment Method</h2>

  <?php if (!isset($success)) { ?>
  <form method="POST" action="payment.php" id="payment-form">
    <div class="mb-3 payment-methods">
      <label class="form-label">Select a method:</label>
      <select class="form-select" name="payment_type" id="payment-type" required>
        <option value="">-- Choose Payment Method --</option>
        <option value="card">Credit / Debit Card</option>
        <option value="online">Online Banking</option>
        <option value="ewallet">E-Wallet</option>
        <option value="cash">Cash</option>
      </select>
    </div>

    <!-- Card -->
    <div id="card-section" class="d-none">
      <input type="text" name="card_name" class="form-control mb-2" placeholder="Cardholder Name">
      <input type="text" name="card_number" class="form-control mb-2" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19">
      <div class="row">
        <div class="col-md-6"><input type="text" name="card_expiry" class="form-control mb-2" placeholder="MM/YY"></div>
        <div class="col-md-6"><input type="password" name="card_cvv" class="form-control mb-2" placeholder="CVV"></div>
      </div>
    </div>

    <!-- Online -->
    <div id="online-section" class="d-none">
      <select name="bank" class="form-select">
        <option>Maybank</option><option>CIMB</option><option>RHB</option><option>Public Bank</option>
      </select>
    </div>

    <!-- E-Wallet -->
    <div id="ewallet-section" class="d-none">
      <select name="ewallet" class="form-select mb-2">
        <option>Touch 'n Go</option><option>Boost</option><option>GrabPay</option>
      </select>
      <input type="tel" name="ewallet_phone" class="form-control" placeholder="Phone Number Linked">
    </div>

    <!-- Cash -->
    <div id="cash-section" class="d-none">
      <div class="alert alert-info">Please prepare exact change. Payment will be made upon pickup/delivery.</div>
    </div>

    <!-- Buttons -->
    <div class="d-flex gap-3 mt-4">
       <button type="submit" class="btn btn-primary w-50">Pay Now</button>
       <a href="cart.php" class="btn btn-outline-secondary w-50">← Back to Cart</a>
    </div>
  </form>
  <?php } else { ?>
    <div class="alert alert-success text-center">
       Payment Successful! Thank you for your purchase.
    </div>
    <div class="text-center mt-4">
      <a href="index.php" class="btn btn-outline-dark">Return to Home</a>
      <a href="receipt.php" class="btn btn-outline-dark">View Receipt</a>
    </div>
  <?php } ?>
</div>

<script>
const paymentType = document.getElementById('payment-type');
if (paymentType) {
  paymentType.addEventListener('change', function () {
    ['card','online','ewallet','cash'].forEach(sec => 
      document.getElementById(sec+'-section').classList.add('d-none')
    );
    if (this.value) {
      document.getElementById(this.value+'-section').classList.remove('d-none');
    }
  });
}
</script>
</body>
</html>
