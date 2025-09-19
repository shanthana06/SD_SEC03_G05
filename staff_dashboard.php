<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body, html {
      margin: 0; 
      padding: 0; 
      height: 100%; 
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

    /* Background blur */
    .cart-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px) brightness(0.85);
      position: fixed;
      top:0; left:0;
      width:100%; height:100%;
      z-index:-1;
      transition: all 0.5s ease;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: -260px;
      width: 240px;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.95);
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

    .sidebar.show {
      left: 0;
    }

    .sidebar a {
      display: block;
      padding: 12px 15px;
      border-radius: 12px;
      text-align: center;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      color: #5C4033; /* dark brown text */
      background-color: #FDFCF9; /* soft off-white button */
    }

    .sidebar a:hover {
      background-color: #D2B48C; /* tan/brown */
      color: #fff;
      transform: translateY(-2px);
    }

    /* Toggle button */
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

    /* Dashboard content */
    .dashboard-content {
      margin-left: 20px;
      padding: 30px;
      transition: margin-left 0.3s ease;
    }

    .dashboard-content.shift {
      margin-left: 260px;
    }

    .dashboard-container {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      animation: fadeIn 1s ease;
    }

    .section-title {
      text-align:center;
      margin-bottom:1.5rem;
      font-size:2rem;
      color:#5C4033;
    }

    /* KPI Cards */
    .card {
      border-radius: 14px;
      transition: all 0.3s ease;
      background-color: #FCFAF7; /* soft off-white */
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="cart-bg-blur"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <a href="add_menu_staff.php">ADD MENU (STAFF)</a>
  <a href="menu_list_staff.php">EDIT MENU (STAFF)</a>
  <a href="menu_list_staff.php">DELETE MENU (STAFF)</a>
  <a href="edit_customer_staff.php">EDIT CUSTOMER (STAFF)</a>
  <a href="delete_customer_staff.php">DELETE CUSTOMER (STAFF)</a>
</div>

<!-- Toggle button -->
<button class="toggle-btn" id="toggleBtn">☰ Menu</button>

<!-- Dashboard main content -->
<div class="dashboard-content" id="dashboardContent">
  <div class="dashboard-container">
    <h2 class="text-center mb-4">Staff Dashboard</h2>

    <!-- KPI Cards -->
    <div class="row text-center mb-4">
      <div class="col-md-6 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Total Customers</h5>
          <p class="fs-3 fw-bold">250</p>
        </div>
      </div>
      <div class="col-md-6 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Feedback Received</h5>
          <p class="fs-3 fw-bold">36</p>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row">
      <div class="col-md-8 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Customer Growth (Last 6 Months)</h5>
          <canvas id="growthChart"></canvas>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Feedback Ratings</h5>
          <canvas id="feedbackChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Sidebar toggle
  const toggleBtn = document.getElementById('toggleBtn');
  const sidebar = document.getElementById('sidebar');
  const dashboard = document.getElementById('dashboardContent');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    dashboard.classList.toggle('shift');
  });

  // Charts with coffee-themed colors
  new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun'],
      datasets: [{
        label: 'Customers',
        data: [20,40,55,70,90,110],
        borderColor: '#8B5E3C',
        backgroundColor: 'rgba(139,94,60,0.2)',
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
        backgroundColor: ['#A9746E','#D2B48C','#F5E1C7','#C49E6C','#8B5E3C']
      }]
    }
  });
</script>

</body>
</html>
=======
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
>>>>>>> 9c3cfdedaaf306ac261286e46793cbcf989f68c9
