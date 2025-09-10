<<<<<<< HEAD
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

            $reset_link = "http://localhost/arjunababy/reset_password.php?token=$token";

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
<title>Forgot Password | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<style>
body, html { height:100%; margin:0; padding:0; }
.payment-bg-blur {
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1;
}
.payment-container {
  background-color: rgba(255,255,255,0.95);
  padding: 40px;
  border-radius: 12px;
  max-width: 600px;
  margin: 60px auto;
  box-shadow: 0 0 20px rgba(0,0,0,0.2);
}
.section-title { text-align:center; margin-bottom:1.5rem; font-size:2rem; color:#333; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="payment-bg-blur"></div>

<div class="payment-container">
<h2 class="section-title">Forgot Your Password?</h2>

<?php if($message) echo "<div class='alert alert-info text-center'>$message</div>"; ?>

<form method="POST">
  <div class="mb-3">
    <label for="email" class="form-label">Enter your registered email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
</form>

<div class="text-center mt-3">
  <a href="login.php" class="btn btn-outline-secondary">← Back to Login</a>
</div>
</div>
</body>
</html>
=======
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
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        // Generate token & expiration
        $token = bin2hex(random_bytes(50));
       $expires = date("Y-m-d H:i:s", strtotime("+1 day"));


        // Save to DB
        $stmt2 = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
        $stmt2->bind_param("ssi", $token, $expires, $user['id']);
        $stmt2->execute();

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

            $reset_link = "http://localhost/arjuna1/reset_password.php?token=$token";

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
<title>Forgot Password | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<style>
body, html { height:100%; margin:0; padding:0; }
.payment-bg-blur {
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1;
}
.payment-container {
  background-color: rgba(255,255,255,0.95);
  padding: 40px;
  border-radius: 12px;
  max-width: 600px;
  margin: 60px auto;
  box-shadow: 0 0 20px rgba(0,0,0,0.2);
}
.section-title { text-align:center; margin-bottom:1.5rem; font-size:2rem; color:#333; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="payment-bg-blur"></div>

<div class="payment-container">
<h2 class="section-title">Forgot Your Password?</h2>

<?php if($message) echo "<div class='alert alert-info text-center'>$message</div>"; ?>

<form method="POST">
  <div class="mb-3">
    <label for="email" class="form-label">Enter your registered email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
</form>

<div class="text-center mt-3">
  <a href="login.php" class="btn btn-outline-secondary">← Back to Login</a>
</div>
</div>
</body>
</html>
>>>>>>> 9c3cfdedaaf306ac261286e46793cbcf989f68c9
