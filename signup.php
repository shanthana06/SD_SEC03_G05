<?php
// Sambung database
$servername = "localhost";
$username = "root";  // kalau pakai XAMPP default dia root
$password = "";      // default kosong
$dbname = "arjuna_coffee";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Dapatkan data dari form
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmpass = $_POST['confirmpass'];

// Check password sama ke tak
if ($password !== $confirmpass) {
  die("❌ Passwords do not match!");
}

// Hash password untuk security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert ke database
$sql = "INSERT INTO users (fullname, email, password) 
        VALUES ('$fullname', '$email', '$hashedPassword')";

if ($conn->query($sql) === TRUE) {
  // Redirect ke page success
  header("Location: signupsuccessful.html");
  exit();
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
