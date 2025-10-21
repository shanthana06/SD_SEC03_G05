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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Elegant Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2c3e50;
      --accent-color: #e74c3c;
      --light-bg: #f8f9fa;
      --text-dark: #2c3e50;
      --text-light: #7f8c8d;
      --border-color: #e1e8ed;
    }
    
    body {
      background-color: var(--light-bg);
      font-family: 'Lora', serif;
      color: var(--text-dark);
      line-height: 1.6;
    }
    
    .page-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem 1.5rem;
    }
    
    .page-header {
      text-align: center;
      margin-bottom: 3rem;
      position: relative;
    }
    
    .page-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.8rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 0.5rem;
      letter-spacing: -0.5px;
    }
    
    .page-subtitle {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.3rem;
      color: var(--text-light);
      max-width: 600px;
      margin: 0 auto;
      font-weight: 400;
    }
    
    .user-welcome {
      text-align: center;
      margin-bottom: 2rem;
      padding: 1.5rem;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .user-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      font-size: 2rem;
      font-weight: 600;
      font-family: 'Playfair Display', serif;
    }
    
    .user-name {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 0.25rem;
    }
    
    .user-email {
      color: var(--text-light);
      font-size: 1rem;
      font-family: 'Cormorant Garamond', serif;
    }
    
    .orders-container {
      max-width: 900px;
      margin: 0 auto;
    }
    
    .order-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--border-color);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .order-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }
    
    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--border-color);
    }
    
    .order-id {
      font-family: 'Playfair Display', serif;
      font-size: 1.4rem;
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 0.25rem;
    }
    
    .order-date {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
      color: var(--text-light);
      font-weight: 500;
    }
    
    .order-type {
      display: inline-block;
      background: #f1f8ff;
      color: #0366d6;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 500;
      font-family: 'Lora', serif;
    }
    
    .order-note {
      background: #f8f9fa;
      padding: 0.75rem;
      border-radius: 8px;
      margin-top: 1rem;
      font-size: 0.95rem;
      color: var(--text-light);
      border-left: 3px solid var(--border-color);
      font-family: 'Lora', serif;
    }
    
    .order-note i {
      margin-right: 0.5rem;
      color: var(--accent-color);
    }
    
    .order-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
    }
    
    .btn-receipt {
      background: var(--primary-color);
      color: white;
      border: none;
      padding: 0.5rem 1.25rem;
      border-radius: 6px;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s ease;
      text-decoration: none;
      font-family: 'Lora', serif;
    }
    
    .btn-receipt:hover {
      background: #1a2530;
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: var(--text-light);
      font-family: 'Cormorant Garamond', serif;
    }
    
    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #bdc3c7;
    }
    
    .empty-state h3 {
      font-family: 'Playfair Display', serif;
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: var(--text-dark);
    }
    
    .empty-state p {
      margin-bottom: 1.5rem;
    }
    
    .btn-primary {
      background: var(--primary-color);
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 6px;
      font-weight: 500;
      font-family: 'Lora', serif;
    }
    
    .btn-primary:hover {
      background: #1a2530;
    }
    
    .stats-container {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-bottom: 2rem;
    }
    
    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      flex: 1;
      max-width: 200px;
    }
    
    .stat-value {
      font-family: 'Playfair Display', serif;
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 0.25rem;
    }
    
    .stat-label {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      color: var(--text-light);
      font-weight: 500;
    }
    
    @media (max-width: 768px) {
      .page-title {
        font-size: 2.2rem;
      }
      
      .page-subtitle {
        font-size: 1.1rem;
      }
      
      .order-header {
        flex-direction: column;
        gap: 1rem;
      }
      
      .order-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
      }
      
      .stats-container {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
      }
      
      .stat-card {
        max-width: 100%;
        width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="page-container">
  <div class="page-header">
    <h1 class="page-title">Order History</h1>
    <p class="page-subtitle">Review your past coffee orders and receipts</p>
  </div>

  <div class="user-welcome">
    <div class="user-avatar">
      <?php 
        // Get first letter of name for avatar
        echo strtoupper(substr($user['name'], 0, 1)); 
      ?>
    </div>
    <div class="user-name"><?php echo htmlspecialchars($user['name']); ?></div>
    <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
  </div>

  <?php if ($orders->num_rows > 0): ?>
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-value"><?php echo $orders->num_rows; ?></div>
        <div class="stat-label">Total Orders</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">
          <?php 
            // Calculate days since first order
            $firstOrderDate = null;
            $orders_data = $orders->fetch_all(MYSQLI_ASSOC);
            if (!empty($orders_data)) {
              $firstOrderDate = strtotime($orders_data[count($orders_data)-1]['created_at']);
              $days = floor((time() - $firstOrderDate) / (60 * 60 * 24));
              echo $days > 0 ? $days : '1';
            } else {
              echo '0';
            }
          ?>
        </div>
        <div class="stat-label">Days as Customer</div>
      </div>
    </div>

    <div class="orders-container">
      <?php foreach ($orders_data as $order): ?>
        <div class="order-card">
          <div class="order-header">
            <div>
              <div class="order-id">Order #<?php echo $order['id']; ?></div>
              <div class="order-date"><?php echo date("F j, Y \a\\t g:i A", strtotime($order['created_at'])); ?></div>
              <div class="order-type"><?php echo htmlspecialchars($order['order_type']); ?></div>
            </div>
          </div>
          
          <?php if (!empty($order['order_note'])): ?>
            <div class="order-note">
              <i class="fas fa-sticky-note"></i>
              <strong>Note:</strong> <?php echo htmlspecialchars($order['order_note']); ?>
            </div>
          <?php endif; ?>
          
          <div class="order-footer">
            <div>
              <a href="receipt.php?order_id=<?php echo $order['id']; ?>" class="btn-receipt">
                <i class="fas fa-receipt"></i> View Receipt
              </a>
            </div>
            <div class="text-muted" style="font-family: 'Cormorant Garamond', serif;">
              <i class="fas fa-clock"></i> <?php echo date("M j, Y", strtotime($order['created_at'])); ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-shopping-bag"></i>
      <h3>No orders yet</h3>
      <p>You haven't placed any orders yet. Start your coffee journey today!</p>
      <a href="menu.php" class="btn btn-primary">Browse Menu</a>
    </div>
  <?php endif; ?>
</div>

<script>
  // Simple animation for order cards on page load
  document.addEventListener('DOMContentLoaded', function() {
    const orderCards = document.querySelectorAll('.order-card');
    orderCards.forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, index * 150);
    });
  });
</script>

</body>
</html>