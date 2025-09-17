<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

$token = trim($_GET['token'] ?? '');
$errors = [];
$success = '';

if (!$token) die("Invalid reset link.");

// Validate token and expiration
$stmt = $conn->prepare("SELECT user_id, reset_expires FROM users WHERE reset_token=? LIMIT 1");
if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) die("Reset link is invalid.");
if ($user['reset_expires'] < date("Y-m-d H:i:s")) die("Reset link has expired.");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validation logic
    if (empty($password) && empty($confirm_password)) {
        $errors[] = "New password and confirmation are required.";
    } elseif (empty($password)) {
        $errors[] = "Please enter a new password.";
    } elseif (empty($confirm_password)) {
        $errors[] = "Please confirm your new password.";
    } else {
        $password_valid = strlen($password) >= 8; // Example criteria: min 8 chars
        $confirm_valid = ($password === $confirm_password);

        if (!$password_valid && !$confirm_valid) {
            $errors[] = "New password is invalid and confirmation does not match. Please check both fields.";
        } elseif (!$password_valid) {
            $errors[] = "New password does not meet the required criteria.";
        } elseif (!$confirm_valid) {
            $errors[] = "Confirm password does not match. Please re-enter.";
        } else {
            // Update password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE user_id=?");
            $stmt2->bind_param("si", $hashed, $user['user_id']);
            $stmt2->execute();

            if ($stmt2->affected_rows === 0) {
                $errors[] = "Password update failed. Please try again.";
            } else {
                $success = "✅ Password reset successful! You can now <a href='login.php'>login</a>.";
            }

            $stmt2->close();
        }
    }
}
?>

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

.btn-coffee {
  color: #3e2723;
  background-color: transparent;
  border: 2px solid #3e2723;
  border-radius: 30px;
  padding: 10px 20px;
  font-weight: 500;
  transition: all 0.3s ease;
}
.btn-coffee:hover {
  background-color: #3e2723;
  color: #fff;
}
.section-title { text-align:center; margin-bottom:1.5rem; font-size:2rem; color:#333; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="payment-bg-blur"></div>

<div class="payment-container">
  <h2 class="section-title">Reset Your Password</h2>

  <?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
  <?php endif; ?>

  <?php if($success): ?>
    <div class="alert alert-success text-center"><?php echo $success; ?></div>
  <?php else: ?>
    <form method="POST">
      <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="password" name="password">
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
      </div>
      <div class="d-grid mt-3">
        <button type="submit" class="btn btn-coffee w-100">Reset Password</button>
      </div>
    </form>
  <?php endif; ?>

  <div class="text-center mt-3">
    <a href="login.php" class="btn btn-outline-secondary">← Back to Login</a>
  </div>
</div>

</body>
</html>
