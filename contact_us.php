<?php
session_start();
include 'db.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $message_text = trim($_POST['message']);

    if(empty($fullname) || empty($email) || empty($message_text)){
        $errors[] = "All fields are required.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Invalid email format.";
    } else {
        // Insert into DB
       $stmt = $conn->prepare("INSERT INTO contacts (fullname, email, message) VALUES (?, ?, ?)");

        $stmt->bind_param("sss", $fullname, $email, $message_text);
        if($stmt->execute()){
            $success = " Your message has been sent successfully!";

          try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'peilingbts@gmail.com';   // your Gmail
    $mail->Password = 'kdhn gjkf vkgx bvqy';   // <-- Gmail App Password (not login password)
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // must match authenticated Gmail
    $mail->setFrom('peilingbts@gmail.com', 'Arjuna n Co-ffee');
    // send to yourself (or another test email)
    $mail->addAddress('peilingbts@gmail.com', 'Admin');

    $mail->isHTML(true);
    $mail->Subject = "New Contact Message from $fullname";
    $mail->Body    = "Name: $fullname<br>Email: $email<br>Message: $message_text";

    $mail->send();
} catch (Exception $e) {
    $errors[] = "Message saved but email not sent: {$mail->ErrorInfo}";
}

        }
    }
}
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

/* Header styling */
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

/* Main content area */
.contact-content {
    max-width: 700px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Contact card */
.contact-card {
    background: white;
    padding: 40px 30px;
    margin-bottom: 40px;
    position: relative;
}

/* Section headers */
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
    margin-bottom: 40px;
    font-style: italic;
    font-size: 1.1rem;
}

/* Form styling */
.form-group {
    margin-bottom: 25px;
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
}

textarea.form-control {
    resize: vertical;
    min-height: 150px;
}

/* Action buttons */
.contact-actions {
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

/* Alert styling - Moved to bottom */
.alert {
    padding: 15px 20px;
    margin: 30px 0;
    border-radius: 0;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.1rem;
}

.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #721c24;
    border-left: 3px solid #dc3545;
}

.alert-success {
    background-color: rgba(40, 167, 69, 0.1);
    color: #155724;
    border-left: 3px solid #28a745;
    margin-top: 40px;
    margin-bottom: 20px;
}

/* Contact info */
.contact-info {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.contact-item {
    text-align: center;
    color: #555;
}

.contact-item i {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #333;
    display: block;
}

.contact-item span {
    font-size: 0.95rem;
}

/* Success message container */
.success-container {
    margin-top: 40px;
    margin-bottom: 20px;
}

/* Responsive adjustments */
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
    
    .contact-info {
        flex-direction: column;
        gap: 25px;
    }
    
    .section-header {
        font-size: 1.5rem;
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


<div class="contact-content">
    <div class="contact-card">
        <h2 class="section-header">Get In Touch</h2>
        <p class="section-description">Have questions, suggestions, or feedback? We'd love to hear from you.</p>

        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $err) echo htmlspecialchars($err)."<br>"; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="form-control" placeholder="Your full name" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="your.email@example.com" required>
            </div>
            
            <div class="form-group">
                <label for="message" class="form-label">Message</label>
                <textarea name="message" id="message" class="form-control" rows="6" placeholder="Tell us what's on your mind..." required></textarea>
            </div>
            
            <div class="contact-actions">
                <button type="submit" class="btn btn-primary">Send Message</button>
                <a href="index.php" class="btn btn-outline">Return to Home</a>
            </div>
        </form>

        <!-- Success message at the bottom -->
        <?php if($success): ?>
            <div class="success-container">
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Contact Information -->
        <div class="decorative-line"></div>
        
        <div class="contact-info">
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>arjunacoffee@gmail.com</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <span>Mon - Fri: 9AM - 6PM</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-coffee"></i>
                <span>Arjuna n Co-ffee</span>
            </div>
        </div>
    </div>
    
    <div class="contact-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | We typically respond within 24 hours</p>
    </div>
</div>

</body>
</html>