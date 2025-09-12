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
            $success = "✅ Your message has been sent successfully!";

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
<title>Contact Us | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<style>
body, html { height: 100%; margin:0; padding:0; }
.contact-bg-blur {
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1;
}
.form-container {
  background-color: rgba(255,255,255,0.9);
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0,0,0,0.2);
  max-width: 600px;
  margin: auto;
}
.section-title { color:#333; font-size:2.2rem; text-align:center; margin-bottom:2rem; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="contact-bg-blur"></div>

<section class="py-5">
<div class="container">
  <div class="form-container">
    <h2 class="section-title">Contact Us</h2>
    <p class="text-center mb-4">Have questions, suggestions, or feedback? Reach out to us below.</p>

    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger"><?php foreach($errors as $e) echo $e."<br>"; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="fullname" class="form-control" placeholder="Your name" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea name="message" class="form-control" rows="5" placeholder="Type your message..." required></textarea>
      </div>
      <button type="submit" class="btn btn-secondary w-100">Send Message</button>
      <div class="text-center mt-4">
        <a href="index.html" class="btn btn-outline-dark me-2">Return to Home</a>
      </div>
    </form>

  </div>
</div>
</section>

</body>
</html>
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
            $success = "✅ Your message has been sent successfully!";

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
<title>Contact Us | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<style>
body, html { height: 100%; margin:0; padding:0; }
.contact-bg-blur {
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1;
}
.form-container {
  background-color: rgba(255,255,255,0.9);
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0,0,0,0.2);
  max-width: 600px;
  margin: auto;
}
.section-title { color:#333; font-size:2.2rem; text-align:center; margin-bottom:2rem; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="contact-bg-blur"></div>

<section class="py-5">
<div class="container">
  <div class="form-container">
    <h2 class="section-title">Contact Us</h2>
    <p class="text-center mb-4">Have questions, suggestions, or feedback? Reach out to us below.</p>

    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger"><?php foreach($errors as $e) echo $e."<br>"; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="fullname" class="form-control" placeholder="Your name" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea name="message" class="form-control" rows="5" placeholder="Type your message..." required></textarea>
      </div>
      <button type="submit" class="btn btn-secondary w-100">Send Message</button>
      <div class="text-center mt-4">
        <a href="index.html" class="btn btn-outline-dark me-2">Return to Home</a>
      </div>
    </form>

  </div>
</div>
</section>

</body>
</html>
