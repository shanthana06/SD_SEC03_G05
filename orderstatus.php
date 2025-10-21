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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Status | Arjuna n Co-ffee</title>
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
    
    .order-status {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-family: 'Lora', serif;
    }
    
    .status-pending {
      background: #fff3cd;
      color: #856404;
    }
    
    .status-preparing {
      background: #cce7ff;
      color: #004085;
    }
    
    .status-ready {
      background: #d1ecf1;
      color: #0c5460;
    }
    
    .status-completed {
      background: #d4edda;
      color: #155724;
    }
    
    .status-other {
      background: #e2e3e5;
      color: #383d41;
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
    
    .status-timeline {
      display: flex;
      justify-content: space-between;
      margin-top: 1.5rem;
      position: relative;
    }
    
    .status-timeline::before {
      content: '';
      position: absolute;
      top: 15px;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--border-color);
      z-index: 1;
    }
    
    .timeline-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      z-index: 2;
      flex: 1;
    }
    
    .timeline-icon {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: white;
      border: 2px solid var(--border-color);
      margin-bottom: 0.5rem;
      color: var(--text-light);
    }
    
    .timeline-label {
      font-family: 'Cormorant Garamond', serif;
      font-size: 0.9rem;
      color: var(--text-light);
      text-align: center;
      font-weight: 500;
    }
    
    .active-step .timeline-icon {
      background: var(--primary-color);
      border-color: var(--primary-color);
      color: white;
    }
    
    .active-step .timeline-label {
      color: var(--primary-color);
      font-weight: 600;
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
      
      .order-status {
        align-self: flex-start;
      }
      
      .status-timeline {
        display: none;
      }
    }
  </style>
</head>
<body>

<div class="page-container">
  <div class="page-header">
    <h1 class="page-title">Order Status</h1>
    <p class="page-subtitle">Track the progress of your coffee orders in real-time</p>
  </div>

  <div class="orders-container">
    <?php if ($orders->num_rows === 0): ?>
      <div class="empty-state">
        <i class="fas fa-shopping-bag"></i>
        <h3>No orders yet</h3>
        <p>You haven't placed any orders. Start your coffee journey today!</p>
      </div>
    <?php else: ?>
      <?php while ($order = $orders->fetch_assoc()): ?>
        <div class="order-card">
          <div class="order-header">
            <div>
              <div class="order-id">Order #<?= $order['id'] ?></div>
              <div class="order-date"><?= date("F j, Y \a\\t g:i A", strtotime($order['created_at'])) ?></div>
              <div class="order-type mt-2"><?= htmlspecialchars($order['order_type']) ?></div>
            </div>
            <div class="order-status">
              <span class="status-badge 
                <?php 
                  if ($order['status'] === 'Pending') echo 'status-pending';
                  elseif ($order['status'] === 'Preparing') echo 'status-preparing';
                  elseif ($order['status'] === 'Ready') echo 'status-ready';
                  elseif ($order['status'] === 'Completed') echo 'status-completed';
                  else echo 'status-other';
                ?>">
                <i class="fas 
                  <?php 
                    if ($order['status'] === 'Pending') echo 'fa-clock';
                    elseif ($order['status'] === 'Preparing') echo 'fa-blender';
                    elseif ($order['status'] === 'Ready') echo 'fa-check';
                    elseif ($order['status'] === 'Completed') echo 'fa-box-check';
                    else echo 'fa-question';
                  ?>"></i>
                <?= htmlspecialchars($order['status']) ?>
              </span>
            </div>
          </div>
          
          <div class="status-timeline">
            <div class="timeline-step <?= in_array($order['status'], ['Pending', 'Preparing', 'Ready', 'Completed']) ? 'active-step' : '' ?>">
              <div class="timeline-icon">
                <i class="fas fa-receipt"></i>
              </div>
              <div class="timeline-label">Order Placed</div>
            </div>
            <div class="timeline-step <?= in_array($order['status'], ['Preparing', 'Ready', 'Completed']) ? 'active-step' : '' ?>">
              <div class="timeline-icon">
                <i class="fas fa-coffee"></i>
              </div>
              <div class="timeline-label">Preparing</div>
            </div>
            <div class="timeline-step <?= in_array($order['status'], ['Ready', 'Completed']) ? 'active-step' : '' ?>">
              <div class="timeline-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="timeline-label">Ready</div>
            </div>
            <div class="timeline-step <?= $order['status'] === 'Completed' ? 'active-step' : '' ?>">
              <div class="timeline-icon">
                <i class="fas fa-box-check"></i>
              </div>
              <div class="timeline-label">Completed</div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
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