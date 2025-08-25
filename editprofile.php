<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$profilePic = !empty($user['profile_pic']) ? 'uploads/'.$user['profile_pic'] : 'images/default-profile.png';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($name) || empty($email)){
        $errors[] = "Name and email are required.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Invalid email format.";
    } else {
        // Handle optional profile picture upload
        if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK){
            $fileTmp = $_FILES['profile_pic']['tmp_name'];
            $fileName = uniqid() . "_" . basename($_FILES['profile_pic']['name']);
            move_uploaded_file($fileTmp, "uploads/".$fileName);
            $profilePic = "uploads/".$fileName;
        }

        // Update password only if provided
        if(!empty($password)){
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("UPDATE users SET name=?, email=?, password=?, profile_pic=? WHERE id=?");
            $stmt2->bind_param("ssssi", $name, $email, $hashed, $fileName, $user_id);
        } else {
            $stmt2 = $conn->prepare("UPDATE users SET name=?, email=?, profile_pic=? WHERE id=?");
            $stmt2->bind_param("sssi", $name, $email, $fileName, $user_id);
        }

        if($stmt2->execute()){
            $success = "✅ Profile updated successfully!";
        } else {
            $errors[] = "Failed to update profile.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Profile | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet" />
<style>
body, html { height: 100%; margin:0; padding:0; }
.login-bg-blur {
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed; top:0; left:0; width:100%; height:100%;
  z-index:-1;
}
.form-container {
  background-color: rgba(255, 255, 255, 0.9);
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0,0,0,0.2);
  max-width: 500px;
  margin: auto;
}
.section-title { font-size: 2.2rem; margin-bottom: 1.5rem; color: #333; text-align: center; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="login-bg-blur"></div>

<div class="form-container">
  <h2 class="section-title text-center">Edit Profile</h2>

  <?php if(!empty($errors)): ?>
      <div class="alert alert-danger"><?php foreach($errors as $e) echo $e."<br>"; ?></div>
  <?php endif; ?>

  <?php if($success): ?>
      <div class="alert alert-success text-center"><?php echo $success; ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3 text-center">
      <label for="profileImage" class="form-label">Profile Picture</label>
      <div class="mb-3">
        <img id="previewImage" src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="rounded-circle" width="150" height="150" style="object-fit: cover;">
      </div>
      <input class="form-control" type="file" name="profile_pic" id="profileImage" accept="image/*">
    </div>

    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">New Password</label>
      <input type="password" class="form-control" name="password" placeholder="Leave blank if unchanged">
    </div>

    <button type="submit" class="btn btn-secondary w-100">Save Changes</button>
    <div class="text-center mt-4">
      <a href="profile.php" class="btn btn-outline-dark">Back to Profile</a>
    </div>
  </form>
</div>

<script>
// Preview uploaded profile image
const imageInput = document.getElementById("profileImage");
const previewImage = document.getElementById("previewImage");
imageInput.addEventListener("change", function(event){
    const file = event.target.files[0];
    if(file && file.type.startsWith("image/")){
        const reader = new FileReader();
        reader.onload = function(e){
            previewImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>
