<?php
// vieworders.php
session_start();
include 'db.php';
include 'navbar.php';

// Check login and role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    echo '<div class="text-center mt-5">⚠ Access denied. Staff/Admin only.</div>';
    exit;
}

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('✅ Order #$order_id status updated to $new_status'); window.location.href='vieworders.php';</script>";
    exit;
}

// Fetch all orders
$sql = "SELECT o.id, o.order_type, o.order_note, o.status, o.created_at, u.name AS customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Management | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- Elegant Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #1a1a1a;
      --secondary-color: #4a4a4a;
      --accent-color: #d4af37;
      --light-bg: #f9f9f9;
      --border-color: #e8e8e8;
    }
    
    body {
      background: var(--light-bg);
      font-family: 'Cormorant Garamond', serif;
      color: var(--primary-color);
      line-height: 1.6;
      min-height: 100vh;
    }
    
    .page-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .page-header {
      text-align: center;
      margin-bottom: 50px;
      padding-bottom: 20px;
    }
    
    .page-title {
      font-family: 'Playfair Display', serif;
      font-weight: 500;
      font-size: 3.2rem;
      letter-spacing: -0.5px;
      margin-bottom: 15px;
      color: #2c2c2c;
    }
    
    .page-subtitle {
      color: var(--secondary-color);
      font-weight: 300;
      font-size: 1.3rem;
      max-width: 600px;
      margin: 0 auto;
      font-family: 'Lora', serif;
    }
    
    .orders-container {
      background-color: #fff;
      border-radius: 8px;
      padding: 40px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.05);
      border: 1px solid var(--border-color);
      margin-bottom: 40px;
    }
    
    .section-title {
      font-family: 'Playfair Display', serif;
      font-weight: 500;
      font-size: 2rem;
      margin-bottom: 25px;
      color: #2c2c2c;
      border-bottom: 1px solid var(--border-color);
      padding-bottom: 10px;
    }
    
    .table th {
      font-weight: 600;
      color: var(--secondary-color);
      border-bottom: 1px solid var(--border-color);
      padding: 15px 10px;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-family: 'Lora', serif;
    }
    
    .table td {
      padding: 20px 10px;
      vertical-align: middle;
      border-bottom: 1px solid #f5f5f5;
      font-size: 1.1rem;
    }
    
    .btn-custom {
      padding: 8px 16px;
      border-radius: 4px;
      font-weight: 500;
      font-size: 0.9rem;
      transition: all 0.2s ease;
      border: none;
      text-decoration: none;
      display: inline-block;
      font-family: 'Lora', serif;
    }
    
    .btn-save {
      background-color: var(--primary-color);
      color: #fff;
    }
    
    .btn-save:hover {
      background-color: #333;
    }
    
    .btn-secondary-custom {
      background-color: transparent;
      color: var(--primary-color);
      border: 1px solid var(--border-color);
      padding: 10px 20px;
      font-family: 'Lora', serif;
    }
    
    .btn-secondary-custom:hover {
      background-color: #f5f5f5;
    }
    
    .action-buttons {
      display: flex;
      gap: 8px;
      justify-content: center;
    }
    
    .form-control {
      border-radius: 4px;
      border: 1px solid var(--border-color);
      padding: 8px 12px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
    }
    
    .form-control:focus {
      box-shadow: 0 0 0 2px rgba(26, 26, 26, 0.1);
      border-color: var(--primary-color);
    }
    
    .form-label {
      font-family: 'Lora', serif;
      font-weight: 500;
      margin-bottom: 8px;
    }
    
    .table-responsive {
      border-radius: 8px;
      overflow: hidden;
    }
    
    .table {
      margin-bottom: 0;
    }
    
    .table-hover tbody tr:hover {
      background-color: rgba(0,0,0,0.02);
    }
    
    .action-header {
      text-align: center;
    }
    
    .orders-count {
      font-family: 'Lora', serif;
      color: var(--secondary-color);
      margin-bottom: 20px;
      font-size: 1.1rem;
    }
    
    .status-badge {
      padding: 8px 16px;
      border-radius: 20px;
      font-family: 'Lora', serif;
      font-weight: 500;
      font-size: 0.85rem;
      border: 2px solid transparent;
    }
    
    .status-pending {
      background-color: #fff3cd;
      color: #856404;
      border-color: #ffeaa7;
    }
    
    .status-preparing {
      background-color: #d1ecf1;
      color: #0c5460;
      border-color: #b8e2e8;
    }
    
    .status-ready {
      background-color: #d4edda;
      color: #155724;
      border-color: #c3e6cb;
    }
    
    .status-completed {
      background-color: #e8f5e8;
      color: #1e7e34;
      border-color: #d4edda;
    }
    
    .order-type-badge {
      background-color: #f8f9fa;
      color: var(--secondary-color);
      border: 1px solid var(--border-color);
      padding: 6px 12px;
      border-radius: 15px;
      font-size: 0.8rem;
      font-family: 'Lora', serif;
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--secondary-color);
    }
    
    .empty-state-icon {
      font-size: 3rem;
      margin-bottom: 20px;
      opacity: 0.5;
    }
    
    .empty-state-text {
      font-size: 1.2rem;
      font-family: 'Lora', serif;
    }
    
    .action-form {
      display: flex;
      gap: 10px;
      align-items: center;
      justify-content: center;
    }
    
    .form-select {
      border-radius: 4px;
      border: 1px solid var(--border-color);
      padding: 8px 12px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
    }
    
    .form-select:focus {
      box-shadow: 0 0 0 2px rgba(26, 26, 26, 0.1);
      border-color: var(--primary-color);
    }
    
    .note-cell {
      max-width: 200px;
      word-wrap: break-word;
    }
    
    .date-cell {
      white-space: nowrap;
    }
    
    @media (max-width: 768px) {
      .page-container {
        padding: 20px 15px;
      }
      
      .orders-container {
        padding: 25px 20px;
      }
      
      .page-title {
        font-size: 2.5rem;
      }
      
      .action-form {
        flex-direction: column;
        gap: 8px;
      }
      
      .table-responsive {
        font-size: 0.9rem;
      }
      
      .note-cell {
        max-width: 150px;
      }
    }
    
    /* Animation for new rows */
    .fade-in {
      animation: fadeInUp 0.6s ease forwards;
      opacity: 0;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-container">
 

  <div class="orders-container">
    <h3 class="section-title">Current Orders</h3>
    
    <?php
    $orders_count = $result->num_rows;
    $result->data_seek(0); // Reset result pointer
    ?>
    <div class="orders-count">
      Total Orders: <?= $orders_count ?>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Type</th>
            <th>Note</th>
            <th>Status</th>
            <th>Created At</th>
            <th class="action-header">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($orders_count > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="fade-in">
                <td class="text-center">
                  <strong>#<?= $row['id'] ?></strong>
                </td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td>
                  <span class="order-type-badge"><?= htmlspecialchars($row['order_type']) ?></span>
                </td>
                <td class="note-cell">
                  <?= $row['order_note'] ? htmlspecialchars($row['order_note']) : '<span class="text-muted">No note</span>' ?>
                </td>
                <td>
                  <span class="status-badge status-<?= strtolower($row['status']) ?>">
                    <i class="bi bi-<?php 
                      if ($row['status'] === 'Pending') echo 'clock';
                      elseif ($row['status'] === 'Preparing') echo 'cup-straw';
                      elseif ($row['status'] === 'Ready') echo 'check-circle';
                      elseif ($row['status'] === 'Completed') echo 'star';
                      else echo 'question';
                    ?> me-1"></i>
                    <?= htmlspecialchars($row['status']) ?>
                  </span>
                </td>
                <td class="date-cell">
                  <?= date("M j, Y g:i A", strtotime($row['created_at'])) ?>
                </td>
                <td>
                  <form method="POST" action="vieworders.php" class="action-form">
                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                    <select name="status" class="form-select" required>
                      <option value="Pending" <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
                      <option value="Preparing" <?= $row['status']=='Preparing'?'selected':'' ?>>Preparing</option>
                      <option value="Ready" <?= $row['status']=='Ready'?'selected':'' ?>>Ready</option>
                      <option value="Completed" <?= $row['status']=='Completed'?'selected':'' ?>>Completed</option>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-save btn-custom">
                      <i class="bi bi-arrow-clockwise me-1"></i>Update
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="empty-state">
                <div class="empty-state-icon">📦</div>
                <div class="empty-state-text">No orders yet</div>
                <p class="text-muted mt-2">Customer orders will appear here once they are placed.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <a href="staff_dashboard.php" class="btn btn-secondary-custom">← Back to Dashboard</a>
    </div>
  </div>
</div>

<script>
// Add animation delays for rows
document.addEventListener('DOMContentLoaded', function() {
  const rows = document.querySelectorAll('.table tbody tr');
  rows.forEach((row, index) => {
    row.style.animationDelay = `${index * 0.1}s`;
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>