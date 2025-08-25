<?php
session_start();
include 'db.php'; // your database connection

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role_selected = $_POST['role']; // 'customer', 'staff', 'admin'

    if (empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, role, is_verified FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            $errors[] = "Email not found. Please sign up.";
        } elseif (!password_verify($password, $user['password'])) {
            $errors[] = "Incorrect password.";
        } elseif ($user['is_verified'] == 0) {
            $errors[] = "Account not verified. Please check your email.";
        } elseif ($role_selected !== $user['role']) {
            $errors[] = "Selected role does not match your account role.";
        } else {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect to dashboard based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'staff') {
                header("Location: staff_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<style>
body, html { height: 100%; margin:0; padding:0; font-family:'Segoe UI', sans-serif; }
.login-bg-blur {
    background-image: url('images/coffee1.jpg');
    background-size: cover;
    background-position: center;
    filter: blur(6px);
    position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1;
}
.form-container { background-color: rgba(255,255,255,0.9); padding:30px; border-radius:12px; box-shadow:0 0 20px rgba(0,0,0,0.2); max-width:500px; margin:auto;}
.section-title { font-size:2.2rem; text-align:center; font-weight:bold; margin-bottom:2rem; color:#333;}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="login-bg-blur"></div>

<section class="py-5">
  <div class="container">
    <div class="form-container">
      <h2 class="section-title">Login</h2>

      <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach($errors as $error) echo $error."<br>"; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Enter email" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required />
        </div>

        <div class="mb-3 text-end">
          <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
        </div>

        <!-- Role checkboxes -->
        <div class="form-check mb-2">
          <input class="form-check-input" type="radio" name="role" value="staff" id="isStaff">
          <label class="form-check-label" for="isStaff">Staff</label>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="radio" name="role" value="admin" id="isAdmin">
          <label class="form-check-label" for="isAdmin">Admin</label>
        </div>
        <!-- Default role is customer if no selection -->
        <input type="hidden" name="role" value="customer">

        <button type="submit" class="btn btn-secondary w-100">Login</button>
      </form>

      <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
  </div>
</section>

</body>
</html>
