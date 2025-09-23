<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            } else {
                $errors[] = "Failed to update profile. (" . $stmt2->error . ")";
            }
            $stmt2->close();
        }
    }
}

$profilePicPath = !empty($user['profile_pic']) ? 'uploads/'.$user['profile_pic'] : 'images/default-profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile - Arjuna n Co-ffee</title>
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
.profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="form-container text-center">
        <h2 class="mb-4">Edit Profile</h2>

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

        <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Profile Picture" class="profile-pic">

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3 text-start">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="profile_pic" class="form-control">
            </div>

            <button type="submit" class="btn btn-dark w-100">Update Profile</button>
            <a href="profile.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>
