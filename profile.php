<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$stmt = $conn->prepare("SELECT name, email, created_at, profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$profilePic = !empty($user['profile_pic']) ? 'uploads/'.$user['profile_pic'] : 'images/default-profile.png';

// Get role from session (default: customer)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Profile | Arjuna n Co-ffee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body, html {
  margin: 0; padding: 0; height: 100%;
  font-family: 'Segoe UI', sans-serif;
}

/* Fullscreen blurred background */
.login-bg-blur {
  background-image: url('images/coffee1.jpg');
  background-size: cover;
  background-position: center;
  filter: blur(6px);
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  z-index: -1;
}

/* Profile card */
.profile-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  max-width: 700px;
  padding: 30px;
  text-align: center;
  margin: 100px auto;
  position: relative;
}

/* Profile picture */
.profile-pic {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 50%;
  border: 5px solid #fff;
  margin-top: -100px;
}

/* Name + role */
.profile-name {
  font-size: 1.8rem;
  font-weight: bold;
  margin-top: 15px;
}
.profile-role {
  color: #777;
  font-size: 1rem;
  margin-bottom: 15px;
}

/* Social icons */
.profile-social a {
  margin: 0 8px;
  color: #555;
  font-size: 1.2rem;
  transition: 0.3s;
}
.profile-social a:hover {
  color: #000;
}

/* Description */
.profile-bio {
  font-size: 0.95rem;
  color: #555;
  margin-top: 15px;
}
.navbar {
  position: absolute;
  top: 0;
  width: 100%;
  z-index: 10;
  background: transparent !important;
}
.navbar a, .navbar-brand {
  color: #fff !important;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>


<!-- Background -->
<div class="login-bg-blur"></div>

<!-- Profile card -->
<div class="profile-card">
  <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="profile-pic">

  <h2 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h2>
  <p class="profile-role"><?php echo ucfirst($role); ?> ☕</p>

  <div class="profile-social mb-3">
    <a href="#"><i class="fab fa-twitter"></i></a>
    <a href="#"><i class="fab fa-facebook"></i></a>
    <a href="#"><i class="fab fa-instagram"></i></a>
  </div>

  <p class="profile-bio">
    Member since <?php echo date("F Y", strtotime($user['created_at'])); ?><br>
    Contact: <?php echo htmlspecialchars($user['email']); ?>
  </p>

  <div class="d-grid gap-2 mt-4">
    <a href="editprofile.php" class="btn btn-dark">Edit Profile</a>
    <a href="index.php" class="btn btn-outline-secondary">Return to Home</a>
  </div>
</div>

</body>
</html>
