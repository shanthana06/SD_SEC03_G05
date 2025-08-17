<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "arjuna_coffee";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
  $token = $_GET['token'];
  $sql = "UPDATE users SET is_verified=1 WHERE verify_token='$token'";

  if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
    echo "🎉 Email verified successfully! You can now login.";
  } else {
    echo "❌ Invalid or expired token.";
  }
}

$conn->close();
?>
