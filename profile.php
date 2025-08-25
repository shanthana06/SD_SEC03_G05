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
$stmt = $conn->prepare("SELECT name, email, created_at, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fallback if no profile pic
$profilePic = !empty($user['profile_pic']) ? 'uploads/'.$user['profile_pic'] : 'images/default-profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>View Profile | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet" />
<style>
body, html { height: 100%; margin: 0; padding: 0; }
.login-bg-blur {
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  z-index: -1;
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

<div class="form-container text-center">
  <h2 class="section-title">Your Profile</h2>

  <!-- Profile Picture -->
  <div class="mb-4">
    <label for="profileImage" class="form-label">Profile Picture</label>
    <div class="mb-3">
      <img id="previewImage" src="<?php echo htmlspecialchars($profilePic); ?>" 
           alt="Profile Picture" class="rounded-circle" width="150" height="150" style="object-fit: cover;">
    </div>
    <input class="form-control" type="file" id="profileImage" accept="image/*">
  </div>

  <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
  <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
  <p><strong>Member Since:</strong> <?php echo date("F Y", strtotime($user['created_at'])); ?></p>

  <a href="editprofile.php" class="btn btn-secondary w-100 mt-3">Edit Profile</a>

  <div class="text-center mt-4">
    <a href="index.php" class="btn btn-outline-dark">Return to Home</a>
  </div>
</div>

<script>
// Load stored image on page load (if uploaded locally)
const imageInput = document.getElementById("profileImage");
const previewImage = document.getElementById("previewImage");

window.addEventListener("DOMContentLoaded", () => {
  const storedImage = localStorage.getItem("profilePicture");
  if(storedImage) previewImage.src = storedImage;
});

// Save uploaded image to localStorage for preview
imageInput.addEventListener("change", function(event) {
  const file = event.target.files[0];
  if(file && file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImage.src = e.target.result;
      localStorage.setItem("profilePicture", e.target.result);
    };
    reader.readAsDataURL(file);
  }
});
</script>

</body>
</html>
