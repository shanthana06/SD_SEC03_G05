<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="desktop portrait m">

<head>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Parisienne&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">


  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Arjuna n Co-ffee | Home</title>

  <!-- Page Fade Animation -->
  <style>
    body {
      transition: opacity ease-in 0.6s;
    }
body {
  transition: opacity ease-in 0.6s;
}
body[unresolved] {
  opacity: 0;
}

    body[unresolved] {
      opacity: 0;
      display: block;
      overflow: hidden;
      position: relative;
    }
  </style>

  <!-- Preconnect for speed -->
  <link rel="preconnect" href="https://static.showit.co">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Elegant Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Lora', serif;
      background-color: #fcfbf9;
      color: #2d2d2d;
      overflow-x: hidden;
    }

h1 {
  font-family: 'Playfair Display', serif;
  font-size: 60px;
  color: #3b2f2f;
}

h2 {
  font-family: 'Parisienne', cursive;
  font-size: 40px;
  color: #8b6f47;
}

p {
  font-family: 'Cormorant Garamond', serif;
  font-size: 18px;
  color: #4a3c2f;
}

    header {
      position: fixed;
      width: 100%;
      top: 0;
      left: 0;
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(8px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 60px;
      z-index: 1000;
    }

    header h4 {
      font-family: 'Parisienne', cursive;
      font-size: 1.8rem;
      color: #3d2b1f;
    }

    header nav a {
      text-decoration: none;
      color: #3d2b1f;
      margin: 0 15px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.05rem;
      letter-spacing: 0.5px;
      transition: color 0.3s ease;
    }

    header nav a:hover {
      color: #caa472;
    }

    .hero {
      height: 100vh;
      background: url('images/AB1.jpg') center/cover no-repeat;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
      color: #fff;
      position: relative;
    }

    .hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(3px);
    }

    .hero-content {
      position: relative;
      z-index: 2;
      animation: fadeUp 1.5s ease-in-out;
    }

    .hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: 3.2rem;
      letter-spacing: 1px;
      margin-bottom: 15px;
    }

    .hero p {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: #f4e6d0;
    }

    .btn-coffee {
      padding: 12px 40px;
      border: 2px solid #fff;
      border-radius: 40px;
      background: transparent;
      color: #fff;
      font-family: 'Cormorant Garamond', serif;
      letter-spacing: 1px;
      transition: all 0.4s ease;
      text-decoration: none;
    }

    .btn-coffee:hover {
      background-color: #fff;
      color: #2e2e2e;
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    section {
      padding: 100px 10%;
    }

    .about {
      display: flex;
      align-items: center;
      gap: 60px;
      flex-wrap: wrap;
    }

    .about img {
      width: 100%;
      max-width: 500px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      transition: transform 0.5s ease;
    }

    .about img:hover {
      transform: scale(1.03);
    }

    .about-text h2 {
      font-family: 'Playfair Display', serif;
      font-size: 2.3rem;
      color: #3b2b1f;
      margin-bottom: 20px;
    }

    .about-text p {
      font-size: 1rem;
      line-height: 1.8;
      color: #5a4c43;
    }

    footer {
      background: #f2ece6;
      text-align: center;
      padding: 40px;
      font-size: 0.9rem;
      color: #7b6d5b;
    }
    .image-stack {
  position: relative;
  height: 100vh;
  overflow: hidden;
  background: url('images/coffee1.jpg') center/cover no-repeat;
}

/* --- Center the text beautifully --- */
.text-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  z-index: 10;
  animation: fadeInText 1.5s ease-in-out;
}

.text-overlay h2 {
  font-family: 'Parisienne', cursive;
  font-size: 3rem;
  color: #f5f0c9;
  letter-spacing: 2px;
  margin: 0;
}

.text-overlay h1 {
  font-family: 'Playfair Display', serif;
  font-size: 6rem;
  color: #f5f0c9;
  letter-spacing: 3px;
  margin: 0;
}

/* --- Fade animation --- */
@keyframes fadeInText {
  from { opacity: 0; transform: translate(-50%, -55%); }
  to { opacity: 1; transform: translate(-50%, -50%); }
}
/* --- SANCTUARY SECTION --- */
.sanctuary-section {
  position: relative;
  height: 100vh;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f4ee;
}

.sanctuary-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: url('images/sanctuary-bg.jpg') center/cover no-repeat;
  filter: brightness(85%) contrast(95%);
  z-index: 1;
  animation: zoomFade 15s ease-in-out infinite alternate;
}

.sanctuary-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: #fff;
  padding: 0 20px;
}

.sanctuary-content h2 {
  font-family: 'Parisienne', cursive;
  font-size: 2.5rem;
  color: #f1e6b0;
  margin-bottom: 10px;
}

.sanctuary-content h1 {
  font-family: 'Playfair Display', serif;
  font-size: 4rem;
  letter-spacing: 2px;
  color: #ffffff;
  margin-bottom: 20px;
}

.sanctuary-content p {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.2rem;
  color: #f6f1e8;
  max-width: 600px;
  margin: 0 auto 30px;
  line-height: 1.6;
}

.sanctuary-btn {
  display: inline-block;
  padding: 12px 32px;
  font-family: 'Playfair Display', serif;
  font-size: 1.1rem;
  background: #e8dbc5;
  color: #3b2f2f;
  border-radius: 25px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.sanctuary-btn:hover {
  background: #d7c5a6;
  transform: scale(1.05);
}

@keyframes zoomFade {
  0% { transform: scale(1); opacity: 1; }
  100% { transform: scale(1.05); opacity: 0.95; }
}

.image-stack {
  position: relative;
  height: 150vh;
  top: 12%;
  overflow: hidden;
  background: url('images/scenery.jpg') center/cover no-repeat;
}
.image-stack h2 {
  position: absolute;
  top: 12%;
  left: 6%;
  font-family: 'Parisienne', cursive;
  font-size: 2.5rem;
  color: #f5f0c9;
  letter-spacing: 2px;
}

.image-stack h1 {
  position: absolute;
  top: 8%;
  left: 15%;
  font-family: 'Playfair Display', serif;
  font-size: 5rem;
  color: #f5f0c9;
  letter-spacing: 3px;
}


.text-overlay {
  top: 40%; /* was 50% */
}
.image-stack img {
  position: absolute;
  opacity: 0;
  transform: scale(0.95);
  transition: all 1.2s ease;
}

.image-stack img.visible {
  opacity: 1;
  transform: scale(1);
}
.dreamy-section {
  width: 100%;
  background-color: #f9f7f3;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 100px 0;
  margin-top: 80px;
}

/* remove container limits, make it stretch fully */
.dreamy-container {
  display: flex;
  flex-wrap: wrap;
  width: 100%;
  background-color: #fdfbf8;
  border-radius: 0;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}


/* Left image */
.dreamy-image {
  flex: 1;
  min-width: 300px;
  background-image: url('images/dualipa.jpg');
  background-size: cover;
  background-position: center;
  height: 700px;
}
.coffee-showcase {
  position: relative;
  text-align: center;
  background-color: #e8e8ef;
  padding: 100px 20px;
  overflow: hidden;
}

.coffee-title {
  font-size: 3rem;
  font-family: 'Playfair Display', serif;
  color: #1e2235;
  margin-bottom: 40px;
  z-index: 2;
  position: relative;
}

.coffee-bg-text {
  font-size: 8rem;
  color: rgba(30, 34, 53, 0.1);
  white-space: nowrap;
  position: absolute;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  animation: scrollText 30s linear infinite;
}

@keyframes scrollText {
  from { transform: translateX(0) translateY(-50%); }
  to { transform: translateX(-50%) translateY(-50%); }
}

.coffee-content img {
  width: 320px;
  border-radius: 10px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
  z-index: 2;
  position: relative;
}

.coffee-buttons {
  margin-top: 40px;
}

.coffee-btn {
  display: inline-block;
  margin: 0 10px;
  padding: 12px 30px;
  border: 1px solid #1e2235;
  border-radius: 30px;
  text-decoration: none;
  color: #1e2235;
  font-family: 'Poppins', sans-serif;
  transition: 0.3s;
}

.coffee-btn:hover {
  background-color: #1e2235;
  color: #fff;
}

/* Right text content */
.dreamy-content {
  flex: 1.2;
  background-color: #f9f7f3;
  padding: 80px 60px;
  text-align: center;
}

.dreamy-title {
  font-family: 'Playfair Display', serif;
  color: #2f3a56;
  font-size: 3rem;
  margin-bottom: 10px;
}

.dreamy-subtitle {
  font-family: 'Parisienne', cursive;
  color: #536480;
  font-size: 2rem;
  margin-bottom: 40px;
}

.dreamy-heading {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  letter-spacing: 1px;
  color: #536480;
  text-transform: uppercase;
  margin-bottom: 40px;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.dreamy-paragraph {
  font-family: 'Cormorant Garamond', serif;
  color: #5a4c43;
  font-size: 1.1rem;
  line-height: 1.8;
  max-width: 550px;
  margin: 0 auto 20px;
}

.dreamy-button a {
  display: inline-block;
  background-color: #2f3a56;
  color: #fff;
  text-decoration: none;
  padding: 12px 40px;
  border-radius: 40px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 1rem;
  margin-top: 30px;
  transition: all 0.3s ease;
}
/* --- Arjuna Coffee Showcase Section --- */
.coffee-showcase {
  position: relative;
  background-color: #e8e8ef;
  text-align: center;
  padding: 8rem 2rem;
  overflow: hidden;
}

.coffee-title {
  font-family: 'Playfair Display', serif;
  font-size: 4rem;
  color: #1e2235;
  position: relative;
  z-index: 10;
  margin-bottom: 2rem;
}

/* animated background text */
.coffee-bg-text {
  position: absolute;
  top: 50%;
  left: 0;
  width: 200%;
  transform: translateY(-50%);
  white-space: nowrap;
  font-size: 7rem;
  color: rgba(30, 34, 53, 0.08);
  font-family: 'Playfair Display', serif;
  animation: scrollText 30s linear infinite;
}

@keyframes scrollText {
  from { transform: translateX(0) translateY(-50%); }
  to { transform: translateX(-50%) translateY(-50%); }
}

.coffee-content {
  position: relative;
  z-index: 10;
  display: flex;
  justify-content: center;
  margin-top: 4rem;
}

.coffee-img {
  width: 350px;
  height: auto;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  transition: transform 0.4s ease;
}

.coffee-img:hover {
  transform: scale(1.05);
}

.coffee-buttons {
  position: relative;
  z-index: 10;
  margin-top: 3rem;
}

.coffee-btn {
  display: inline-block;
  padding: 0.75rem 2.5rem;
  margin: 0 0.8rem;
  border: 1.5px solid #555;
  border-radius: 9999px;
  color: #333;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.9rem;
  letter-spacing: 1px;
  background: transparent;
  transition: all 0.3s ease;
}

.coffee-btn:hover {
  background-color: rgba(255, 255, 255, 0.6);
  border-color: #222;
  transform: translateY(-3px);
}

.dreamy-button a:hover {
  background-color: #4b5b77;
}

.img1 { top: 60%; left: 5%; width: 200px; }
.img2 { 
  top: 45%; /* <-- Change this: Moved UP to prevent it from being cut off */
  left: 30%; /* <-- Adjusted slightly to the left for balance */
  width: 300px; /* <-- Slightly increased width to make it a focal point */
} 
.img3 { top: 50%; right: 5%; width: 250px; }
/* --- SANCTUARY SECTION STYLING --- */
.sanctuary-hero {
  height: 100vh;
  background: url('images/girl.jpg') center/cover no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  text-align: center;
  margin-top: 0;
}

.sanctuary-hero::after {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.25);
}

.sanctuary-content {
  position: relative;
  z-index: 2;
  color: #fff;
}

.sanctuary-content h1 {
  font-family: 'Playfair Display', serif;
  font-size: 5rem;
  color: #f7f1e7;
  letter-spacing: 2px;
  margin-bottom: 5px;
  animation: fadeIn 1.5s ease-out;
}

.sanctuary-content h2 {
  font-family: 'Parisienne', cursive;
  font-size: 4rem;
  color: #f7f1e7;
  margin-top: 0;
  margin-bottom: 30px;
  animation: fadeIn 2s ease-out;
}

.sanctuary-content p {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.2rem;
  color: #fff;
  letter-spacing: 3px;
  text-transform: uppercase;
  margin-bottom: 50px;
  animation: fadeIn 2.5s ease-out;
}
.coffee-title {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 10px; /* smaller spacing */
}

.italic-word {
  font-style: italic;
  font-weight: 500;
}

/* keep layout together */
.coffee-showcase {
  margin-bottom: 0;
  padding-bottom: 0;
}

.scroll-indicator {
  position: absolute;
  bottom: 50px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 3;
  color: #fff;
  font-size: 2rem;
  animation: bounce 2s infinite;
}

.sanctuary-text {
  position: relative;
  background: url('images/bg.jpg') center/cover no-repeat;
  color: white;
  text-align: center;
  padding: 120px 20px;
}



/* make sure text stays above the overlay */
.sanctuary-text .overlay {
  position: relative;
  z-index: 1;
}

.sanctuary-text h2 {
  font-size: 2.5rem;
  margin-bottom: 20px;
  font-style: italic;
}

.sanctuary-text p {
  max-width: 700px;
  margin: 0 auto;
  font-size: 1.1rem;
  line-height: 1.8;
}
        .services-section {
            padding: 120px 10% 80px; /* Top padding to clear fixed header */
            text-align: center;
        }

        .services-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            color: #3b2f2f;
            margin-bottom: 5px;
            height: 50px;
        }

        .services-title h2 {
            font-family: 'Parisienne', cursive;
            font-size: 2.5rem;
            color: #536480; /* A soft blue/grey for contrast */
            margin-top: 0px;
            margin-bottom: 60px;
        }

        .services-grid {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-column {
            flex: 1 1 30%; /* Allows columns to grow/shrink but not too much */
            display: flex;
            flex-direction: column;
            gap: 60px;
            text-align: left;
        }

        .service-center-image {
            flex: 0 0 auto;
            width: 350px;
            height: 500px;
            background: url('images/newcoffee.jpg') center/cover no-repeat; /* Placeholder Image */
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 40px; /* Align slightly lower than text columns */
        }
        
        .service-item {
            position: relative;
            padding: 0 15px;
        }

        .service-item h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #3b2f2f;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .service-item p {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            line-height: 1.6;
            color: #5a4c43;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0d7c4; /* Light separator line */
        }

        .service-number {
            font-family: 'Playfair Display', serif;
            font-size: 5rem;
            color: #e0d7c4; /* Soft, subtle background number */
            position: absolute;
            top: -25px;
            left: 0;
            opacity: 0.7;
            z-index: -1;
            line-height: 1;
        }

        /* Footer styling (copied from your index.php) */
        footer { background: #f2ece6; text-align: center; padding: 40px; font-size: 0.9rem; color: #7b6d5b; margin-top: 50px; }

   
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translate(-50%, 0); }
  40% { transform: translate(-50%, -15px); }
  60% { transform: translate(-50%, -7px); }
}

</style>

<div class="image-stack">
  <div class="text-overlay">
    <h2>coffee</h2>
    <h1>LIFE</h1>
  </div>

  <img src="images/coffee5.jpg" class="img1" alt="Shell">
  <img src="images/cheesecake.jpg" class="img2" alt="Woman">
  <img src="images/croissant.jpg" class="img3" alt="Sailboat">
</div>

<script>
window.addEventListener('load', () => {
  const images = document.querySelectorAll('.image-stack img');
  let index = 0;

  function showNextImage() {
    images.forEach(img => img.classList.remove('visible')); // hide all
    images[index].classList.add('visible'); // show one
    index = (index + 1) % images.length; // next image
  }

  showNextImage(); // show first
  setInterval(showNextImage, 2500); // repeat every 2.5s
});
</script>



  </style>
</head>

<body unresolved>


 <!-- Header -->
<header style="display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-family: Garamond, serif;">
  <div style="display: flex; align-items: center; gap: 15px;">
    <h4 style="margin: 0;">Arjuna n Co-ffee</h4>

    <!-- Three-dot Menu -->
    <div class="menu" style="position: relative;">
      <button class="menu-btn" onclick="toggleMenu()" style="background: none; border: none; font-size: 22px; cursor: pointer;">⋮</button>
      <div class="menu-list" id="menuList" style="display: none; position: absolute; right: 0; top: 30px; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
        <a href="profile.php" style="display: block; padding: 10px 20px; text-decoration: none; color: #333;">Profile</a>
        <a href="settings.php" style="display: block; padding: 10px 20px; text-decoration: none; color: #333;">Settings</a>
        <a href="logout.php" style="display: block; padding: 10px 20px; text-decoration: none; color: #333;">Logout</a>
      </div>
    </div>
  </div>

  <nav style="display: flex; gap: 20px;">
    <a href="index.php">Home</a>
    <a href="menu.php">Menu</a>
    <a href="about.php">About</a>
    <a href="contact_us.php">Contact</a>
    <?php if (isset($_SESSION['role'])): ?>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </nav>
</header>

<script>
function toggleMenu() {
  const menu = document.getElementById("menuList");
  menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

window.onclick = function(e) {
  if (!e.target.matches('.menu-btn')) {
    document.getElementById("menuList").style.display = "none";
  }
}
</script>


  <section class="dreamy-section">
  <div class="dreamy-container">
    <!-- Left side image -->
    <div class="dreamy-image"></div>

    <!-- Right side text -->
    <div class="dreamy-content">
      <h1 class="dreamy-title">Arjuna Days</h1>
      <p class="dreamy-subtitle">coffee ways</p>

      <h3 class="dreamy-heading">
        THIS SPACE IS FOR YOU — THE COFFEE LOVER, THE DREAM CHASER, 
        THE SOUL WHO KNOWS THERE’S MORE TO LIFE THAN RUSH
      </h3>

      <p class="dreamy-paragraph">
        At Arjuna n Co-ffee, every cup tells a story of calm mornings and warm smiles. 
        Inspired by the art of slow living, we blend passion and peace — where 
        every sip feels like sunshine, and every aroma feels like home.
      </p>

      <p class="dreamy-paragraph">
        We invite you to pause, breathe, and savor. Whether you're seeking comfort, 
        connection, or creativity, this is your space. Your table. Your moment.
      </p>

      <div class="dreamy-button">
        <a href="menu.php">Explore Our Brews</a>
      </div>
    </div>
  </div>
</section>

<!-- Sanctuary Section (Third Section) -->
<section class="sanctuary-hero">
  <div class="sanctuary-content">
    <h1>Discover</h1>
    <h2>Your Ideal Brew</h2>
    <p>Espresso Yourself</p>
  </div>
  <div class="scroll-indicator">&darr;</div>
</section>

<section class="sanctuary-text">
  <div class="overlay">
    <h2>A Space to Unwind</h2>
    <p>
      Step away from the rush. Our Sanctuary is designed as an extension of the calm you seek in every cup.
      A moment of clarity, a breath of fresh air, and a gentle reminder that some things are meant to be savored slowly.
    </p>
  </div>
</section>

<!-- Section 4: Services -->

    <section class="services-section">
        <div class="services-title">
            <h2>My Signature</h2>
            <h1>Services</h1>
        </div>

        <div class="services-grid">
            
            <div class="service-column">
                <div class="service-item">
                    <span class="service-number">01</span>
                    <h3>Scan QR or Access Site</h3>
                    <p>Use the QR code at your table or visit our website to start your order.</p>
                </div>
                
                <div class="service-item">
                    <span class="service-number">02</span>
                    <h3>Place Your Order</h3>
                    <p>Select your favorite coffee and desserts, customize, and add them to your cart.</p>
                </div>
            </div>

            <div class="service-center-image">
                </div>

            <div class="service-column">
                <div class="service-item">
                    <span class="service-number">03</span>
                    <h3>Enjoy Your Coffee</h3>
                    <p>Wait for your name to be called or pick up your order — freshly brewed for you.</p>
                </div>

                <div class="service-item">
                    <span class="service-number">04</span>
                    <h3>Share the Joy</h3>
                    <p>Snap, sip, and share your cozy café moments with #ArjunaNCo.</p>
                </div>
            </div>
        </div>
    </section>

<section class="coffee-showcase">
  <h2 class="coffee-title">Arjuna <span class="italic-word">Moments</span></h2>

  <!-- Animated background text -->
  <div class="coffee-bg-text">
    Sip . Relax . Enjoy . Sip . Relax . Enjoy . Sip . Relax . Enjoy . Sip . Relax . Enjoy .
  </div>

  <!-- Center image -->
  <div class="coffee-content">
    <img src="images/coffeemodel.jpg" alt="Coffee Time" class="coffee-img">
  </div>

  <!-- Buttons -->
  <div class="coffee-buttons">
    <a href="contact_us.php" class="coffee-btn">Give Feedback</a>
    <a href="menu.php" class="coffee-btn">View Menu</a>
    <a href="index.php" class="coffee-btn">About Us</a>
  </div>
</section>

<footer>
  &copy; 2025 Arjuna n Co-ffee. All Rights Reserved.
</footer>

<script>
  // fade in on load
  window.addEventListener('DOMContentLoaded', () => {
    document.body.removeAttribute('unresolved');
  });
</script>


</body>
</html>
