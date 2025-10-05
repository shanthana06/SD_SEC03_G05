<?php
// dashboard.php
session_start();
include 'db.php';
include 'navbar.php';

// --- Access control ---
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    echo '<div class="text-center mt-5">⚠ Access denied. Staff/Admin only.</div>';
    exit;
}

// --- Initialize variables ---
$totalSales = 0;
$totalOrders = 0;
$pendingOrders = 0;
$completedOrders = 0;
$newCustomers = 0;

// --- Total Sales & Total Completed Orders ---
$sql = "SELECT 
            SUM(total_amount) AS totalSales, 
            COUNT(*) AS totalOrders 
        FROM orders 
        WHERE status = 'Completed'";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $totalSales = $row['totalSales'] ?? 0;
    $totalOrders = $row['totalOrders'] ?? 0;
}

// --- Pending & Completed Order Counts ---
$sql = "SELECT 
            SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) AS completed
        FROM orders";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $pendingOrders = $row['pending'] ?? 0;
    $completedOrders = $row['completed'] ?? 0;
}

// --- Daily Sales for Chart ---
$sql = "SELECT 
            DATE(created_at) AS order_date, 
            SUM(total_amount) AS total_sales
        FROM orders
        WHERE status = 'Completed'
        GROUP BY DATE(created_at)
        ORDER BY order_date ASC";
$resultSales = $conn->query($sql);

$days = [];
$dailySales = [];
if ($resultSales && $resultSales->num_rows > 0) {
    while ($row = $resultSales->fetch_assoc()) {
        $days[] = $row['order_date'];
        $dailySales[] = (float)$row['total_sales'];
    }
}

// --- New Customers (today only) ---
$sql = "SELECT COUNT(*) AS newCustomers 
        FROM users 
        WHERE DATE(created_at) = CURDATE()";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $newCustomers = $row['newCustomers'] ?? 0;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales Report | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      background-color: #f8f5f2;
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

    .cart-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px) brightness(0.85);
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
    }

    .dashboard-container {
      background-color: rgba(255,255,255,0.95);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .card {
      border-radius: 14px;
      background-color: #FCFAF7;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: -260px;
      width: 240px;
      height: 100%;
      background-color: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 15px;
      box-shadow: 2px 0 15px rgba(0,0,0,0.1);
      border-right: 1px solid rgba(200,180,160,0.3);
      z-index: 100;
      transition: all 0.3s ease;
    }

    .sidebar.show { left: 0; }

    .sidebar a {
      display: block;
      padding: 12px 15px;
      border-radius: 12px;
      text-align: center;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      color: #5C4033;
      background-color: #FDFCF9;
    }

    .sidebar a:hover {
      background-color: #D2B48C;
      color: #fff;
      transform: translateY(-2px);
    }

    .toggle-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 200;
      background-color: rgba(255,255,255,0.95);
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      cursor: pointer;
      transition: all 0.3s ease;
      color: #5C4033;
      font-weight: bold;
    }

    .toggle-btn:hover {
      background-color: rgba(255,255,255,1);
      transform: scale(1.05);
    }
  </style>
</head>

<body>
  <div class="cart-bg-blur"></div>

  <!-- 🌸 Sidebar Toggle -->
  <button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

  <!-- 🌸 Sidebar -->
  <div class="sidebar" id="sidebar">
    <a href="add_menu_staff.php">Add Menu</a>
    <a href="menu_list_staff.php">Edit Menu</a>
    <a href="menu_list_staff.php">Delete Menu</a>
    <a href="customer_list.php">Edit Customer</a>
    <a href="view_feedback.php">View Feedback</a>
    <a href="vieworders.php">View Orders</a>
  </div>

  <div class="container mt-5">
    <div class="dashboard-container">
      <h2 class="text-center mb-4">Sales Report Dashboard</h2>

      <!-- Summary Cards -->
      <div class="row text-center mb-4">
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm border-0 rounded-4" style="background-color:#f5e9e0;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-1">Total Orders</h6>
                  <h3 class="fw-bold"><?= $totalOrders ?></h3>
                  <small class="text-danger">-2.33%</small>
                </div>
                <div class="rounded-circle p-3" style="background-color:#d6b8a1;">
                  <i class="bi bi-bag-fill fs-4 text-white"></i>
                </div>
              </div>
              <div class="progress mt-3" style="height:5px;">
                <div class="progress-bar" style="width:70%; background-color:#b97a56;"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="card shadow-sm border-0 rounded-4" style="background-color:#f5e9e0;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-1">New Customers</h6>
                  <h3 class="fw-bold"><?= $newCustomers ?></h3>
                  <small class="text-success">+32.4%</small>
                </div>
                <div class="rounded-circle p-3" style="background-color:#d6b8a1;">
                  <i class="bi bi-person-fill fs-4 text-white"></i>
                </div>
              </div>
              <div class="progress mt-3" style="height:5px;">
                <div class="progress-bar" style="width:80%; background-color:#b97a56;"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="card shadow-sm border-0 rounded-4" style="background-color:#f5e9e0;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-1">Total Sales</h6>
                  <h3 class="fw-bold">RM <?= number_format($totalSales, 2) ?></h3>
                  <small class="text-success">+25%</small>
                </div>
                <div class="rounded-circle p-3" style="background-color:#d6b8a1;">
                  <i class="bi bi-cart-fill fs-4 text-white"></i>
                </div>
              </div>
              <div class="progress mt-3" style="height:5px;">
                <div class="progress-bar" style="width:75%; background-color:#b97a56;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chart -->
      <div class="card p-4 shadow-sm">
        <h5 class="text-center mb-3">Daily Sales Analytics</h5>
        <canvas id="salesChart"></canvas>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('show');
    }

    const days = <?= json_encode($days) ?>;
    const sales = <?= json_encode($dailySales) ?>;

    new Chart(document.getElementById('salesChart'), {
      type: 'line',
      data: {
        labels: days,
        datasets: [{
          label: 'Daily Sales',
          data: sales,
          fill: true,
          borderColor: '#C49E6C',
          backgroundColor: 'rgba(196,158,108,0.15)',
          tension: 0.4,
          pointBackgroundColor: '#8B5E3C',
          pointBorderColor: '#fff',
          pointHoverRadius: 6,
          pointRadius: 5,
          pointHoverBackgroundColor: '#8B5E3C',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#fff',
            titleColor: '#000',
            bodyColor: '#000',
            borderColor: '#C49E6C',
            borderWidth: 1,
            padding: 10
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.05)' },
            title: { display: true, text: 'RM (Sales Amount)' }
          },
          x: {
            grid: { display: false },
            title: { display: true, text: 'Date' }
          }
        }
      }
    });
  </script>
</body>
</html>
