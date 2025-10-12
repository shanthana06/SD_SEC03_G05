<?php
// Start session and destroy it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
$_SESSION = [];
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logout Successful | Arjuna n Co-ffee</title>
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

    /* Header styling */
    .logout-header {
        padding: 40px 20px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .logout-header h1 {
        font-family: 'Playfair Display', serif;
        font-weight: 400;
        font-size: 2.5rem;
        letter-spacing: 1px;
        color: #333;
    }

    /* Main content area */
    .logout-content {
        max-width: 500px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* Logout card */
    .logout-card {
        background: white;
        padding: 50px 30px;
        margin-bottom: 40px;
        position: relative;
        text-align: center;
    }

    /* Success icon */
    .success-icon {
        font-size: 4rem;
        color: #28a745;
        margin-bottom: 25px;
    }

    /* Section headers */
    .section-header {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        font-weight: 400;
        margin-bottom: 15px;
        color: #333;
    }

    .section-description {
        text-align: center;
        color: #777;
        margin-bottom: 30px;
        font-style: italic;
        font-size: 1.1rem;
    }

    /* Action buttons */
    .logout-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
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

    /* Footer */
    .logout-footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid rgba(0,0,0,0.05);
        color: #777;
        font-size: 0.9rem;
    }

    .logout-footer a {
        color: #777;
        text-decoration: none;
    }

    .logout-footer a:hover {
        text-decoration: underline;
    }

    /* Coffee animation */
    .coffee-steam {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 30px 0;
    }

    .steam {
        width: 8px;
        height: 40px;
        background: linear-gradient(to top, transparent, rgba(0,0,0,0.1), transparent);
        animation: steam 3s infinite ease-in-out;
        border-radius: 4px;
    }

    .steam:nth-child(2) {
        animation-delay: 0.5s;
    }

    .steam:nth-child(3) {
        animation-delay: 1s;
    }

    @keyframes steam {
        0%, 100% {
            transform: translateY(0) scale(1);
            opacity: 0.5;
        }
        50% {
            transform: translateY(-20px) scale(1.1);
            opacity: 0.8;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .logout-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .btn {
            width: 100%;
            max-width: 250px;
        }
        
        .logout-header h1 {
            font-size: 2rem;
        }
        
        .section-header {
            font-size: 1.5rem;
        }
        
        .success-icon {
            font-size: 3rem;
        }
    }

    /* Decorative elements */
    .decorative-line {
        height: 1px;
        background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
        margin: 30px 0;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="logout-header">
    <h1>Until Next Time</h1>
</div>

<div class="logout-content">
    <div class="logout-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h2 class="section-header">Successfully Logged Out</h2>
        <p class="section-description">Thank you for visiting Arjuna n Co-ffee</p>

        <!-- Coffee steam animation -->
        <div class="coffee-steam">
            <div class="steam"></div>
            <div class="steam"></div>
            <div class="steam"></div>
        </div>

        <p class="text-center mb-4" style="color: #777; font-size: 1.1rem;">
            We hope you enjoyed your coffee experience with us.<br>
            Come back soon for another delightful cup!
        </p>

        <div class="decorative-line"></div>

        <div class="logout-actions">
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Login Again
            </a>
            <a href="index.php" class="btn btn-outline">
                <i class="fas fa-home me-2"></i>Return to Home
            </a>
        </div>
    </div>
    
    <div class="logout-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | See you soon for your next coffee break!</p>
    </div>
</div>

<script>
// Add a simple fade-in animation
document.addEventListener('DOMContentLoaded', function() {
    const logoutCard = document.querySelector('.logout-card');
    logoutCard.style.opacity = '0';
    logoutCard.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        logoutCard.style.transition = 'all 0.6s ease';
        logoutCard.style.opacity = '1';
        logoutCard.style.transform = 'translateY(0)';
    }, 100);
});
</script>

</body>
</html>