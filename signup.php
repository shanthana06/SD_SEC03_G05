<?php
session_start();
include 'db.php'; 

require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';
require __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role             = "customer"; 

    // Field-level validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    } elseif (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        $errors[] = "Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>).";
    } else {
        // Check if email already exists - FIXED QUERY
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Email is already registered. Please use a different email or login.";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Generate verification token
                $token = bin2hex(random_bytes(50));

                // Insert into users table
                $insert = $conn->prepare(
                    "INSERT INTO users (name, email, password, role, verification_token, is_verified, created_at) 
                     VALUES (?, ?, ?, ?, ?, 0, NOW())"
                );
                
                if ($insert) {
                    $insert->bind_param("sssss", $name, $email, $hashed_password, $role, $token);

                    if ($insert->execute()) {
                        // Send verification email
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'peilingbts@gmail.com'; // your Gmail
                            $mail->Password   = 'zyse rhvs wlix nihw'; // Gmail App Password
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
                            $mail->Port       = 465;

                            $mail->setFrom('peilingbts@gmail.com', 'Arjuna n Co-ffee');
                            $mail->addAddress($email, $name);

                            $mail->isHTML(true);
                            $mail->Subject = 'Verify Your Arjuna n Co-ffee Account';
                            $verification_link = "http://localhost/arjunababy/verify.php?token=" . $token;

                            $mail->Body    = "Hi $name,<br><br>
                                Thank you for signing up! Please verify your account by clicking the link below:<br>
                                <a href='$verification_link'>Verify Account</a><br><br>
                                Cheers,<br>Arjuna n Co-ffee Team";

                            if ($mail->send()) {
                                $success = " Account created! Please check your email to verify your account.";
                            } else {
                                $errors[] = " Verification email could not be sent. Please contact support.";
                            }
                        } catch (Exception $e) {
                            $errors[] = " Email configuration error. Please try again later.";
                        }
                    } else {
                        $errors[] = " Database error: " . $insert->error;
                    }
                    $insert->close();
                } else {
                    $errors[] = " Database preparation failed.";
                }
            }
            $stmt->close();
        } else {
            $errors[] = " Database connection error.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | Arjuna n Co-ffee</title>
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
    .signup-header {
        padding: 40px 20px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .signup-header h1 {
        font-family: 'Playfair Display', serif;
        font-weight: 400;
        font-size: 2.5rem;
        letter-spacing: 1px;
        color: #333;
    }

    /* Main content area */
    .signup-content {
        max-width: 500px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* Signup card */
    .signup-card {
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
    .signup-actions {
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
    .signup-footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid rgba(0,0,0,0.05);
        color: #777;
        font-size: 0.9rem;
    }

    .signup-footer a {
        color: #777;
        text-decoration: none;
    }

    .signup-footer a:hover {
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
    .login-link {
        text-align: center;
        margin-top: 25px;
        color: #777;
    }

    .login-link a {
        color: #333;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    /* Password requirement */
    .password-requirements {
        font-size: 0.85rem;
        color: #777;
        margin-top: 8px;
        padding: 10px;
        background: rgba(0,0,0,0.02);
        border-radius: 4px;
    }

    .password-requirements ul {
        margin: 0;
        padding-left: 20px;
    }

    .password-requirements li {
        margin-bottom: 3px;
    }

    .requirement-met {
        color: #28a745;
    }

    .requirement-not-met {
        color: #dc3545;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .signup-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .btn {
            width: 100%;
            max-width: 250px;
        }
        
        .signup-header h1 {
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
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>



<div class="signup-content">
    <div class="signup-card">
        <h2 class="section-header">Create Your Account</h2>
        <p class="section-description">Start your coffee journey with us today</p>

        <form method="POST" action="" id="signupForm">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Your full name" required 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="your.email@example.com" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required />
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-requirements">
                    <strong>Password must contain:</strong>
                    <ul>
                        <li id="req-length">At least 8 characters</li>
                        <li id="req-upper">One uppercase letter (A-Z)</li>
                        <li id="req-lower">One lowercase letter (a-z)</li>
                        <li id="req-number">One number (0-9)</li>
                        <li id="req-special">One special character (!@#$%^&*()\-_=+{};:,<.>)</li>
                    </ul>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" required />
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="password-match" class="password-requirement" style="display: none;"></div>
            </div>

            <!-- Error messages at the bottom -->
            <?php if(!empty($errors)): ?>
                <div class="error-container">
                    <div class="alert alert-danger">
                        <?php foreach($errors as $error) echo htmlspecialchars($error)."<br>"; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Success message at the bottom -->
            <?php if($success): ?>
                <div class="error-container">
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="signup-actions">
                <button type="submit" class="btn btn-primary">Create Account</button>
                <a href="login.php" class="btn btn-outline">Back to Login</a>
            </div>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
    
    <div class="signup-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | Secure Registration</p>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const eyeIcon = passwordInput.parentNode.querySelector('.password-toggle i');
    
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

// Real-time password validation
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordMatch = document.getElementById('password-match');
    
    function validatePassword() {
        const value = password.value;
        
        // Update requirement indicators
        document.getElementById('req-length').className = value.length >= 8 ? 'requirement-met' : 'requirement-not-met';
        document.getElementById('req-upper').className = /[A-Z]/.test(value) ? 'requirement-met' : 'requirement-not-met';
        document.getElementById('req-lower').className = /[a-z]/.test(value) ? 'requirement-met' : 'requirement-not-met';
        document.getElementById('req-number').className = /[0-9]/.test(value) ? 'requirement-met' : 'requirement-not-met';
        document.getElementById('req-special').className = /[!@#$%^&*()\-_=+{};:,<.>]/.test(value) ? 'requirement-met' : 'requirement-not-met';
        
        // Password match validation
        if (confirmPassword.value) {
            if (password.value !== confirmPassword.value) {
                passwordMatch.textContent = " Passwords do not match";
                passwordMatch.style.color = '#dc3545';
                passwordMatch.style.display = 'block';
                confirmPassword.setCustomValidity("Passwords do not match");
            } else {
                passwordMatch.textContent = " Passwords match";
                passwordMatch.style.color = '#28a745';
                passwordMatch.style.display = 'block';
                confirmPassword.setCustomValidity('');
            }
        } else {
            passwordMatch.style.display = 'none';
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
    
    // Form submission validation
    document.getElementById('signupForm').addEventListener('submit', function(e) {
        const value = password.value;
        if (value.length < 8 || 
            !/[A-Z]/.test(value) || 
            !/[a-z]/.test(value) || 
            !/[0-9]/.test(value) || 
            !/[!@#$%^&*()\-_=+{};:,<.>]/.test(value)) {
            e.preventDefault();
            alert('Please ensure your password meets all requirements.');
        }
    });
});
</script>

</body>
</html>