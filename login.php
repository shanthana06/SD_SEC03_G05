<?php
session_start();
include 'db.php'; // your database connection

$errors = [];
$selected_role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $selected_role = isset($_POST['role']) ? $_POST['role'] : '';

    if (empty($email) && !empty($password)) {
        $errors[] = "Email is required.";
    } elseif (!empty($email) && empty($password)) {
        $errors[] = "Please fill out this field (Password).";
    } elseif (empty($email) && empty($password)) {
        $errors[] = "Please fill out both Email and Password.";
    } elseif (empty($selected_role)) {
        $errors[] = "Please choose a role (Customer, Staff, or Admin).";
    } else {
        $stmt = $conn->prepare("SELECT user_id, name, email, password, role, is_verified 
                                FROM users WHERE email=? LIMIT 1");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
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
        } elseif ($user['role'] !== $selected_role) {
            $errors[] = "Selected role does not match this account.";
        } else {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            switch ($user['role']) {
                case 'admin': header("Location: admin_dashboard.php"); break;
                case 'staff': header("Location: staff_dashboard.php"); break;
                default:      header("Location: index.php"); break;
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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
<style>
body, html {
  height: 100%; margin:0;
  font-family: 'Poppins', sans-serif;
}
.login-bg-blur {
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1;
}
.form-container {
  background-color: rgba(255,255,255,0.92);
  padding: 35px;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.2);
  max-width: 500px;
  margin: auto;
}
.section-title {
  font-family: 'Playfair Display', serif;
  font-size: 2.2rem;
  text-align: center;
  font-weight: 700;
  margin-bottom: 1.8rem;
  color: #3e2723;
  letter-spacing: 1px;
}
.btn-coffee {
  border: 2px solid #3e2723;
  background-color: transparent;
  color: #3e2723;
  font-weight: 500;
  padding: 10px;
  border-radius: 30px;
  transition: all 0.3s ease;
  font-family: 'Poppins', sans-serif;
}
.btn-coffee:hover {
  background-color: #3e2723;
  color: #fff;
}
.link-custom {
  color: #6d4c41;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
}
.link-custom:hover {
  text-decoration: underline;
}
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
          <?php foreach($errors as $error) echo htmlspecialchars($error)."<br>"; ?>
        </div>
      <?php endif; ?>

      <form method="POST" id="loginForm">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" 
                 placeholder="Enter email" required 
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required />
        </div>

        <div class="mb-3 text-end">
          <a href="forgot_password.php" class="link-custom">Forgot Password?</a>
        </div>

        <input type="hidden" name="role" id="roleField" value="">

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-coffee" onclick="setRole('customer')">Login as Customer</button>
          <button type="submit" class="btn btn-coffee" onclick="setRole('staff')">Login as Staff</button>
          <button type="submit" class="btn btn-coffee" onclick="setRole('admin')">Login as Admin</button>
        </div>
      </form>

      <p class="text-center mt-3">Don't have an account? <a href="signup.php" class="link-custom">Sign up here</a></p>
    </div>
  </div>
</section>

<script>
function setRole(role) {
  document.getElementById("roleField").value = role;
}
</script>

</body>
</html>
