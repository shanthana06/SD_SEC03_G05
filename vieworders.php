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
  <title>View Orders | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- Elegant Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  <style>
    /* Modern aesthetic styling */
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      font-family: 'Cormorant Garamond', serif;
      font-weight: 400;
      min-height: 100vh;
    }

    .glass-container {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 40px;
      margin-top: 80px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    h2 { 
      text-align: center; 
      color: #2c3e50; 
      margin-bottom: 35px; 
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      font-size: 2.5rem;
      background: linear-gradient(135deg, #8B5E3C 0%, #D2B48C 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Modern table styling */
    .table-modern {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      border: none;
    }

    .table-modern thead {
      background: linear-gradient(135deg, #8B5E3C 0%, #A67B5B 100%);
    }

    .table-modern thead th {
      border: none;
      padding: 20px 15px;
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      color: white;
      font-size: 1.1rem;
      text-align: center;
      letter-spacing: 0.5px;
    }

    .table-modern tbody tr {
      transition: all 0.3s ease;
      border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .table-modern tbody tr:hover {
      background: rgba(210, 180, 140, 0.1);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .table-modern tbody td {
      padding: 18px 15px;
      border: none;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.15rem;
      text-align: center;
      vertical-align: middle;
      color: #2c3e50;
    }

    /* Modern status badges */
    .status-badge {
      font-size: 0.85rem;
      padding: 8px 16px;
      border-radius: 20px;
      font-family: 'Lora', serif;
      font-weight: 500;
      letter-spacing: 0.5px;
      border: 2px solid transparent;
    }

    .bg-warning { background: linear-gradient(135deg, #FFD166 0%, #FFB347 100%) !important; color: #8B5E3C !important; }
    .bg-info { background: linear-gradient(135deg, #6BC5D2 0%, #4A9FA5 100%) !important; color: white !important; }
    .bg-primary { background: linear-gradient(135deg, #8B5E3C 0%, #A67B5B 100%) !important; color: white !important; }
    .bg-success { background: linear-gradient(135deg, #06D6A0 0%, #04A777 100%) !important; color: white !important; }

    /* Modern form controls */
    .form-select-modern {
      background: rgba(255, 255, 255, 0.9);
      border: 2px solid #e9ecef;
      border-radius: 12px;
      padding: 10px 15px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .form-select-modern:focus {
      border-color: #8B5E3C;
      box-shadow: 0 0 0 3px rgba(139, 94, 60, 0.1);
      background: white;
    }

    /* Modern button */
    .btn-modern {
      background: linear-gradient(135deg, #8B5E3C 0%, #A67B5B 100%);
      border: none;
      border-radius: 12px;
      padding: 10px 20px;
      color: white;
      font-family: 'Cormorant Garamond', serif;
      font-weight: 500;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(139, 94, 60, 0.3);
    }

    .btn-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(139, 94, 60, 0.4);
      background: linear-gradient(135deg, #A67B5B 0%, #8B5E3C 100%);
      color: white;
    }

    /* Action form styling */
    .action-form {
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: center;
    }

    /* Empty state styling */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #6c757d;
    }

    .empty-state i {
      font-size: 4rem;
      color: #dee2e6;
      margin-bottom: 20px;
    }

    /* Responsive design */
    @media (max-width: 768px) {
      .glass-container {
        margin: 20px;
        padding: 25px;
      }
      
      .table-modern thead {
        display: none;
      }
      
      .table-modern tbody tr {
        display: block;
        margin-bottom: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      }
      
      .table-modern tbody td {
        display: block;
        text-align: right;
        padding: 12px 15px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
      }
      
      .table-modern tbody td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        font-family: 'Playfair Display', serif;
        color: #8B5E3C;
      }
      
      .action-form {
        flex-direction: column;
        gap: 10px;
      }
    }

    /* Header badge */
    .header-badge {
      background: linear-gradient(135deg, #8B5E3C 0%, #D2B48C 100%);
      color: white;
      padding: 8px 20px;
      border-radius: 25px;
      font-size: 0.9rem;
      margin-left: 15px;
      vertical-align: middle;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="glass-container">
    <h2>Order Management <span class="header-badge">Live Updates</span></h2>

    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-modern">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Type</th>
            <th>Note</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td data-label="Order ID">
                <strong>#<?= $row['id'] ?></strong>
              </td>
              <td data-label="Customer"><?= htmlspecialchars($row['customer_name']) ?></td>
              <td data-label="Type">
                <span class="badge bg-light text-dark"><?= htmlspecialchars($row['order_type']) ?></span>
              </td>
              <td data-label="Note">
                <?= $row['order_note'] ? htmlspecialchars($row['order_note']) : '<span class="text-muted">No note</span>' ?>
              </td>
              <td data-label="Status">
                <span class="badge status-badge bg-<?php 
                    if ($row['status'] === 'Pending') echo 'warning';
                    elseif ($row['status'] === 'Preparing') echo 'info';
                    elseif ($row['status'] === 'Ready') echo 'primary';
                    elseif ($row['status'] === 'Completed') echo 'success';
                    else echo 'secondary';
                ?>">
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
              <td data-label="Created">
                <small class="text-muted"><?= date("M j, Y h:i A", strtotime($row['created_at'])) ?></small>
              </td>
              <td data-label="Actions">
                <form method="POST" action="vieworders.php" class="action-form">
                  <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                  <select name="status" class="form-select form-select-modern" required>
                    <option value="Pending" <?= $row['status']=='Pending'?'selected':'' ?>> Pending</option>
                    <option value="Preparing" <?= $row['status']=='Preparing'?'selected':'' ?>> Preparing</option>
                    <option value="Ready" <?= $row['status']=='Ready'?'selected':'' ?>> Ready</option>
                    <option value="Completed" <?= $row['status']=='Completed'?'selected':'' ?>> Completed</option>
                  </select>
                  <button type="submit" name="update_status" class="btn btn-modern">
                    <i class="bi bi-arrow-clockwise me-1"></i>Update
                  </button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h4>No Orders Yet</h4>
        <p class="text-muted">When orders come in, they'll appear here.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Add some interactive animations
document.addEventListener('DOMContentLoaded', function() {
  const rows = document.querySelectorAll('.table-modern tbody tr');
  rows.forEach((row, index) => {
    row.style.animationDelay = `${index * 0.1}s`;
    row.classList.add('fade-in');
  });
});
</script>

<style>
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
</body>
</html>