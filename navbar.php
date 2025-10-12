<?php
include 'db.php';
// navbar.php - START OF FILE

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Parisienne&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<header>
    <div class="brand-section">
        <h4>Arjuna n Co-ffee</h4>
        <?php if (isset($_SESSION['role'])): ?>
        <div class="menu" style="position: relative;">
            <button class="menu-btn" onclick="toggleMenu()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #3d2b1f;">⋮</button>
            <div class="menu-list" id="menuList">
                <a href="profile.php">Profile</a>
                <a href="settings.php">Settings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <nav class="center-nav">
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
        <?php endif; ?>
    </nav>

    <div class="icon-section">
        <a href="login.php" title="Login"><i class="fa-regular fa-user"></i></a>
        <a href="search.php" title="Search"><i class="fa-solid fa-magnifying-glass"></i></a>
        <a href="javascript:void(0)" onclick="openCartSidebar()" title="Cart"><i class="fa-solid fa-cart-shopping"></i></a>
    </div>
</header>

<style>
/* Header Styles */
header {
    position: fixed;
    width: 100%;
    top: 0;
    left: 0;
    background: #ffffff;
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 60px;
    z-index: 1000;
}

.brand-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

header h4 {
    font-family: 'Parisienne', cursive;
    font-size: 1.8rem;
    color: #3d2b1f;
    margin: 0;
}

.center-nav {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 25px;
}

.center-nav a {
    text-decoration: none;
    color: #3d2b1f;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.05rem;
    letter-spacing: 0.5px;
    transition: color 0.3s ease;
}

.center-nav a:hover {
    color: #caa472;
}

.icon-section {
    display: flex;
    align-items: center;
    gap: 20px;
}

.icon-section a {
    color: #3d2b1f;
    font-size: 1.3rem;
    transition: color 0.3s ease;
    cursor: pointer; 
}

.icon-section a:hover {
    color: #caa472;
}

.menu-list {
    position: absolute;
    right: 0;
    top: 35px;
    background: white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    display: none;
    z-index: 1001; 
}

.menu-list.show {
    display: block;
}

.menu-list a {
    display: block;
    padding: 10px 20px;
    text-decoration: none;
    color: #333;
    font-family: 'Cormorant Garamond', serif;
    transition: background 0.3s;
}

.menu-list a:hover {
    background: #f9f4ef;
}

/* Responsive */
@media (max-width: 1024px) {
    header {
        flex-direction: column;
        padding: 15px 30px;
    }
    .center-nav {
        position: static;
        transform: none;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 10px;
    }
    .icon-section {
        margin-top: 10px;
    }
}

/* Cart Sidebar */
.cart-sidebar {
    position: fixed;
    top: 0;
    right: -100%;
    width: 450px;
    height: 100%;
    background: #f7f7f7;
    box-shadow: -5px 0 15px rgba(0,0,0,0.2);
    z-index: 2000;
    transition: right 0.4s ease;
}

.cart-sidebar iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.cart-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 1500;
}
</style>


<div id="cartSidebar" class="cart-sidebar">
    <iframe src="cart.php" id="cartIframe" frameborder="0"></iframe>
</div>
<div id="cartOverlay" class="cart-overlay"></div>

<script>
const cartSidebar = document.getElementById('cartSidebar');
const cartOverlay = document.getElementById('cartOverlay');
const cartIframe = document.getElementById('cartIframe');

function openCartSidebar() {
    // FIX: Always refresh the cart when opening
    cartIframe.src = 'cart.php?refresh=' + new Date().getTime();
    cartSidebar.style.right = '0';
    cartOverlay.style.display = 'block';
}

function closeCartSidebar() {
    cartSidebar.style.right = '-100%';
    cartOverlay.style.display = 'none';
}

// FIX: Listen for cart updates from menu page
window.addEventListener("message", function(event) {
    if (event.data === "close-cart") {
        closeCartSidebar();
    }
    if (event.data === "refresh-cart") {
        // Refresh cart when items are added
        cartIframe.src = 'cart.php?refresh=' + new Date().getTime();
    }
});

// FIX: Also listen for storage events (when items are added via AJAX)
window.addEventListener('storage', function(event) {
    if (event.key === 'cartUpdated') {
        cartIframe.src = 'cart.php?refresh=' + new Date().getTime();
    }
});

cartOverlay.addEventListener('click', closeCartSidebar);

function toggleMenu() {
    const menu = document.getElementById("menuList");
    menu.classList.toggle("show");
}

window.addEventListener('click', function(e) {
    if (!e.target.closest('.menu')) {
        const menu = document.getElementById("menuList");
        if (menu) menu.classList.remove("show");
    }
});

// FIX: Global function to refresh cart from any page
function refreshCart() {
    if (cartIframe) {
        cartIframe.src = 'cart.php?refresh=' + new Date().getTime();
    }
}
</script>