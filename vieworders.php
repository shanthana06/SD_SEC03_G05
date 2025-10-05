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
  <style>
    body {
      background: url('images/arjunabackground.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .container {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 10px;
      padding: 30px;
      margin-top: 60px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    h2 { text-align: center; color: #333; margin-bottom: 25px; }
    .status-badge { font-size: 0.9rem; padding: 5px 10px; }
  </style>
</head>
<body>
<div class="container">
  <h2> View All Orders</h2>

  <?php if ($result->num_rows > 0): ?>
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark text-center">
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Note</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr class="text-center">
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['order_type']) ?></td>
            <td><?= htmlspecialchars($row['order_note']) ?></td>
            <td>
              <span class="badge bg-<?php 
                  if ($row['status'] === 'Pending') echo 'warning';
                  elseif ($row['status'] === 'Preparing') echo 'info';
                  elseif ($row['status'] === 'Ready') echo 'primary';
                  elseif ($row['status'] === 'Completed') echo 'success';
                  else echo 'secondary';
              ?> status-badge">
                <?= htmlspecialchars($row['status']) ?>
              </span>
            </td>
            <td><?= date("M j, Y h:i A", strtotime($row['created_at'])) ?></td>
            <td>
              <form method="POST" action="vieworders.php" class="d-flex justify-content-center align-items-center gap-2">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <select name="status" class="form-select form-select-sm" required>
                  <option value="Pending" <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
                  <option value="Preparing" <?= $row['status']=='Preparing'?'selected':'' ?>>Preparing</option>
                  <option value="Ready" <?= $row['status']=='Ready'?'selected':'' ?>>Ready</option>
                  <option value="Completed" <?= $row['status']=='Completed'?'selected':'' ?>>Completed</option>
                </select>
                <button type="submit" name="update_status" class="btn btn-sm btn-outline-success">Update</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p class="text-center text-muted">No orders found.</p>
  <?php endif; ?>
</div>
</body>
</html>
