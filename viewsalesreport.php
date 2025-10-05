<?php
session_start();
include 'db.php';

// Check role (only staff/admin can access)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    echo '<div class="text-center mt-5">⚠ Access Denied. Staff or Admin only.</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Report | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4 text-center">☕ Sales Report</h2>

  <?php
  // --- Total Sales and Orders ---
  $summaryQuery = "SELECT 
                      COUNT(*) AS total_orders,
                      SUM(total_amount) AS total_sales
                   FROM orders
                   WHERE status = 'Completed'";
  $summaryResult = $conn->query($summaryQuery);
  $summary = $summaryResult->fetch_assoc();

  // --- Sales by Date ---
  $salesByDateQuery = "SELECT 
                          DATE(created_at) AS order_date,
                          COUNT(*) AS total_orders,
                          SUM(total_amount) AS total_sales
                       FROM orders
                       WHERE status = 'Completed'
                       GROUP BY DATE(created_at)
                       ORDER BY order_date DESC";
  $salesByDate = $conn->query($salesByDateQuery);
  ?>

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h5>Total Orders</h5>
          <h3 class="text-primary"><?= $summary['total_orders'] ?? 0 ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h5>Total Sales</h5>
          <h3 class="text-success">RM <?= number_format($summary['total_sales'] ?? 0, 2) ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
      <h5 class="mb-0">Sales by Date</h5>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Date</th>
            <th>Total Orders</th>
            <th>Total Sales (RM)</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($salesByDate->num_rows > 0) {
              while ($row = $salesByDate->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['order_date']}</td>
                          <td>{$row['total_orders']}</td>
                          <td>RM " . number_format($row['total_sales'], 2) . "</td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='3' class='text-center'>No completed sales found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
  </div>

</div>
</body>
</html>
