<?php
// Ensure session is started at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Arjuna n Co-ffee</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Parisienne&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* --- General Reset & Variables (From About.php) --- */
        :root {
            --color-background: #f5f3f0;
            --color-text-dark: #3a3a3a;
            --font-serif: 'Cormorant Garamond', serif;
            --font-sans: 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: var(--color-background);
            color: var(--color-text-dark);
            font-family: var(--font-sans);
            margin: 0;
            padding: 0;
            /* Flex layout for centering content, adjusted to allow header to be fixed */
            display: flex;
            flex-direction: column; 
            align-items: center;
            /* Ensure content starts below the fixed header */
            padding-top: 80px; /* Adjust based on your header's actual height + padding */
            min-height: 100vh;
        }
        
        /* --- Navbar Styles (From navbar.php) --- */
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Added from inline style */
        }

        header h4 {
            font-family: 'Parisienne', cursive !important; /* Use !important to override about.php's general font if necessary */
            font-size: 1.8rem;
            color: #3d2b1f;
            margin: 0; /* From inline style */
        }

        header nav a {
            text-decoration: none;
            color: #3d2b1f;
            margin: 0 15px;
            font-family: 'Cormorant Garamond', serif !important; /* Use !important */
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            transition: color 0.3s ease;
        }

        header nav a:hover {
            color: #caa472;
        }
        
        /* Three-dot menu styles */
        .menu-list {
            position: absolute;
            right: 0;
            top: 35px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            display: none;
            min-width: 120px;
        }

        .menu-list.show {
            display: block;
        }

        .menu-list a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
            transition: background 0.3s;
            font-family: 'Cormorant Garamond', serif;
            margin: 0; /* Resetting the nav a margin */
        }

        .menu-list a:hover {
            background: #f9f4ef;
        }
        
        /* Inline styles moved to CSS */
        .menu-btn {
            background: none; 
            border: none; 
            font-size: 22px; 
            cursor: pointer; 
            color: #3d2b1f;
        }
        .header-logo-container {
            display: flex; 
            align-items: center; 
            gap: 15px;
        }
        
        /* --- About Page Content Styles (From about.php) --- */
        
        /* --- Main Layout: Grid System --- */
        .marketing-hero {
            display: grid;
            grid-template-columns: 1.5fr 2fr 2fr; 
            max-width: 1300px; 
            width: 90%;
            padding: 50px 0;
        }

        .text-block {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .text-block-left {
            align-items: flex-end;
            text-align: right;
            justify-content: center; 
            margin-top: -300px; 
            z-index: 3;
            position: relative;
        }


        .text-block-right {
            align-items: flex-start;
            text-align: left;
            justify-content: flex-start;
        }

        /* --- Typography (Fonts & Sizes) --- */
        .heading-large {
            font-family: var(--font-serif);
            font-weight: 300;
            font-size: 5vw; 
            line-height: 1.0;
            margin: 0;
            padding-top: 15vh;
        }

        .heading-medium {
            font-family: var(--font-serif);
            font-weight: 400;
            font-size: 2.5vw; 
            line-height: 1.2;
            margin: 0 0 20px 0;
            padding-top: 10vh;
        }

        .body-text {
            font-family: var(--font-serif);
            font-weight: 300;
            font-size: 0.95em;
            line-height: 1.6;
            max-width: 400px;
            margin-bottom: 40px;
        }

        /* --- Image Styling & Overlap --- */
        .image-section {
            position: relative;
        }

        .image-main {
            display: block;
            width: 100%;
            max-height: 80vh; 
            object-fit: cover;
        }

        /* --- Animated Image Container --- */
        .image-walking-container {
            position: absolute;
            top: 25%; 
            left: -20%;
            width: 45%; 
            height: 50vh; 
            overflow: hidden; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); 
        }

        .walking-animation-frame {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            animation: walk-fade 16s infinite ease-in-out;
        }

        /* Smooth fading between images */
        @keyframes walk-fade {
            0% { background-image: url('images/boss2.jpg'); opacity: 1; }
            20% { opacity: 1; }
            25% { opacity: 0; background-image: url('images/boss.jpg'); }
            45% { opacity: 1; }
            50% { opacity: 0; background-image: url('images/boss3.jpg'); }
            70% { opacity: 1; }
            75% { opacity: 0; background-image: url('images/boss4.jpg'); }
            95% { opacity: 1; }
            100% { opacity: 0; background-image: url('images/boss1.jpg'); }
        }


        /* --- Button Style --- */
        .button-primary {
            display: inline-block;
            padding: 12px 30px;
            border: 1px solid var(--color-text-dark);
            text-decoration: none;
            font-family: var(--font-sans);
            font-size: 0.8em;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--color-text-dark);
            transition: background-color 0.3s, color 0.3s;
        }

        .button-primary:hover {
            background-color: var(--color-text-dark);
            color: var(--color-background);
        }

        /* --- Responsiveness (Combined) --- */
        @media (max-width: 1024px) {
            .marketing-hero {
                grid-template-columns: 1fr;
            }
            
            .heading-large, .heading-medium {
                font-size: 6vw;
                padding-top: 0;
            }
            
            .text-block-left, .text-block-right {
                align-items: flex-start;
                text-align: left;
            }
            
            .text-block-left {
                order: -3;
                margin-top: -50px; 
            }

            .text-block-right {
                order: 3;
            }

            .image-section {
                order: 2;
                margin: 50px 0;
                padding: 0 20%;
            }
            
            .image-walking-container {
                top: -10%; 
                left: 5%;
                width: 40%;
                height: 40vh;
            }
        }
        /* Inside your <style> block: */

header h4 {
    /* Use !important to override any general body or container fonts */
    font-family: 'Parisienne', cursive !important; 
    font-size: 1.8rem;
    color: #3d2b1f;
    margin: 0; 
}

header nav a {
    /* Apply !important here too, just in case, for navigation links */
    font-family: 'Cormorant Garamond', serif !important;
    text-decoration: none;
    color: #3d2b1f;
    margin: 0 15px;
    font-size: 1.05rem;
    letter-spacing: 0.5px;
    transition: color 0.3s ease;
}
        /* Navbar Responsiveness */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 15px 30px;
            }
            header nav {
                margin-top: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            header nav a {
                margin: 5px 8px;
                font-size: 1rem;
            }
            body {
                padding-top: 120px; /* More padding needed for wrapped header */
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-logo-container">
        <h4 style="margin: 0;">Arjuna n Co-ffee</h4>

        <?php if (isset($_SESSION['role'])): ?>
        <div class="menu" style="position: relative;">
            <button class="menu-btn" onclick="toggleMenu()">⋮</button>
            <div class="menu-list" id="menuList">
                <a href="profile.php">Profile</a>
                <a href="settings.php">Settings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <nav>
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="about.php">About</a>
        <a href="contact_us.php">Contact</a>

        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'Customer'): ?>
                <a href="customer_orders.php">My Orders</a>
            <?php elseif ($_SESSION['role'] === 'Staff'): ?>
                <a href="staff_dashboard.php">Dashboard</a>
            <?php elseif ($_SESSION['role'] === 'Admin'): ?>
                <a href="admin_dashboard.php">Admin Panel</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<div class="marketing-hero">
    
    <div class="text-block text-block-left">
        <h1 class="heading-large">Meet the <br> founder</h1>
    </div>

    <div class="image-section">
        <div class="image-main-container">
            <img src="images/boss2.jpg" alt="Founder looking thoughtfully" class="image-main">
        </div>
        
        <div class="image-walking-container">
            <div class="walking-animation-frame"></div>
        </div>
    </div>

    <div class="text-block text-block-right">
        <h2 class="heading-medium">Brewing Connections
<br>That Inspire <br> Every Sip</h2>
        <p class="body-text">
            At Arjuna n Co-ffee, we believe a cup of coffee is more than a drink — it’s a story of passion, craft, and connection.
            Behind every blend is Arjuna, our founder, whose vision is to create moments that comfort, energize, and bring people together — one intentional brew at a time.
        </p>
        <a href="contact_us.php" class="button-primary">CONTACT US</a>
    </div>

</div>
<script>
// Navbar Script for the three-dot menu
function toggleMenu() {
    const menu = document.getElementById("menuList");
    menu.classList.toggle("show");
}

window.addEventListener('click', function(e) {
    // Check if the click is outside the .menu container
    if (!e.target.closest('.menu')) {
        const menu = document.getElementById("menuList");
        // Only try to remove 'show' if the menu exists
        if (menu && menu.classList.contains("show")) {
            menu.classList.remove("show");
        }
    }
});
</script>

</body>
</html>