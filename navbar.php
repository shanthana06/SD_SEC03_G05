<?php

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold" href="#" style="font-family: 'Georgia', serif;">Arjuna n Co-ffee</a>
    <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarNav" aria-controls="sidebarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="menu.html">Menu</a></li>

        <?php if(isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link text-white" href="profile.php">Profile</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-white" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="signup.php">Sign Up</a></li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link text-white" href="contact_us.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="orderstatus.html">Order Status</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="orderhistory.html">Order History</a></li>
      </ul>
    </div>
  </div>
</nav>
