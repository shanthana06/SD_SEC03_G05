<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$errors = [];
$successMessages = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $oldPassword = trim($_POST['old_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validation
    if ($oldPassword === "" || $newPassword === "" || $confirmPassword === "") {
        $errors[] = "All fields are required.";
    } elseif (!password_verify($oldPassword, $user['password'])) {
        $errors[] = "Old password is incorrect.";
    } elseif (strlen($newPassword) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    } elseif (!preg_match('/[0-9]/', $newPassword)) {
        $errors[] = "New password must contain at least one number.";
    } elseif (!preg_match('/[^a-zA-Z0-9]/', $newPassword)) {
        $errors[] = "New password must contain at least one special character.";
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = "New password and confirm password do not match.";
    } elseif ($newPassword === $oldPassword) {
        $errors[] = "New password cannot be the same as the old password.";
    } else {
        // ✅ Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $stmt->bind_param("si", $hashedPassword, $user_id);
        if ($stmt->execute()) {
            $successMessages[] = "✅ Password changed successfully!";
        } else {
            $errors[] = "Failed to update password. (" . $stmt->error . ")";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password - Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('images/coffee1.jpg') no-repeat center center fixed;
      background-size: cover;
      backdrop-filter: blur(6px);
    }
    .form-container {
      max-width: 450px;
      margin: 80px auto;
      background: rgba(255,255,255,0.9);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
  <div class="form-container text-center">
    <h2 class="mb-4">Change Password</h2>

    <!-- Errors -->
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $err) echo htmlspecialchars($err)."<br>"; ?>
      </div>
    <?php endif; ?>

    <!-- Success -->
    <?php if (!empty($successMessages)): ?>
      <div class="alert alert-success">
        <?php foreach($successMessages as $msg) echo htmlspecialchars($msg)."<br>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3 text-start">
        <label class="form-label">Old Password</label>
        <input type="password" name="old_password" class="form-control" required>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" required>
        <div class="form-text">
          Must be at least 8 characters with at least one number and one special character.
        </div>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-dark w-100">Change Password</button>
      <a href="profile.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
    </form>
  </div>
</div>
</body>
</html>
