<?php
session_start();
include 'db.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id, name FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        // Generate token & expiration
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime("+1 day"));

        // Update user with reset token
        $stmt2 = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE user_id=?");
        if (!$stmt2) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt2->bind_param("ssi", $token, $expires, $user['user_id']);
        $stmt2->execute();
        $stmt2->close();

        // Prepare email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'peilingbts@gmail.com'; // your Gmail
            $mail->Password = 'tysk vvax tfof xpfp'; // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('yourgmail@gmail.com', 'Arjuna n Co-ffee');
            $mail->addAddress($email, $user['name']);

           $reset_link = "https://arjunacoffee.infinityfreeapp.com/reset_password.php?token=" . $token;


            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body = "Hi {$user['name']},<br><br>
                Click the link below to reset your password:<br>
                <a href='$reset_link'>Reset Password</a><br><br>
                Link expires in 1 hour.";

            $mail->send();
            $message = "✅ A reset link has been sent to your email.";
        } catch (Exception $e) {
            $message = "❌ Could not send email. Error: {$mail->ErrorInfo}";
        }

    } else {
        $message = "❌ Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password | Arjuna n Co-ffee</title>
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


.login-header {
    padding: 40px 20px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.login-header h1 {
    font-family: 'Playfair Display', serif;
    font-weight: 400;
    font-size: 2.5rem;
    letter-spacing: 1px;
    color: #333;
}

.login-content {
    max-width: 500px;
    margin: 60px auto; 
    padding: 40px 20px;
}


.login-card {
    background: white;
    padding: 40px 30px;
    margin-bottom: 40px;
    position: relative;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    border-radius: 4px;
}


.section-header {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 400;
    margin-bottom: 15px;
    color: #333;
    text-align: center;
}

.section-description {
    text-align: center;
    color: #777;
    margin-bottom: 30px;
    font-style: italic;
    font-size: 1.1rem;
}


.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-label {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
    display: block;
    font-size: 1.1rem;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid rgba(0,0,0,0.1);
    background: white;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.1rem;
    color: #333;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #333;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.95rem;
    margin-top: 5px;
}


.login-actions {
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
    opacity: 0.9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}


.login-footer {
    text-align: center;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid rgba(0,0,0,0.05);
    color: #777;
    font-size: 0.9rem;
}

.login-footer a {
    color: #777;
    text-decoration: none;
}

.login-footer a:hover {
    text-decoration: underline;
}


.alert {
    padding: 15px 20px;
    margin: 20px 0;
    border-radius: 0;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.1rem;
    border-left: 3px solid;
}

.alert-info {
    background-color: rgba(0, 123, 255, 0.1);
    color: #004085;
    border-left-color: #007bff;
}

.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #721c24;
    border-left-color: #dc3545;
}

.alert-success {
    background-color: rgba(40, 167, 69, 0.1);
    color: #155724;
    border-left-color: #28a745;
}


.back-to-login {
    text-align: center;
    margin-top: 25px;
}

.back-to-login a {
    color: #777;
    text-decoration: none;
    font-size: 1rem;
}

.back-to-login a:hover {
    text-decoration: underline;
    color: #333;
}


.decorative-line {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
    margin: 25px 0;
}


.success-message {
    text-align: center;
    margin: 20px 0;
    padding: 20px;
    background-color: rgba(40, 167, 69, 0.1);
    border-left: 3px solid #28a745;
}

.success-message i {
    color: #28a745;
    margin-right: 10px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .login-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 250px;
    }
    
    .login-header h1 {
        font-size: 2rem;
    }
    
    .section-header {
        font-size: 1.5rem;
    }
    
    .login-content {
        margin: 40px auto; /* Adjusted for mobile */
    }
}

/* Animation for form submission */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="login-content">
    <div class="login-card">
        <h2 class="section-header">Reset Your Password</h2>
        <p class="section-description">Enter your email to receive a password reset link</p>

        <form method="POST" id="forgotPasswordForm" autocomplete="off">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" 
                       class="form-control" 
                       placeholder="your.email@example.com"
                       autocomplete="email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                       required />
            </div>

            <div class="decorative-line"></div>

            <div class="login-actions">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                </button>
                <a href="login.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left me-2"></i>Back to Login
                </a>
            </div>

            <!-- Success/Error messages -->
            <?php if($message): ?>
                <div class="alert <?php echo strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
        </form>

        <div class="back-to-login">
            <p>Remember your password? <a href="login.php">Login here</a></p>
        </div>
    </div>
    
    <div class="login-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | Secure Password Reset</p>
    </div>
</div>

<script>
// Add interactive feedback for form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;
        
        // Re-enable button after 5 seconds in case of error
        setTimeout(function() {
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Reset Link';
            submitBtn.disabled = false;
        }, 5000);
    });
    
    // Add focus effect to form inputs
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
    });
});
</script>

</body>
</html>