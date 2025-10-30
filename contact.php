<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Parisienne&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body, html {
    height: 100%;
    font-family: 'Cormorant Garamond', serif;
    background-color: #fefefe;
    color: #333;
    line-height: 1.6;
}

.contact-header {
    padding: 40px 20px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.contact-header h1 {
    font-family: 'Playfair Display', serif;
    font-weight: 400;
    font-size: 2.5rem;
    letter-spacing: 1px;
    color: #333;
}

.contact-content {
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px 20px;
}

.contact-card {
    background: white;
    padding: 40px 30px;
    margin-bottom: 40px;
    position: relative;
}

.section-header {
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem;
    font-weight: 400;
    margin-bottom: 20px;
    color: #333;
    text-align: center;
}

.section-description {
    text-align: center;
    color: #777;
    margin-bottom: 60px;
    font-style: italic;
    font-size: 1.3rem;
    line-height: 1.8;
}

.contact-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 40px;
}

.btn {
    padding: 12px 30px;
    border-radius: 0;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.1rem;
    font-weight: 400;
    letter-spacing: 1px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid #333;
    background: transparent;
    color: #333;
    cursor: pointer;
    display: inline-block;
    text-align: center;
}

.btn-primary {
    background: #333;
    color: white;
}

.btn-outline {
    background: transparent;
    color: #333;
}

.btn:hover {
    opacity: 0.8;
    transform: translateY(-2px);
}

.contact-footer {
    text-align: center;
    margin-top: 60px;
    padding-top: 30px;
    border-top: 1px solid rgba(0,0,0,0.05);
    color: #777;
    font-size: 0.9rem;
}

.contact-footer a {
    color: #777;
    text-decoration: none;
}

.contact-footer a:hover {
    text-decoration: underline;
}

.contact-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
    margin-top: 40px;
    padding-top: 40px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.contact-detail-item {
    text-align: center;
    padding: 30px 20px;
    background: rgba(0,0,0,0.02);
    border-radius: 5px;
    transition: transform 0.3s ease;
}

.contact-detail-item:hover {
    transform: translateY(-5px);
}

.contact-detail-item i {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: #333;
    display: block;
}

.contact-detail-item h4 {
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
    margin-bottom: 20px;
    color: #333;
}

.contact-detail-item p {
    color: #555;
    margin-bottom: 8px;
    font-size: 1.1rem;
    line-height: 1.6;
}

.contact-info-section {
    text-align: center;
    margin: 50px 0 30px 0;
}

.contact-info-section h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: #333;
}

.contact-info-section p {
    color: #777;
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .contact-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 250px;
    }
    
    .contact-header h1 {
        font-size: 2rem;
    }
    
    .contact-details-grid {
        grid-template-columns: 1fr;
        gap: 25px;
    }
    
    .section-header {
        font-size: 1.8rem;
    }
    
    .section-description {
        font-size: 1.1rem;
    }
    
    .contact-detail-item {
        padding: 20px 15px;
    }
}

.decorative-line {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
    margin: 40px 0;
}

.contact-hero {
    text-align: center;
    padding: 40px 20px;
    margin-bottom: 40px;
}

.contact-hero h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    font-weight: 400;
    margin-bottom: 20px;
    color: #333;
}

.contact-hero p {
    font-size: 1.3rem;
    color: #777;
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.8;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="contact-content">
    <div class="contact-card">
        <!-- Hero Section -->
        <div class="contact-hero">
            <h2>Reach Out to Us</h2>
            <p>We are here to serve you!</p>
        </div>

        <!-- Contact Information -->
        <div class="contact-details-grid">
            <div class="contact-detail-item">
                <i class="fas fa-phone"></i>
                <h4>CALL US</h4>
                <p>+6011 6121 1888</p>
                <p>+6011 6188 9898</p>
                <p>+6011 3422 2311</p>
            </div>
            
            <div class="contact-detail-item">
                <i class="fas fa-map-marker-alt"></i>
                <h4>LOCATION</h4>
                <p>Lot 17, Jalan Sutera Merah 3, Taman Sutera Utama</p>
                <p> 81300 Skudai, Johor Bahru, Johor, Malaysia</p>
                
            </div>
            
            <div class="contact-detail-item">
                <i class="fas fa-envelope"></i>
                <h4>MAIL</h4>
                <p>arjunacoffeestaff@sample.com</p>
                <p>arjunacoffee@gmail.com</p>
            </div>
            
            <div class="contact-detail-item">
                <i class="fas fa-clock"></i>
                <h4>HOURS</h4>
                <p><strong>Mon – Fri</strong> …… 11 am – 8 pm</p>
                <p><strong>Sat, Sun</strong> …… 6 am – 8 pm</p>
            </div>
        </div>

        <!-- Additional Info Section -->
        <div class="contact-info-section">
            <h3>Visit Us Today</h3>
            <p>Experience the perfect blend of quality coffee and exceptional service at any of our locations. Our friendly staff is ready to welcome you and make your visit memorable.</p>
        </div>

        <div class="contact-actions">
            <a href="index.php" class="btn btn-primary">Return to Home</a>
            <a href="menu.php" class="btn btn-outline">View Our Menu</a>
        </div>
    </div>
    
    <div class="contact-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | We're here to serve you</p>
        
    </div>
</div>

</body>
</html>