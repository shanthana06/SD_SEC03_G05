<?php
session_start();
include 'db.php'; // your DB connection

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = mysqli_real_escape_string($conn, $_POST['order_type']);
    $name = mysqli_real_escape_string($conn, $_POST['order_name']);
    $note = mysqli_real_escape_string($conn, $_POST['order_details']);

    $sql = "INSERT INTO orders (order_type, customer_name, order_note) 
            VALUES ('$type', '$name', '$note')";

    if (mysqli_query($conn, $sql)) {
        $message = "✅ Your order has been placed!";
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Place Order | Arjuna n Co-ffee</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
      padding: 0;
      background: url('images/coffee1.jpg') no-repeat center center/cover;
    }
    .form-container {
      background-color: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }
    .btn-transparent {
      background: transparent !important;
      border: 2px solid #333;
      color: #333;
      transition: 0.3s;
    }
    .btn-transparent:hover {
      background: #333;
      color: #fff;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="login-bg-blur"></div>

<section class="py-5">
  <div class="container">
    <h2 class="section-title text-center mb-4">Place Your Order</h2>

    <div class="form-container">
      <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Order Type</label>
          <select class="form-select" name="order_type">
            <option>Dine-In</option>
            <option>Pickup</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Table Number / Pickup Name</label>
          <input type="text" class="form-control" name="order_name" placeholder="Enter info" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Order Details</label>
          <textarea class="form-control" name="order_details" rows="4" placeholder="Optional note..."></textarea>
        </div>

       <a href="payment.php" class="btn btn-transparent">Submit Order</a>
        <a href="index.php" class="btn btn-transparent">Return to Home</a>
      </form>
    </div>
  </div>
</section>
</body>
</html>
