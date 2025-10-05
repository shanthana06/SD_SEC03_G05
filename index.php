<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Arjuna n Co-ffee Ordering System</title>

  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

  <style>
    body {
      margin: 0;
      background-color: #fdfaf6;
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }
    

    .hero-section {
      position: relative;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
      color: white;
      overflow: hidden;
    }

    .hero-blur-bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('images/AB1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px) brightness(0.6);
      z-index: 0;
    }

    .hero-section h1,
    .hero-section p {
      z-index: 1;
      text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.6);
    }

    .btn-primary {
      background-color: #6f4e37;
      border-color: #6f4e37;
    }

    .btn-primary:hover {
      background-color: #5a3e2b;
    }
    .custom-btn {
  background-color: transparent;
  border: 2px solid #c49a6c; /* coffee gold */
  color: #c49a6c;
  font-family: 'Poppins', sans-serif; /* elegant font */
  font-size: 1rem;
  padding: 10px 20px;
  border-radius: 30px;
  transition: all 0.3s ease;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.custom-btn:hover {
  background-color: #c49a6c;
  color: white;
  text-decoration: none;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}


   .offcanvas-custom {
  background: rgba(107, 81, 80, 0.85); /* semi-transparent white */
  backdrop-filter: blur(15px); /* glass effect */
  -webkit-backdrop-filter: blur(15px);
  color: #333;
  border-right: 1px solid rgba(0, 0, 0, 0.1);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.offcanvas-custom .offcanvas-title {
  font-family: 'Playfair Display', serif;
  font-weight: bold;
  color: #ffffff;
}

.offcanvas-custom .nav-link {
  color: #333;
  font-weight: 500;
  padding: 10px 0;
  font-family: 'Roboto', sans-serif;
  transition: all 0.3s ease;
}

.offcanvas-custom .nav-link:hover {
  background-color: rgba(0, 0, 0, 0.05);
  border-radius: 8px;
  padding-left: 14px;
  color: #000;
}

.logout-btn {
  position: absolute;
  bottom: 20px;
  width: 100%;
}


.see-menu-btn {
  background-color: transparent;
  color: white;
  border: 2px solid white;
  padding: 10px 30px;
  font-size: 0.9rem;
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 1px;
  border-radius: 4px;
  transition: all 0.3s ease;
  text-decoration: none;
}

.see-menu-btn:hover {
  background-color: white;
  color: #333;
  text-decoration: none;
}

.see-menu-btn {
  background-color: transparent;
  border: 2px solid #fff;
  color: #fff;
  padding: 10px 20px;
  font-weight: 500;
  border-radius: 30px;
  text-transform: uppercase;
  transition: all 0.3s ease;
  backdrop-filter: blur(4px);
}

.see-menu-btn:hover {
  background-color: #fff;
  color: #000;
  text-decoration: none;
}


    .menu-toggle {
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 10;
    }
  </style>
  <!-- Example of Google Elegant Fonts -->
<link href="https://fonts.gstatic.com/s/roboto/v48/KFOMCnqEu92Fr1ME7kSn66aGLdTylUAMQXC89YmC2DPNWubEbVmaiArmlw.woff2" rel="stylesheet">

<style>
  body {
    font-family: 'Playfair Display', serif;
  }

  h1, h2, h3, nav {
    font-family: 'Playfair Display', serif;
  }

  p, a, button {
    font-family: 'Roboto', sans-serif;
  }
</style>

</head>

<body>

  <!-- Sidebar Toggle Button -->
  <button class="btn btn-outline-light menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarNav" aria-controls="sidebarNav">
    <i class="bi bi-list fs-3"></i>
  </button>

  <!-- Sidebar Toggle Button -->
<button class="btn btn-outline-light menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarNav" aria-controls="sidebarNav">
  <i class="bi bi-list fs-3"></i>
</button>

<!-- Sidebar Offcanvas Menu -->
<div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1" id="sidebarNav">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title fw-bold">Arjuna n Co-ffee</h5>
    <button type="button" class="btn-close text-reset bg-light" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column justify-content-between">
    <nav class="nav flex-column">
      <a href="index.php" class="nav-link text-white"><i class="bi bi-house-door-fill me-2"></i>Home</a>
      <a href="menu.php" class="nav-link text-white"><i class="bi bi-cup-hot me-2"></i>Menu</a>
      <a href="login.php" class="nav-link text-white"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
      <a href="signup.php" class="nav-link text-white"><i class="bi bi-person-plus-fill me-2"></i>Sign Up</a>
      <a href="profile.php" class="nav-link text-white"><i class="bi bi-person-fill me-2"></i>Profile</a>
      <a href="contact_us.php" class="nav-link text-white"><i class="bi bi-envelope-fill me-2"></i>Contact</a>
      <a href="orderstatus.html" class="nav-link text-white"><i class="bi bi-info-circle-fill me-2"></i>Order Status</a>
      <a href="orderhistory.html" class="nav-link text-white"><i class="bi bi-clock-history me-2"></i>Order History</a>
    </nav>

    <!-- Logout Button at Bottom -->
    <div class="mt-4">
      <a href="logout.php" class="nav-link text-white"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>
  </div>
</div>

  <!-- Hero Section -->
  <section class="hero-section text-white position-relative">
    <div class="hero-blur-bg"></div>

    <p class="mb-2 fs-5" style="letter-spacing: 2px;" data-aos="fade-down">
      <img src="https://cdn-icons-png.flaticon.com/128/16508/16508920.png" alt="Coffee Icon" width="30" height="30" style="margin-right: 8px;">
      Crafted with Love.
    </p>

    <h1 data-aos="fade-down">WELCOME TO ARJUNA N CO-FFEE</h1>

    <p class="lead" data-aos="fade-up">
      
    </p>
<?php
if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff', 'customer'])) {
    echo '<a href="menu.php" class="btn see-menu-btn">ORDERING NOW</a>';
}
?>


<div class="d-flex justify-content-center gap-3 mt-3" data-aos="fade-up" data-aos-delay="200">

  <a href="#about" class="btn see-menu-btn">EXPLORE MORE</a>
</div>

   

  </section>

  <!-- Bootstrap + AOS JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
</body>

</html>

  <!-- Call to Action Buttons -->
  

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content custom-order-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="orderModalLabel">Choose Order Type</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Add your order type form/buttons here -->
        <p>Please select your order method:</p>
        <div class="d-flex flex-column gap-3">
          <a href="menu.html" class="btn btn-outline-dark">Dine-In</a>
          <a href="menu.html" class="btn btn-outline-secondary">Pickup</a>
        </div>
      </div>
    </div>
  </div>
</div>


  <!-- AOS Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
  </script>
</body>

</html>

  <!-- About Section -->
  <section id="about" class="py-5" data-aos="popup">
  <div class="container" data-aos="fade-up">
    <h2 class="text-center mb-4 section-title" data-aos="fade-down">About Us</h2>

      <!-- 1 -->
      <div class="row align-items-center">
        <div class="col-md-6">
          <img src="images/arjunashop.jpg" class="img-fluid" alt="About Arjuna n Co-ffee">
        </div>
        <div class="col-md-6">
          <h4 class="mb-3">At Arjuna n Co-ffee</h4>
          <p class="mb-3">We believe coffee is more than a drink. It's an experience. Founded with a passion for crafting artisanal coffee and serving our community, we blend traditional brewing techniques with modern convenience.</p>
          <p class="mb-3">Whether you're stopping by for a rich espresso, working remotely over a matcha latte, or ordering online for a quick pickup, we're here to make every sip meaningful.</p>
          <p>Join us at our café or order from your table with our contactless ordering system. It's coffee, reimagined.</p>
        </div>
      </div>

      <!-- 2 -->
      <div class="row align-items-center mt-5">
        <div class="col-md-6 order-md-2">
          <img src="images/coffeegirl.jpg" class="img-fluid" alt="Coffee Brewing Process">
        </div>
        <div class="col-md-6 order-md-1">
          <h4 class="mb-3">From Bean to Cup</h4>
          <p>Every cup we serve starts with carefully selected beans sourced from ethical farms. Our roasting process brings out the unique flavors of each origin, while our baristas ensure every drink is brewed to perfection.</p>
          <p>We’re committed to sustainability and transparency, because great coffee begins with great values.</p>
        </div>
      </div>

      <!-- 3 -->
      <div class="row align-items-center mt-5">
        <div class="col-md-6">
          <img src="images/table.jpg" class="img-fluid" alt="Cafe Ambience">
        </div>
        <div class="col-md-6">
          <h4 class="mb-3">More Than Just Coffee</h4>
          <p>At Arjuna n Co-ffee, we understand that our customers don’t just come for the caffeine—they come for the calm. Every corner of our café is thoughtfully designed to offer comfort, warmth, and a sense of belonging.</p>
          <p>Whether you're catching up with friends, reading a book, or working remotely, our cozy ambiance and gentle playlists create a space where moments linger longer and coffee tastes even better.</p>
        </div>
      </div>
    </div>

   <!-- Video Section -->
<div class="row justify-content-center mt-5" data-aos="zoom-in">
  <div class="col-md-8 text-center"> <!-- Reduced from col-md-10 to col-md-8 -->
    <h4 class="mb-4">From bean to cup, learn how we craft the perfect latte<br> and showcase our finest coffee beans.</h4>
    <div class="ratio ratio-16x9" style="max-width: 700px; margin: 0 auto;"> <!-- Limit max width -->
      <video controls poster="images/2coffee.jpg">
        <source src="coffee.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
  </div>
</div>


    
  </section>

  <section class="py-5" style="background-color: #fff7f0;">
  <div class="container">
    <h2 class="text-center mb-4 section-title">How It Works?</h2>
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="card p-3 h-100">
          <h4>1. Scan QR or Access Site</h4>
          <p>Use QR code at the table or go to our website.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card p-3 h-100">
          <h4>2. Place Your Order</h4>
          <p>Select items, customize, and add to cart.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card p-3 h-100">
          <h4>3. Enjoy Your Coffee</h4>
          <p>Wait for your name to be called or pick up your order.</p>
        </div>
      </div>
    </div>
  </div>
</section>


     <!-- Feedback Section -->
<section class="py-5" style="background-color: #f8f3ef;" id="feedback">
  <div class="container">
    <h2 class="text-center section-title mb-5">💬 What Our Customers Say</h2>
    <div class="row g-4">

      <!-- Feedback 1 -->
      <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0">
          <img src="images/user1.jpg" alt="Aina M." class="rounded-circle mx-auto mb-3" width="80" height="80" style="object-fit: cover;">
          <i class="bi bi-chat-left-heart-fill text-danger" style="font-size: 1.5rem;"></i>
          <p class="mt-3">“Best coffee in town! The caramel latte is heavenly and the café ambiance is perfect.”</p>
          <h6 class="mb-0">Aina M.</h6>
        </div>
      </div>

      <!-- Feedback 2 -->
      <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0">
          <img src="images/user2.jpg" alt="Jason L." class="rounded-circle mx-auto mb-3" width="80" height="80" style="object-fit: cover;">
          <i class="bi bi-chat-left-quote-fill text-warning" style="font-size: 1.5rem;"></i>
          <p class="mt-3">“I love how I can order online and pick up without waiting. So convenient!”</p>
          <h6 class="mb-0">Jason L.</h6>
        </div>
      </div>

      <!-- Feedback 3 -->
      <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0">
          <img src="images/user3.jpg" alt="Nurul A." class="rounded-circle mx-auto mb-3" width="80" height="80" style="object-fit: cover;">
          <i class="bi bi-chat-left-dots-fill text-primary" style="font-size: 1.5rem;"></i>
          <p class="mt-3">“Super friendly staff and cozy place to chill or work with great coffee.”</p>
          <h6 class="mb-0">Nurul A.</h6>
        </div>
      </div>

    </div>
  </div>
</section>


 <!-- Contact -->
<section id="contact" class="py-5 text-center text-dark" style="position: relative; overflow: hidden;">
  <!-- Blurred Background -->
  <div style="
    background-image: url('images/arjunabackground.jpg');
    background-size: cover;
    background-position: center;
    filter: blur(6px);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
  "></div>

  <!-- Content with overlay -->
  
    <div class="container">
      <h2 class="section-title mb-3">Contact Us</h2>
      <p>Email: contact@arjunacoffee.com | Phone: +6012-3456789</p>
      <div class="social-icons mt-3">
        <a href="https://instagram.com" target="_blank" aria-label="Instagram" style="color: black; font-size: 1.5rem; margin: 0 10px;"><i class="bi bi-instagram"></i></a>
        <a href="https://x.com" target="_blank" aria-label="Twitter/X" style="color: black; font-size: 1.5rem; margin: 0 10px;"><i class="bi bi-twitter-x"></i></a>
        <a href="https://facebook.com" target="_blank" aria-label="Facebook" style="color: black; font-size: 1.5rem; margin: 0 10px;"><i class="bi bi-facebook"></i></a>
        <a href="https://wa.me/60123456789" target="_blank" aria-label="WhatsApp" style="color: black; font-size: 1.5rem; margin: 0 10px;"><i class="bi bi-whatsapp"></i></a>
      </div>
    </div>
  </div>
</section>



  <!-- Footer -->
  <footer class="text-black text-center py-3">
    &copy; 2025 Arjuna n Co-ffee. All rights reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Order Type Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="orderModalLabel">Choose Order Type</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-4">Would you like to dine-in or pick up your order?</p>
        <a href="menu.html?type=dine-in" class="btn btn-outline-primary me-2">Dine-In</a>
        <a href="menu.html?type=pickup" class="btn btn-outline-success">Pickup</a>
      </div>
    </div>
  </div>
</div>

</body>

</html>
