<?php
session_start();
include 'db.php'; // your database connection

require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';
require __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validations
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered.";
        } else {
            // Hash password and generate verification token
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(50));

            // Insert user into DB
            $insert = $conn->prepare("INSERT INTO users (name, email, password, role, verification_token) VALUES (?, ?, ?, 'customer', ?)");
            $insert->bind_param("ssss", $name, $email, $hashed_password, $token);

            if ($insert->execute()) {
                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    // SMTP settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'peilingbts@gmail.com'; // YOUR email
                    $mail->Password   = 'zyse rhvs wlix nihw';   // Gmail App Password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('youremail@gmail.com', 'Arjuna n Co-ffee');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Arjuna n Co-ffee Account';
                    $verification_link = "http://localhost/arjuna1/verify.php?token=".$token;
                    $mail->Body    = "Hi $name,<br><br>Thank you for signing up! Please verify your account by clicking the link below:<br><a href='$verification_link'>Verify Account</a><br><br>Cheers,<br>Arjuna n Co-ffee Team";

                    $mail->send();
                    $success = "✅ Account created! Please check your email to verify your account.";
                } catch (Exception $e) {
                    $errors[] = "Account created, but verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    body, html { height: 100%; margin: 0; font-family: 'Segoe UI', sans-serif; }
    .signup-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px);
      position: fixed; top: 0; left: 0; height: 100%; width: 100%; z-index: -1;
    }
    .form-container { background-color: rgba(255,255,255,0.95); padding: 30px; border-radius: 12px; box-shadow: 0 0 20px rgba(0,0,0,0.2);}
    .section-title { font-size: 2rem; font-weight: bold; text-align: center; margin-bottom: 1.5rem; color: #333;}
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="signup-bg-blur"></div>

<section class="py-5">
  <div class="container" style="max-width: 500px;">
    <div class="form-container">
      <h2 class="section-title">Create an Account</h2>

      <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach($errors as $error) echo $error."<br>"; ?>
        </div>
      <?php endif; ?>

      <?php if($success): ?>
        <div class="alert alert-success">
          <?php echo $success; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" placeholder="Your name" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="example@email.com" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required />
        </div>
        <button type="submit" class="btn btn-secondary w-100">Sign Up</button>
      </form>

      <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</section>
</body>
</html>
