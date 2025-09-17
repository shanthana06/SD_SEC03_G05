<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Arjuna n Co-ffee</title>
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
    .dashboard-content.shift { margin-left: 260px; }

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

    /* Cards */
    .card {
      border-radius: 14px;
      transition: all 0.3s ease;
      background-color: #FCFAF7;
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
  <a href="add_menu_admin.php">ADD MENU (ADMIN)</a>
  <a href="edit_customer_admin.php">EDIT CUSTOMER (ADMIN)</a>
  <a href="delete_customer_admin.php">DELETE CUSTOMER (ADMIN)</a>

  <!-- Staff functions -->
  <a href="add_staff.php">ADD STAFF (ADMIN)</a>
  <a href="view_staff.php">VIEW STAFF (ADMIN)</a>
  <a href="edit_staff.php">EDIT STAFF (ADMIN)</a>
  <a href="delete_staff.php">DELETE STAFF (ADMIN)</a>
</div>

<!-- Toggle button -->
<button class="toggle-btn" id="toggleBtn">☰ Menu</button>

<!-- Dashboard main content -->
<div class="dashboard-content" id="dashboardContent">
  <div class="dashboard-container">
    <h2 class="text-center mb-4">Admin Dashboard</h2>

    <!-- KPI Cards -->
    <div class="row text-center mb-4">
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Total Customers</h5>
          <p class="fs-3 fw-bold">250</p>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Total Staff</h5>
          <p class="fs-3 fw-bold">18</p>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Reports Generated</h5>
          <p class="fs-3 fw-bold">42</p>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row">
      <div class="col-md-8 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Customer & Staff Growth</h5>
          <canvas id="growthChart"></canvas>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>Reports Overview</h5>
          <canvas id="reportChart"></canvas>
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

  // Charts
  new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun'],
      datasets: [
        {
          label: 'Customers',
          data: [20,40,55,70,90,110],
          borderColor: '#8B5E3C',
          backgroundColor: 'rgba(139,94,60,0.2)',
          fill: true,
          tension: 0.3
        },
        {
          label: 'Staff',
          data: [10,12,14,16,17,18],
          borderColor: '#D2B48C',
          backgroundColor: 'rgba(210,180,140,0.3)',
          fill: true,
          tension: 0.3
        }
      ]
    }
  });

  new Chart(document.getElementById('reportChart'), {
    type: 'doughnut',
    data: {
      labels: ['Completed','Pending','In Progress'],
      datasets: [{
        data: [28,8,6],
        backgroundColor: ['#A9746E','#D2B48C','#8B5E3C']
      }]
    }
  });
</script>

</body>
</html>
