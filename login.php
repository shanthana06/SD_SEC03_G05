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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Arjuna n Co-ffee</title>
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
.login-header {
    padding: 40px 20px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.login-header h1 {
    font-family: 'Playfair Display', serif;
    font-weight: 400;
    font-size: 2.5rem;
    letter-spacing: 1px;
    color: #333;
}

/* Main content area */
.login-content {
    max-width: 500px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Login card */
.login-card {
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
    margin-bottom: 30px;
    font-style: italic;
    font-size: 1.1rem;
}

/* Form styling */
.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-label {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
    display: block;
    font-size: 1.1rem;
}

.password-input-wrapper {
    position: relative;
    width: 100%;
}

.form-control {
    width: 100%;
    padding: 12px 45px 12px 15px;
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

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.95rem;
    margin-top: 5px;
}

/* Password toggle button */
.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #777;
    cursor: pointer;
    font-size: 1.1rem;
    transition: color 0.3s ease;
    padding: 5px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle:hover {
    color: #333;
}

/* Role buttons */
.role-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 30px;
}

.role-btn {
    padding: 14px 20px;
    border: 1px solid #333;
    background: transparent;
    color: #333;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.1rem;
    font-weight: 400;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    width: 100%;
}

.role-btn:hover {
    background: #333;
    color: white;
    transform: translateY(-2px);
}

/* Action buttons */
.login-actions {
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
.login-footer {
    text-align: center;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid rgba(0,0,0,0.05);
    color: #777;
    font-size: 0.9rem;
}

.login-footer a {
    color: #777;
    text-decoration: none;
}

.login-footer a:hover {
    text-decoration: underline;
}

/* Alert styling - MOVED TO BOTTOM */
.alert {
    padding: 15px 20px;
    margin: 30px 0 20px 0;
    border-radius: 0;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.1rem;
}

.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #721c24;
    border-left: 3px solid #dc3545;
}

/* Error container at bottom */
.error-container {
    margin-top: 30px;
    margin-bottom: 20px;
}

/* Links */
.forgot-password {
    text-align: right;
    margin-top: 8px;
}

.forgot-password a {
    color: #777;
    text-decoration: none;
    font-size: 0.95rem;
}

.forgot-password a:hover {
    text-decoration: underline;
    color: #333;
}

.signup-link {
    text-align: center;
    margin-top: 25px;
    color: #777;
}

.signup-link a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
}

.signup-link a:hover {
    text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .login-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 250px;
    }
    
    .login-header h1 {
        font-size: 2rem;
    }
    
    .section-header {
        font-size: 1.5rem;
    }
    
    .role-buttons {
        gap: 10px;
    }
}

/* Decorative elements */
.decorative-line {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
    margin: 25px 0;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>



<div class="login-content">
    <div class="login-card">
        <h2 class="section-header">Login to Your Account</h2>
        <p class="section-description">Sign in to access your personalized experience</p>

        <form method="POST" id="loginForm" autocomplete="off">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" 
                       class="form-control <?php echo $email_error ? 'is-invalid' : ''; ?>" 
                       placeholder="your.email@example.com"
                       autocomplete="username"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                <?php if ($email_error): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($email_error); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="password" 
                           class="form-control <?php echo $password_error ? 'is-invalid' : ''; ?>" 
                           placeholder="Enter your password"
                           autocomplete="current-password" />
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if ($password_error): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($password_error); ?></div>
                <?php endif; ?>
                <div class="forgot-password">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>
            </div>

            <input type="hidden" name="role" id="roleField" value="">

            <div class="decorative-line"></div>

            <div class="role-buttons">
                <button type="submit" class="role-btn" onclick="setRole('customer')">
                    <i class="fas fa-user me-2"></i>Login as Customer
                </button>
                <button type="submit" class="role-btn" onclick="setRole('staff')">
                    <i class="fas fa-concierge-bell me-2"></i>Login as Staff
                </button>
                <button type="submit" class="role-btn" onclick="setRole('admin')">
                    <i class="fas fa-crown me-2"></i>Login as Admin
                </button>
            </div>

            <!-- Error messages at the bottom -->
            <?php if(!empty($errors)): ?>
                <div class="error-container">
                    <div class="alert alert-danger">
                        <?php foreach($errors as $error) echo htmlspecialchars($error)."<br>"; ?>
                    </div>
                </div>
            <?php endif; ?>
        </form>

        <div class="signup-link">
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </div>
    </div>
    
    <div class="login-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | Secure Login</p>
    </div>
</div>

<script>
function setRole(role) {
    document.getElementById("roleField").value = role;
}

function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.querySelector('.password-toggle i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

// Add some interactive feedback for role selection
document.addEventListener('DOMContentLoaded', function() {
    const roleButtons = document.querySelectorAll('.role-btn');
    
    roleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            roleButtons.forEach(btn => {
                btn.style.backgroundColor = '';
                btn.style.color = '';
            });
            
            // Add active style to clicked button
            this.style.backgroundColor = '#333';
            this.style.color = 'white';
        });
    });
});
</script>

</body>
</html>