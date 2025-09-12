<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background: #fff;
      border-right: 1px solid #ddd;
      padding: 20px;
      position: fixed;
      top: 0;
      left: 0;
      width: 240px;
    }
    .sidebar h4 {
      font-weight: bold;
      margin-bottom: 20px;
    }
    .sidebar a {
      display: block;
      padding: 10px 15px;
      border-radius: 8px;
      color: #333;
      text-decoration: none;
      margin-bottom: 8px;
    }
    .sidebar a:hover {
      background: #f1f1f1;
    }
    .content {
      margin-left: 260px;
      padding: 30px;
    }
    .feature-card {
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      padding: 20px;
      background: #fff;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h4>☕ Arjuna n Co-ffee</h4>
  <a href="admin_dashboard.php">📊 Dashboard</a>
  <a href="manage_menu.php">🍴 Menu</a>
  <a href="manage_customers.php">👤 Customers</a>
  <a href="manage_staff.php">👥 Staff</a>
  <a href="reports.php">📑 Reports</a>
</div>

<div class="content">
  <h2 class="mb-4">Admin Dashboard</h2>

  <!-- Example Features -->
  <div class="row">
    <div class="col-md-4">
      <div class="feature-card">
        <h6>ADD MENU (ADMIN)</h6>
        <a href="add_menu_admin.php" class="btn btn-success btn-sm">Go</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <h6>EDIT CUSTOMER (ADMIN)</h6>
        <a href="edit_customer_admin.php" class="btn btn-info btn-sm">Go</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <h6>ADD STAFF (ADMIN)</h6>
        <a href="add_staff.php" class="btn btn-primary btn-sm">Go</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
