<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Staff Dashboard | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .dashboard-container {
      background-color: rgba(255,255,255,0.95);
      border-radius: 12px;
      padding: 30px;
      max-width: 1000px;
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
  <h2 class="text-center mb-4">Staff Dashboard</h2>

  <!-- KPI Cards -->
  <div class="row text-center mb-4">
    <div class="col-md-6">
      <div class="card p-3">
        <h5>Total Customers</h5>
        <p class="fs-3 fw-bold">250</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <h5>Feedback Received</h5>
        <p class="fs-3 fw-bold">36</p>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="row">
    <div class="col-md-8 mb-3">
      <div class="card p-3">
        <h5>Customer Growth (Last 6 Months)</h5>
        <canvas id="growthChart"></canvas>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card p-3">
        <h5>Feedback Ratings</h5>
        <canvas id="feedbackChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
new Chart(document.getElementById('growthChart'), {
  type: 'line',
  data: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun'],
    datasets: [{
      label: 'Customers',
      data: [20,40,55,70,90,110],
      borderColor: '#fd7e14',
      backgroundColor: 'rgba(253,126,20,0.2)',
      fill: true,
      tension: 0.3
    }]
  }
});

new Chart(document.getElementById('feedbackChart'), {
  type: 'doughnut',
  data: {
    labels: ['5★','4★','3★','2★','1★'],
    datasets: [{
      data: [20,10,4,2,1],
      backgroundColor: ['#28a745','#17a2b8','#ffc107','#fd7e14','#dc3545']
    }]
  }
});
</script>
</body>
</html>
