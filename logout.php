
<?php
// Start session and destroy it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
$_SESSION = [];
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logout Successful | Arjuna n Co-ffee</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    body, html { height: 100%; margin: 0; }
    .bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px);
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
    .form-container {
      background: rgba(255, 255, 255, 0.9);
      padding: 40px;
      border-radius: 12px;
      max-width: 500px;
      margin: 80px auto;
      text-align: center;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

<div class="bg-blur"></div>


<?php include 'navbar.php'; ?>
<script>
  fetch('navbar.html')
    .then(res => res.text())
    .then(data => { document.getElementById('navbar-placeholder').innerHTML = data; });
</script>

<div class="form-container">
  <h2 class="text-success">✅ You have successfully logged out.</h2>
  <p>We hope to see you again soon at <strong>Arjuna n Co-ffee</strong>!</p>
  <a href="index.php" class="btn btn-secondary mt-3">Return to Home</a>
</div>

</body>
</html>
