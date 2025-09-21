<<<<<<< HEAD
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Arjuna n Co-ffee</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .custom-navbar {
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .custom-navbar .navbar-brand {
      font-size: 1.6rem;
      color: #ffffff;
      text-shadow: 0 0 10px #fff;
      font-family: 'Georgia', serif;
    }

    .custom-navbar .nav-link {
      color: #ffffff !important;
      margin: 0 0.5rem;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .custom-navbar .nav-link:hover {
      color: #00d4ff !important;
      transform: scale(1.05);
      text-shadow: 0 0 5px rgba(0, 212, 255, 0.5);
    }

    .navbar-toggler {
      border-color: rgba(255, 255, 255, 0.5);
    }

    .dropdown-menu {
      background-color: #f8f9fa;
      border: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .dropdown-item:hover {
      background-color: #e9ecef;
    }
  </style>
</head>
<body style="background-color:#0094ff; min-height: 100vh;">

<nav class="navbar navbar-expand-lg navbar-dark custom-navbar shadow-sm">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold" href="#">Arjuna n Co-ffee</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
      <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">

        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>

        

        <?php if(isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>

          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'staff'): ?>
            <li class="nav-item">
              <a class="nav-link text-warning fw-bold" href="staff_dashboard.php">🛠 Staff Dashboard</a>
            </li>
          <?php endif; ?>

          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link" href="contact_us.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="orderstatus.html">Order Status</a></li>
        <li class="nav-item"><a class="nav-link" href="orderhistory.html">Order History</a></li>
        <li class="nav-item"><a class="nav-link" href="change_password.php">Change Password</a></li>

      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
