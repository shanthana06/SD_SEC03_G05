<?php
// staff_dashboard.php
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

// --- Total Revenue & Total Payment Count ---
$sql = "SELECT 
            SUM(amount) AS totalSales, 
            COUNT(*) AS totalOrders 
        FROM payments 
        WHERE payment_date IS NOT NULL";
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

// --- Daily Revenue for Chart ---
$sql = "SELECT 
            DATE(payment_date) AS order_date, 
            SUM(amount) AS total_sales
        FROM payments
        WHERE payment_date IS NOT NULL 
        GROUP BY DATE(payment_date)
        ORDER BY order_date DESC
        LIMIT 30";
$resultSales = $conn->query($sql);

$days = [];
$dailySales = [];
if ($resultSales && $resultSales->num_rows > 0) {
    while ($row = $resultSales->fetch_assoc()) {
        $days[] = $row['order_date'];
        $dailySales[] = (float)$row['total_sales'];
    }
    // Reverse to show oldest to newest
    $days = array_reverse($days);
    $dailySales = array_reverse($dailySales);
}

// --- New Customers (today only) ---
$sql = "SELECT COUNT(*) AS newCustomers 
        FROM users 
        WHERE DATE(created_at) = CURDATE()";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $newCustomers = $row['newCustomers'] ?? 0;
}

// --- Debug: Check if we have any payment data ---
$debugSql = "SELECT COUNT(*) as total_payments, 
                    MIN(payment_date) as first_payment,
                    MAX(payment_date) as last_payment
             FROM payments 
             WHERE payment_date IS NOT NULL";
$debugResult = $conn->query($debugSql);
$debugData = $debugResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Elegant Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  
  <style>
    /* Apply elegant fonts to entire page */
    body {
      background-color: #f8f5f2;
      font-family: 'Cormorant Garamond', serif;
      overflow-x: hidden;
      font-weight: 400;
    }

    /* Remove blur background */
    .cart-bg-blur {
      display: none;
    }

    /* Apply fonts to all elements */
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Playfair Display', serif;
      font-weight: 500;
    }

    .dashboard-container {
      background-color: rgba(255,255,255,0.95);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      font-family: 'Cormorant Garamond', serif;
    }

    .card {
      border-radius: 14px;
      background-color: #FCFAF7;
      transition: all 0.3s ease;
      font-family: 'Cormorant Garamond', serif;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: -260px;
      width: 240px;
      height: 100%;
      background-color: rgba(255,255,255,0.98);
      backdrop-filter: blur(10px);
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 15px;
      box-shadow: 2px 0 15px rgba(0,0,0,0.1);
      border-right: 1px solid rgba(200,180,160,0.3);
      z-index: 1500;
      transition: all 0.3s ease;
      font-family: 'Cormorant Garamond', serif;
    }

    .sidebar.show { 
      left: 0; 
      padding-top: 80px;
    }

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
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
    }

    .sidebar a:hover {
      background-color: #D2B48C;
      color: #fff;
      transform: translateY(-2px);
    }

    .toggle-btn {
      position: fixed;
      top: 90px;
      left: 20px;
      z-index: 2000;
      background-color: rgba(255,255,255,0.95);
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      cursor: pointer;
      transition: all 0.3s ease;
      color: #5C4033;
      font-weight: bold;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
    }

    .sidebar.show ~ .toggle-btn {
      display: none;
    }

    .toggle-btn:hover {
      background-color: rgba(255,255,255,1);
      transform: scale(1.05);
    }

    .card-title, .card-text, .text-muted, .fw-bold, h6 {
      font-family: 'Cormorant Garamond', serif;
    }

    .fw-bold {
      font-weight: 600 !important;
    }

    .text-muted {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 300;
    }

    .chart-container {
      font-family: 'Cormorant Garamond', serif;
    }

    .progress, small {
      font-family: 'Lora', serif;
    }

    .rounded-circle {
      font-family: 'Lora', serif;
    }
  </style>
</head>

<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

<!-- 🌸 Sidebar (Staff-specific menu) -->
<div class="sidebar" id="sidebar">
    
    <a href="menu_list_staff.php">Add & Edit Menu</a>
    <a href="menu_list_staff.php">Delete Menu</a>
    <a href="customer_list.php">Edit Customer</a>
    <a href="view_feedback.php">View Feedback</a>
    <a href="vieworders.php">View Orders</a>
</div>

  <div class="container mt-5">
    <div class="dashboard-container">
      <h2 class="text-center mb-4">Staff Dashboard</h2>

      <!-- Summary Cards -->
      <div class="row text-center mb-4">
        <div class="col-md-3 mb-4">
          <div class="card shadow-sm border-0 rounded-4" style="background-color:#f5e9e0;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-1">Total Payments</h6>
                  <h3 class="fw-bold"><?= $totalOrders ?></h3>
                </div>
                <div class="rounded-circle p-3" style="background-color:#d6b8a1;">
                  <i class="bi bi-credit-card fs-4 text-white"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 mb-4">
          <div class="card shadow-sm border-0 rounded-4" style="background-color:#f5e9e0;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-1">Pending Orders</h6>
                  <h3 class="fw-bold"><?= $pendingOrders ?></h3>
                </div>
                <div class="rounded-circle p-3" style="background-color:#d6b8a1;">
                  <i class="bi bi-clock-fill fs-4 text-white"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 mb-4">
          <div class="card shadow-sm border-0 rounded-4" style="background-color:#f5e9e0;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-1">New Customers</h6>
                  <h3 class="fw-bold"><?= $newCustomers ?></h3>
                </div>
                <div class="rounded-circle p-3" style="background-color:#d6b8a1;">
                  <i class="bi bi-person-fill fs-4 text-white"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 mb-4">
          <div class="card shadow-sm border-0 rounded-4" style="background-color:#f5e9e0;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-1">Total Revenue</h6>
                  <h3 class="fw-bold">RM <?= number_format($totalSales, 2) ?></h3>
                </div>
                <div class="rounded-circle p-3" style="background-color:#d6b8a1;">
                  <i class="bi bi-cash-coin fs-4 text-white"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chart -->
      <div class="card p-4 shadow-sm chart-container">
        <h5 class="text-center mb-3">Daily Revenue Analytics (Last 30 Days)</h5>
        <canvas id="salesChart"></canvas>
      </div>

      <!-- Database Debug Info -->
      <div class="mt-4 p-3 bg-light rounded">
        <h6 class="text-muted">Database Information</h6>
        <div class="row">
          <div class="col-md-4">
            <small><strong>Total Payments:</strong> <?= $debugData['total_payments'] ?? 0 ?></small>
          </div>
          <div class="col-md-4">
            <small><strong>First Payment:</strong> <?= $debugData['first_payment'] ?? 'No data' ?></small>
          </div>
          <div class="col-md-4">
            <small><strong>Latest Payment:</strong> <?= $debugData['last_payment'] ?? 'No data' ?></small>
          </div>
        </div>
        <div class="mt-2">
          <small><strong>Chart Data Points:</strong> <?= count($days) ?></small><br>
          <small><strong>Data Range:</strong> <?= reset($days) ?: 'No start' ?> to <?= end($days) ?: 'No end' ?></small>
        </div>
      </div>
    </div>
  </div>

<script>
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.querySelector('.toggle-btn');

  function toggleSidebar() {
    sidebar.classList.toggle('show');
    if (sidebar.classList.contains('show')) {
      toggleBtn.style.display = 'none';
    } else {
      toggleBtn.style.display = 'block';
    }
  }

  // Close sidebar when clicking outside
  document.addEventListener('click', function(event) {
    if (sidebar.classList.contains('show') &&
        !sidebar.contains(event.target) &&
        event.target !== toggleBtn) {
      sidebar.classList.remove('show');
      toggleBtn.style.display = 'block';
    }
  });

  // Chart setup
  const days = <?= json_encode($days) ?>;
  const sales = <?= json_encode($dailySales) ?>;

  console.log('Chart Data:', { days, sales });

  if (days.length > 0 && sales.length > 0) {
    new Chart(document.getElementById('salesChart'), {
      type: 'line',
      data: {
        labels: days,
        datasets: [{
          label: 'Daily Revenue',
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
            padding: 10,
            callbacks: {
              label: function(context) {
                return 'RM ' + context.parsed.y.toFixed(2);
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.05)' },
            title: { display: true, text: 'RM (Revenue)' },
            ticks: {
              callback: function(value) {
                return 'RM ' + value;
              }
            }
          },
          x: {
            grid: { display: false },
            title: { display: true, text: 'Date' }
          }
        }
      }
    });
  } else {
    document.getElementById('salesChart').innerHTML = 
      '<div class="text-center p-4 text-muted">No payment data available for the chart.</div>';
  }
</script>

</body>
</html>