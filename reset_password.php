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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password | Arjuna n Co-ffee</title>
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

/* Alert styling */
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

.alert-success {
    background-color: rgba(40, 167, 69, 0.1);
    color: #155724;
    border-left: 3px solid #28a745;
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
}

/* Decorative elements */
.decorative-line {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
    margin: 25px 0;
}

/* Password strength indicator */
.password-strength {
    height: 4px;
    margin-top: 8px;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.strength-weak {
    background-color: #dc3545;
    width: 25%;
}

.strength-medium {
    background-color: #ffc107;
    width: 50%;
}

.strength-strong {
    background-color: #28a745;
    width: 100%;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="login-content">
    <div class="login-card">
        <h2 class="section-header">Reset Your Password</h2>
        <p class="section-description">Create a new secure password for your account</p>

        <?php if($success): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
            <div class="text-center mt-4">
                <a href="login.php" class="btn btn-outline">← Back to Login</a>
            </div>
        <?php else: ?>
            <form method="POST" id="resetForm" autocomplete="off">
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="password" id="password" 
                               class="form-control" 
                               placeholder="Enter your new password"
                               autocomplete="new-password" />
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" 
                               class="form-control" 
                               placeholder="Re-enter your new password"
                               autocomplete="new-password" />
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Error messages at the bottom -->
                <?php if(!empty($errors)): ?>
                    <div class="error-container">
                        <div class="alert alert-danger">
                            <?php foreach($errors as $error) echo htmlspecialchars($error)."<br>"; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="login-actions">
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                    <a href="login.php" class="btn btn-outline">← Back to Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <div class="login-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | Secure Password Reset</p>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const eyeIcon = document.querySelector(`#${fieldId} + .password-toggle i`);
    
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

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('passwordStrength');
    
    // Reset
    strengthBar.className = 'password-strength';
    
    if (password.length === 0) {
        return;
    }
    
    // Calculate strength
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength += 1;
    
    // Character variety checks
    if (/[a-z]/.test(password)) strength += 1;
    if (/[A-Z]/.test(password)) strength += 1;
    if (/[0-9]/.test(password)) strength += 1;
    if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
    
    // Apply visual feedback
    if (strength <= 2) {
        strengthBar.classList.add('strength-weak');
    } else if (strength <= 4) {
        strengthBar.classList.add('strength-medium');
    } else {
        strengthBar.classList.add('strength-strong');
    }
});

// Form validation
document.getElementById('resetForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long.');
        return;
    }
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match. Please check both fields.');
        return;
    }
});
</script>

</body>
</html>