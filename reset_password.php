<?php
session_start();
include 'db.php';

$token = $_GET['token'] ?? '';
$errors = [];
$success = '';

if (!$token) die("Invalid reset link.");

$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token=? AND reset_expires > NOW() LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) die("Reset link is invalid or expired.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password) || empty($confirm_password)) $errors[] = "All fields are required.";
    elseif ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
        $stmt2->bind_param("si", $hashed, $user['id']);
        $stmt2->execute();
        $success = "✅ Password reset successful! You can now <a href='login.php'>login</a>.";
    }
}
?>

<!-- Use same HTML styles as forgot_password.php -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password | Arjuna n Co-ffee</title>
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

<div id="navbar-placeholder"></div>
<script>
fetch('navbar.html').then(res => res.text()).then(data => {
  document.getElementById('navbar-placeholder').innerHTML = data;
});
</script>

<div class="payment-bg-blur"></div>

<div class="payment-container">
<h2 class="section-title">Reset Your Password</h2>

<?php if(!empty($errors)): ?>
  <div class="alert alert-danger"><?php foreach($errors as $e) echo $e."<br>"; ?></div>
<?php endif; ?>

<?php if($success): ?>
  <div class="alert alert-success text-center"><?php echo $success; ?></div>
<?php else: ?>
  <form method="POST">
    <div class="mb-3">
      <label for="password" class="form-label">New Password</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
      <label for="confirm_password" class="form-label">Confirm Password</label>
      <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
  </form>
<?php endif; ?>

<div class="text-center mt-3">
  <a href="login.php" class="btn btn-outline-secondary">← Back to Login</a>
</div>
</div>
</body>
</html>
