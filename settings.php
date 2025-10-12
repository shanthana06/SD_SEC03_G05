<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$successMessages = [];

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Fetch current user data to verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Validate current password
    if (!password_verify($current_password, $user['password'])) {
        $errors[] = "Current password is incorrect.";
    }

    // Validate new password
    if (strlen($new_password) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "New password and confirmation do not match.";
    }

    // If no errors, update password
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $successMessages[] = "✅ Password updated successfully!";
        } else {
            $errors[] = "Failed to update password. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch user data for display
$stmt = $conn->prepare("SELECT name, email, created_at, profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$profilePicPath = !empty($user['profile_pic']) ? 'uploads/'.$user['profile_pic'] : 'images/default-profile.png';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';

// Calculate member duration
$joinDate = new DateTime($user['created_at']);
$now = new DateTime();
$interval = $joinDate->diff($now);
$months = $interval->y * 12 + $interval->m;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings | Arjuna n Co-ffee</title>
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
.settings-header {
    padding: 40px 20px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.settings-header h1 {
    font-family: 'Playfair Display', serif;
    font-weight: 400;
    font-size: 2.5rem;
    letter-spacing: 1px;
    color: #333;
}

/* Main content area */
.settings-content {
    max-width: 600px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Settings card */
.settings-card {
    background: white;
    padding: 40px 30px;
    margin-bottom: 40px;
    position: relative;
}

/* Profile section */
.profile-section {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.profile-pic {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.1);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    margin-bottom: 15px;
}

.profile-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 400;
    margin-bottom: 5px;
    color: #333;
}

.profile-email {
    font-size: 1rem;
    color: #777;
    margin-bottom: 10px;
}

.profile-role {
    display: inline-block;
    padding: 4px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    background-color: #f8f8f8;
    color: #555;
    letter-spacing: 0.5px;
}

/* Section headers */
.section-header {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 400;
    margin-bottom: 25px;
    color: #333;
    text-align: center;
}

.section-description {
    text-align: center;
    color: #777;
    margin-bottom: 30px;
    font-style: italic;
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

/* Password toggle button - FIXED POSITION */
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

.password-requirement {
    font-size: 0.9rem;
    color: #777;
    margin-top: 5px;
    font-style: italic;
}

/* Action buttons */
.settings-actions {
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
.settings-footer {
    text-align: center;
    margin-top: 60px;
    padding-top: 30px;
    border-top: 1px solid rgba(0,0,0,0.05);
    color: #777;
    font-size: 0.9rem;
}

.settings-footer a {
    color: #777;
    text-decoration: none;
}

.settings-footer a:hover {
    text-decoration: underline;
}

/* Alert styling */
.alert {
    padding: 15px 20px;
    margin-bottom: 30px;
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

/* Quick links */
.quick-links {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.quick-link {
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.quick-link:hover {
    transform: translateY(-3px);
    color: #555;
}

.quick-link i {
    font-size: 1.5rem;
    margin-bottom: 8px;
    display: block;
}

.quick-link span {
    font-size: 0.9rem;
    letter-spacing: 0.5px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .settings-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 250px;
    }
    
    .settings-header h1 {
        font-size: 2rem;
    }
    
    .quick-links {
        flex-direction: column;
        gap: 20px;
    }
    
    .profile-name {
        font-size: 1.5rem;
    }
}

/* Decorative elements */
.decorative-line {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
    margin: 30px 0;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>


<div class="settings-content">
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $err) echo htmlspecialchars($err)."<br>"; ?>
        </div>
    <?php endif; ?>

    <?php if(!empty($successMessages)): ?>
        <div class="alert alert-success">
            <?php foreach($successMessages as $msg) echo htmlspecialchars($msg)."<br>"; ?>
        </div>
    <?php endif; ?>

    <div class="settings-card">
        <!-- Profile Overview -->
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Profile Picture" class="profile-pic">
            <h2 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h2>
            <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
            <span class="profile-role"><?php echo ucfirst($role); ?></span>
            <p class="text-muted mt-2">Member for <?php echo $months; ?> months</p>
        </div>

        <!-- Password Change Section -->
        <h3 class="section-header">Change Password</h3>
        <p class="section-description">Update your password to keep your account secure</p>

        <form method="POST">
            <input type="hidden" name="change_password" value="1">
            
            <div class="form-group">
                <label for="current_password" class="form-label">Current Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                    <button type="button" class="password-toggle" data-target="current_password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="new_password" class="form-label">New Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                    <button type="button" class="password-toggle" data-target="new_password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-requirement">Must be at least 8 characters long</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    <button type="button" class="password-toggle" data-target="confirm_password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="settings-actions">
                <button type="submit" class="btn btn-primary">Update Password</button>
                <a href="profile.php" class="btn btn-outline">Back to Profile</a>
            </div>
        </form>

        <!-- Quick Links -->
        <div class="quick-links">
            <a href="profile.php" class="quick-link">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="profile.php?edit=true" class="quick-link">
                <i class="fas fa-edit"></i>
                <span>Edit Profile</span>
            </a>
            <a href="index.php" class="quick-link">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
        </div>
    </div>
    
    <div class="settings-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | Need help? <a href="support.php">Contact Support</a></p>
    </div>
</div>

<script>
// Password visibility toggle
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.password-toggle');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Password confirmation validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePassword() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords do not match");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    newPassword.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>

</body>
</html>