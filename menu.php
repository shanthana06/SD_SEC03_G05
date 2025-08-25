<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Your custom CSS file -->
<link href="style.css" rel="stylesheet"> <!-- Make sure this exists and includes navbar styles -->

  <meta charset="UTF-8" />
  <title>Menu | Arjuna n Co-ffee</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    body, html {
      height: 100%;
      margin: 0;
      padding: 0;
    }

    .login-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }

    .menu-container {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      padding: 40px 30px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .section-title {
      text-align: center;
      font-size: 2.2rem;
      margin-bottom: 1.5rem;
      color: #333;
    }

    .menu-card {
      transition: transform 0.3s ease;
    }

    .menu-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .add-to-cart {
      transition: all 0.3s ease;
    }

    .add-to-cart:hover {
      background-color: #dcb78d !important;
      color: white;
      transform: scale(1.05);
    }

    .filter-btns {
      text-align: center;
      margin-bottom: 2rem;
    }

    .filter-btns .btn {
      margin: 0 10px 10px 10px;
    }

    .hidden {
      display: none !important;
    }

    .return-home {
      text-align: center;
      margin-top: 2rem;
    }
  </style>
</head>
<body>

  <!-- Placeholder for the navbar -->
<div id="navbar-placeholder"></div>

<!-- Script to load the navbar.html file -->
<?php include 'navbar.php'; ?>
  <!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>


  <div class="login-bg-blur"></div>

 

  <section class="py-5">
    <div class="container menu-container">
      <h2 class="section-title">Our Menu</h2>

      <!-- Filter Buttons -->
      <div class="filter-btns">
        <button class="btn btn-outline-secondary filter-btn" data-filter="all">All</button>
        <button class="btn btn-outline-secondary filter-btn" data-filter="coffee">Coffee</button>
        <button class="btn btn-outline-secondary filter-btn" data-filter="food">Food</button>
        <button class="btn btn-outline-secondary filter-btn" data-filter="dessert">Dessert</button>
      </div>

      <div class="row g-4">

     <div class="row g-4">
  <!-- COFFEE ITEMS -->
  <div class="col-md-4 menu-item coffee" data-aos="fade-up" data-aos-delay="0">
    <div class="card menu-card">
      <img src="images/ia.jpg" class="card-img-top" alt="Americano">
      <div class="card-body text-center">
        <h5 class="card-title">Americano</h5>
        <p class="text-muted"><strong>RM 9.00</strong></p>
        <p class="card-text">Smooth and classic.</p>
        <button class="btn btn-secondary add-to-cart" data-name="Americano" data-price="9.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item coffee" data-aos="fade-up" data-aos-delay="100">
    <div class="card menu-card">
      <img src="images/espresso ice.jpg" class="card-img-top" alt="Espresso">
      <div class="card-body text-center">
        <h5 class="card-title">Signature Arjuna Coffee</h5>
        <p class="text-muted"><strong>RM 10.00</strong></p>
        <p class="card-text">Rich espresso blend with creamy milk</p>
        <button class="btn btn-secondary add-to-cart" data-name="Espresso" data-price="10.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item coffee" data-aos="fade-up" data-aos-delay="200">
    <div class="card menu-card">
      <img src="images/latte.jpg" class="card-img-top" alt="Latte">
      <div class="card-body text-center">
        <h5 class="card-title">Latte</h5>
        <p class="text-muted"><strong>RM 10.00</strong></p>
        <p class="card-text">Creamy with steamed milk.</p>
        <button class="btn btn-secondary add-to-cart" data-name="Latte" data-price="10.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item coffee" data-aos="fade-up" data-aos-delay="300">
    <div class="card menu-card">
      <img src="images/matchalatte.jpg" class="card-img-top" alt="Matcha Latte">
      <div class="card-body text-center">
        <h5 class="card-title">Matcha Latte</h5>
        <p class="text-muted"><strong>RM 12.00</strong></p>
        <p class="card-text">Japanese green tea blend.</p>
        <button class="btn btn-secondary add-to-cart" data-name="Matcha Latte" data-price="12.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item coffee" data-aos="fade-up" data-aos-delay="400">
    <div class="card menu-card">
      <img src="images/cf.jpg" class="card-img-top" alt="Chocolate Frappe">
      <div class="card-body text-center">
        <h5 class="card-title">Chocolate Frappe</h5>
        <p class="text-muted"><strong>RM 10.00</strong></p>
        <p class="card-text">Coffee meets chocolate.</p>
        <button class="btn btn-secondary add-to-cart" data-name="Chocolate Frappe" data-price="10.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <!-- FOOD ITEMS -->
  <div class="col-md-4 menu-item food" data-aos="fade-up" data-aos-delay="500">
    <div class="card menu-card">
      <img src="images/croissant.jpg" class="card-img-top" alt="Croissant">
      <div class="card-body text-center">
        <h5 class="card-title">Croissant</h5>
        <p class="text-muted"><strong>RM 9.00</strong></p>
        <p class="card-text">Buttery and flaky pastry</p>
        <button class="btn btn-secondary add-to-cart" data-name="Croissant" data-price="9.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item food" data-aos="fade-up" data-aos-delay="600">
    <div class="card menu-card">
      <img src="images/tunawrap.jpg" class="card-img-top" alt="Tuna Wrap">
      <div class="card-body text-center">
        <h5 class="card-title">Tuna Wrap</h5>
        <p class="text-muted"><strong>RM 9.40</strong></p>
        <p class="card-text">Tuna, veggies, and sauce in a wrap</p>
        <button class="btn btn-secondary add-to-cart" data-name="Tuna Wrap" data-price="9.40">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item food" data-aos="fade-up" data-aos-delay="700">
    <div class="card menu-card">
      <img src="images/caesarsalad.jpg" class="card-img-top" alt="Caesar Salad">
      <div class="card-body text-center">
        <h5 class="card-title">Caesar Salad</h5>
        <p class="text-muted"><strong>RM 10.00</strong></p>
        <p class="card-text">Romaine, croutons, parmesan, dressing</p>
        <button class="btn btn-secondary add-to-cart" data-name="Caesar Salad" data-price="10.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item food" data-aos="fade-up" data-aos-delay="800">
    <div class="card menu-card">
      <img src="images/sandwich.jpg" class="card-img-top" alt="Chicken Sandwich">
      <div class="card-body text-center">
        <h5 class="card-title">Chicken Sandwich</h5>
        <p class="text-muted"><strong>RM 6.00</strong></p>
        <p class="card-text">Grilled chicken with lettuce & mayo</p>
        <button class="btn btn-secondary add-to-cart" data-name="Chicken Sandwich" data-price="6.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <!-- DESSERT ITEMS -->
  <div class="col-md-4 menu-item dessert" data-aos="fade-up" data-aos-delay="900">
    <div class="card menu-card">
      <img src="images/brownie.jpg" class="card-img-top" alt="Brownie">
      <div class="card-body text-center">
        <h5 class="card-title">Brownie</h5>
        <p class="text-muted"><strong>RM 9.00</strong></p>
        <p class="card-text">Fudgy chocolate brownie.</p>
        <button class="btn btn-secondary add-to-cart" data-name="Brownie" data-price="9.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item dessert" data-aos="fade-up" data-aos-delay="1000">
    <div class="card menu-card">
      <img src="images/cheesecake.jpg" class="card-img-top" alt="Cheesecake Slice">
      <div class="card-body text-center">
        <h5 class="card-title">Cheesecake Slice</h5>
        <p class="text-muted"><strong>RM 15.00</strong></p>
        <p class="card-text">Creamy New York-style cheesecake</p>
        <button class="btn btn-secondary add-to-cart" data-name="Cheesecake" data-price="15.00">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="col-md-4 menu-item dessert" data-aos="fade-up" data-aos-delay="1100">
    <div class="card menu-card">
      <img src="images/lemoncake.jpg" class="card-img-top" alt="Lemon Cake">
      <div class="card-body text-center">
        <h5 class="card-title">Lemon Cake</h5>
        <p class="text-muted"><strong>RM 9.00</strong></p>
        <p class="card-text">Delicate, moist lemon cake layer</p>
        <button class="btn btn-secondary add-to-cart" data-name="Lemon Cake" data-price="9.00">Add to Cart</button>
      </div>
    </div>
  </div>
</div>


      <!-- Return to Home Button -->
      <div class="return-home">
        <a href="index.html" class="btn btn-outline-dark mt-4">Return to Home</a>
      </div>

    </div>
  </section>

  <!-- Scripts -->
  <script>
    // Filter logic
    const filterBtns = document.querySelectorAll('.filter-btn');
    const menuItems = document.querySelectorAll('.menu-item');

    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const filter = btn.getAttribute('data-filter');
        menuItems.forEach(item => {
          if (filter === 'all') {
            item.classList.remove('hidden');
          } else {
            item.classList.toggle('hidden', !item.classList.contains(filter));
          }
        });
      });
    });

    // Cart script
    document.querySelectorAll(".add-to-cart").forEach(button => {
      button.addEventListener("click", function () {
        const item = {
          name: this.dataset.name,
          price: parseFloat(this.dataset.price),
          quantity: 1
        };

        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        const existing = cart.find(i => i.name === item.name);
        if (existing) {
          existing.quantity += 1;
        } else {
          cart.push(item);
        }

        localStorage.setItem("cart", JSON.stringify(cart));
        window.location.href = "cart.html";
      });
    });
  </script>
</body>
</html>
