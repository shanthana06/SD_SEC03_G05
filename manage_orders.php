<?php
// manage_orders.php (for staff)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';
include 'navbar.php';

// ✅ Only staff/admin can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['staff','admin'])) {
    echo '<div class="text-center mt-5">⚠ Access denied. Staff only.</div>';
    exit;
}

// ✅ Handle update form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
}

// ✅ Fetch all orders
$sql = "SELECT o.id, o.created_at, o.order_type, o.status, u.name 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Orders | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('images/arjunabackground.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .order-bar {
      background: rgba(255,255,255,0.95);
      border-radius: 8px;
      padding: 15px 20px;
      margin: 12px auto;
      max-width: 900px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    }
    select, button {
      margin-left: 5px;
    }
  </style>
</head>
<body>

<div class="mt-5">
  <h2 class="text-center text-white mb-4">Manage Orders</h2>

  <?php if ($result->num_rows === 0): ?>
      <p class="text-center text-white">No orders available.</p>
  <?php else: ?>
      <?php while ($row = $result->fetch_assoc()): ?>
          <div class="order-bar">
            <div>
              <strong>Order #<?= $row['id'] ?></strong><br>
              Customer: <?= htmlspecialchars($row['name']) ?><br>
              <?= date("M j, Y h:i A", strtotime($row['created_at'])) ?><br>
              Type: <?= htmlspecialchars($row['order_type']) ?>
            </div>
            <div>
              <form method="POST" style="display:flex;align-items:center;">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <select name="status" class="form-select form-select-sm">
                  <option value="Pending" <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
                  <option value="Preparing" <?= $row['status']=='Preparing'?'selected':'' ?>>Preparing</option>
                  <option value="Ready" <?= $row['status']=='Ready'?'selected':'' ?>>Ready</option>
                  <option value="Completed" <?= $row['status']=='Completed'?'selected':'' ?>>Completed</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Update</button>
              </form>
            </div>
          </div>
      <?php endwhile; ?>
  <?php endif; ?>
</div>

</body>
</html>
