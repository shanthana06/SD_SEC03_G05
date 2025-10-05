<?php
// orderstatus.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';
include 'navbar.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo '<div class="text-center mt-5">⚠ Please log in as a customer to view order status.</div>';
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders with status
$sql = "SELECT id, created_at, order_type, status 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Status | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    body {
      background: url('images/arjunabackground.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .order-bar {
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 8px;
      padding: 12px 20px;
      margin: 10px auto;
      max-width: 850px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

<div class="mt-5">
  <h2 class="text-center text-white mb-4">Order Status</h2>

  <?php if ($orders->num_rows === 0): ?>
      <p class="text-center text-white">No active or past orders found.</p>
  <?php else: ?>
      <?php while ($order = $orders->fetch_assoc()): ?>
          <div class="order-bar">
            <div>
              <strong>Order #<?= $order['id'] ?></strong> 
              <br><?= date("M j, Y h:i A", strtotime($order['created_at'])) ?>
              <br>Type: <?= htmlspecialchars($order['order_type']) ?>
            </div>
            <div>
              <span class="badge bg-<?php 
                  if ($order['status'] === 'Pending') echo 'warning';
                  elseif ($order['status'] === 'Preparing') echo 'info';
                  elseif ($order['status'] === 'Ready') echo 'primary';
                  elseif ($order['status'] === 'Completed') echo 'success';
                  else echo 'secondary';
              ?>">
                <?= htmlspecialchars($order['status']) ?>
              </span>
            </div>
          </div>
      <?php endwhile; ?>
  <?php endif; ?>
</div>

</body>
</html>
