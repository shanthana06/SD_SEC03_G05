<<<<<<< HEAD
<?php
session_start();
include 'db.php'; // your database connection

$errors = [];
$email_error = '';
$password_error = '';
$selected_role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $selected_role = isset($_POST['role']) ? $_POST['role'] : '';

    // Field-level validation
    if (empty($email)) {
        $email_error = "Email is required.";
    }
    if (empty($password)) {
        $password_error = "Please fill out this field.";
    }
    if (empty($selected_role)) {
        $errors[] = "Please choose a role (Customer, Staff, or Admin).";
    }

    // Only proceed if all fields are filled
    if (empty($email_error) && empty($password_error) && empty($errors)) {
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
            $email_error = "Email not found. Please sign up.";
        } else {
            // Only check password if email exists
            if (!password_verify($password, $user['password'])) {
                $password_error = "Incorrect password.";
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

     <form method="POST" id="loginForm" autocomplete="off">
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" 
           class="form-control <?php echo $email_error ? 'is-invalid' : ''; ?>" 
           placeholder="Enter email"
           autocomplete="username"
           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
    <?php if ($email_error): ?>
      <div class="invalid-feedback"><?php echo htmlspecialchars($email_error); ?></div>
    <?php endif; ?>
  </div>

  <div class="mb-3">
  <label class="form-label">Password</label>
  <input type="password" name="password" 
         class="form-control <?php echo $password_error ? 'is-invalid' : ''; ?>" 
         placeholder="Enter password"
         autocomplete="new-password" />
  <?php if ($password_error): ?>
    <div class="invalid-feedback"><?php echo htmlspecialchars($password_error); ?></div>
  <?php endif; ?>
  <div class="mt-1">
    <a href="forgot_password.php" class="link-custom">Forgot Password?</a>
    
  </div>
</div>


  <input type="hidden" name="role" id="roleField" value="">

  <div class="d-grid gap-2">
    <button type="submit" class="btn btn-coffee" onclick="setRole('customer')">Login as Customer</button>
    <button type="submit" class="btn btn-coffee" onclick="setRole('staff')">Login as Staff</button>
    <button type="submit" class="btn btn-coffee" onclick="setRole('admin')">Login as Admin</button>
  </div>
</form>

      <p class="text-center mt-3">Don't have an account? 
        <a href="signup.php" class="link-custom">Sign up here</a>
      </p>
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
=======
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
>>>>>>> 9c3cfdedaaf306ac261286e46793cbcf989f68c9
