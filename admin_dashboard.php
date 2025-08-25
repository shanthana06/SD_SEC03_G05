<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .dashboard-container {
      background-color: rgba(255,255,255,0.95);
      border-radius: 12px;
      padding: 30px;
      max-width: 1200px;
      margin: 40px auto;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- Background -->
<div class="cart-bg-blur" style="
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed; top:0; left:0;
  width:100%; height:100%; z-index:-1;">
</div>

<div class="dashboard-container">
  <h2 class="text-center mb-4">Admin Dashboard</h2>

  <!-- KPI Cards -->
  <div class="row text-center mb-4">
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Total Staff</h5>
        <p class="fs-3 fw-bold">12</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Total Customers</h5>
        <p class="fs-3 fw-bold">250</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Monthly Sales</h5>
        <p class="fs-3 fw-bold">RM 12,560</p>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="row">
    <div class="col-md-8 mb-3">
      <div class="card p-3">
        <h5>Sales Report (Last 6 Months)</h5>
        <canvas id="salesChart"></canvas>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card p-3">
        <h5>Customer Distribution</h5>
        <canvas id="customerChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
new Chart(document.getElementById('salesChart'), {
  type: 'line',
  data: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun'],
    datasets: [{
      label: 'Sales (RM)',
      data: [2000, 3500, 3000, 4500, 5000, 5600],
      borderColor: '#6f42c1',
      backgroundColor: 'rgba(111,66,193,0.2)',
      fill: true,
      tension: 0.3
    }]
  }
});

new Chart(document.getElementById('customerChart'), {
  type: 'doughnut',
  data: {
    labels: ['Regular','New','VIP'],
    datasets: [{
      data: [120,90,40],
      backgroundColor: ['#0d6efd','#198754','#ffc107']
    }]
  }
});
</script>
</body>
</html>
