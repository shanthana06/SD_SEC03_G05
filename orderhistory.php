<?php
// order_history.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';
include 'navbar.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo '<div class="text-center mt-5">⚠ Please log in as a customer to view order history.</div>';
    exit;
}

$user_id = $_SESSION['user_id'];

// Get customer info
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Fetch all orders
$sql = "SELECT id, created_at, order_type, order_note 
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
  <title>Order History | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('images/arjunabackground.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
    }
    .order-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 18px;
      margin: 10px 0;
      background: rgba(0,0,0,0.65);
      border-radius: 8px;
      transition: 0.3s;
    }
    .order-bar:hover {
      background: rgba(0,0,0,0.8);
    }
    .order-info {
      font-size: 15px;
    }
    .btn-receipt {
      font-size: 13px;
      padding: 5px 10px;
    }
  </style>
</head>
<body>

<div class="container-fluid mt-4">
  <h2 class="text-center mb-4"> Welcome, <?php echo htmlspecialchars($user['name']); ?> – Your Order History</h2>

  <?php if ($orders->num_rows > 0): ?>
    <?php while ($order = $orders->fetch_assoc()): ?>
      <div class="order-bar">
        <div class="order-info">
          <strong>#<?php echo $order['id']; ?></strong> |
          <?php echo htmlspecialchars($order['order_type']); ?> |
          <?php echo date("d M Y, h:i A", strtotime($order['created_at'])); ?>
          <?php if (!empty($order['order_note'])): ?>
            <br><em>Note: <?php echo htmlspecialchars($order['order_note']); ?></em>
          <?php endif; ?>
        </div>
        <div>
          <a href="receipt.php?order_id=<?php echo $order['id']; ?>" class="btn btn-sm btn-light btn-receipt">View Receipt</a>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-center mt-4">You don’t have any orders yet.</p>
  <?php endif; ?>
</div>

</body>
</html>
