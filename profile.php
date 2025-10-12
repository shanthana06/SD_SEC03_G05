<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 'true';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$errors = [];
$successMessages = [];

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && $edit_mode) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $currentPicFilename = $user['profile_pic'];
    $finalPicFilename = $currentPicFilename;

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $filename = $_FILES['profile_pic']['name'];
        $fileTmp = $_FILES['profile_pic']['tmp_name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid image type. Allowed: JPG, JPEG, PNG, GIF.";
        } else {
            $finalPicFilename = uniqid('u'.$user_id.'_').".".$ext;
            $uploadPath = "uploads/".$finalPicFilename;
            if (move_uploaded_file($fileTmp, $uploadPath)) {
                if ($currentPicFilename && file_exists("uploads/".$currentPicFilename)) {
                    unlink("uploads/".$currentPicFilename);
                }
                $successMessages[] = "✅ Profile picture updated successfully!";
            } else {
                $errors[] = "Failed to upload new profile picture.";
            }
        }
    }

    // If no errors, update DB
    if (empty($errors)) {
        $updateFields = [];
        $params = [];
        $types = "";

        if ($name !== $user['name']) {
            $updateFields[] = "name=?";
            $params[] = $name;
            $types .= "s";
            $successMessages[] = "✅ Full name updated successfully!";
        }

        if ($email !== $user['email']) {
            $updateFields[] = "email=?";
            $params[] = $email;
            $types .= "s";
            $successMessages[] = "✅ Email updated successfully!";
        }

        if ($finalPicFilename !== $currentPicFilename) {
            $updateFields[] = "profile_pic=?";
            $params[] = $finalPicFilename;
            $types .= "s";
        }

        if (!empty($updateFields)) {
            $params[] = $user_id;
            $types .= "i";
            $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE user_id=?";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param($types, ...$params);
            if ($stmt2->execute()) {
                // Refresh $user array
                $user['name'] = $name;
                $user['email'] = $email;
                $user['profile_pic'] = $finalPicFilename;
                // Refresh session name if changed
                $_SESSION['user_name'] = $name;
            } else {
                $errors[] = "Failed to update profile. (" . $stmt2->error . ")";
            }
            $stmt2->close();
        }
    }
}

$profilePicPath = !empty($user['profile_pic']) ? 'uploads/'.$user['profile_pic'] : 'images/default-profile.png';

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
<title><?php echo $edit_mode ? 'Edit Profile' : 'Profile'; ?> | Arjuna n Co-ffee</title>
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
.profile-header {
    padding: 40px 20px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.profile-header h1 {
    font-family: 'Playfair Display', serif;
    font-weight: 400;
    font-size: 2.5rem;
    letter-spacing: 1px;
    color: #333;
}

/* Main content area */
.profile-content {
    max-width: 600px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Profile card */
.profile-card {
    background: white;
    padding: 40px 30px;
    margin-bottom: 40px;
    text-align: center;
    position: relative;
}

/* Profile picture */
.profile-pic-container {
    margin-bottom: 30px;
    position: relative;
    display: inline-block;
}

.profile-pic {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.1);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

/* Profile info */
.profile-name {
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem;
    font-weight: 400;
    margin-bottom: 8px;
    color: #333;
}

.profile-email {
    font-size: 1.1rem;
    color: #777;
    margin-bottom: 20px;
    font-style: italic;
}

.profile-role {
    display: inline-block;
    padding: 6px 18px;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-bottom: 30px;
    background-color: #f8f8f8;
    color: #555;
    letter-spacing: 0.5px;
}

/* Profile stats */
.profile-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin: 30px 0;
    padding: 30px 0;
    border-top: 1px solid rgba(0,0,0,0.05);
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 400;
    color: #333;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.85rem;
    color: #777;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* Action buttons */
.profile-actions {
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
.profile-footer {
    text-align: center;
    margin-top: 60px;
    padding-top: 30px;
    border-top: 1px solid rgba(0,0,0,0.05);
    color: #777;
    font-size: 0.9rem;
}

.profile-footer a {
    color: #777;
    text-decoration: none;
}

.profile-footer a:hover {
    text-decoration: underline;
}

/* Form styling for edit mode */
.form-group {
    margin-bottom: 25px;
    text-align: left;
}

.form-label {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
    display: block;
    font-size: 1.1rem;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
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

.file-input-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}

.file-input-wrapper input[type=file] {
    opacity: 0;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    cursor: pointer;
}

.file-input-label {
    display: block;
    padding: 10px 15px;
    border: 1px dashed rgba(0,0,0,0.2);
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    color: #777;
}

.file-input-label:hover {
    border-color: #333;
    color: #333;
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-stats {
        flex-direction: column;
        gap: 20px;
    }
    
    .profile-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 250px;
    }
    
    .profile-header h1 {
        font-size: 2rem;
    }
    
    .profile-name {
        font-size: 1.8rem;
    }
}

/* Decorative elements */
.decorative-line {
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
    margin: 30px 0;
}

.member-since {
    font-style: italic;
    color: #777;
    margin-top: 20px;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>



<div class="profile-content">
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

    <div class="profile-card">
        <?php if($edit_mode): ?>
            <!-- Edit Profile Form -->
            <form method="POST" enctype="multipart/form-data">
                <div class="profile-pic-container">
                    <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Profile Picture" class="profile-pic" id="profilePicPreview">
                    <div class="form-group mt-4">
                        <label for="profile_pic" class="form-label">Change Profile Picture</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="profile_pic" id="profile_pic" class="form-control" accept="image/*">
                            <div class="file-input-label">Choose a new image</div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="profile-actions">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="profile.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <!-- Profile View -->
            <div class="profile-pic-container">
                <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Profile Picture" class="profile-pic">
            </div>
            
            <h2 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h2>
            <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
            
            <div>
                <span class="profile-role"><?php echo ucfirst($role); ?></span>
            </div>
            
            <div class="decorative-line"></div>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $months; ?></div>
                    <div class="stat-label">Months with us</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo ucfirst($role); ?></div>
                    <div class="stat-label">Account Type</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">Active</div>
                    <div class="stat-label">Status</div>
                </div>
            </div>
            
            <p class="member-since">
                Member since <?php echo date("F Y", strtotime($user['created_at'])); ?>
            </p>
            
            <div class="profile-actions">
                <a href="profile.php?edit=true" class="btn btn-primary">Edit Profile</a>
                <a href="index.php" class="btn btn-outline">Return to Home</a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="profile-footer">
        <p>Arjuna n Co-ffee &copy; <?php echo date("Y"); ?> | Need help? <a href="support.php">Contact Support</a></p>
    </div>
</div>

<script>
// Simple image preview for profile picture upload
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_pic');
    const profilePic = document.getElementById('profilePicPreview');
    
    if (fileInput && profilePic) {
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    profilePic.src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>

</body>
</html>