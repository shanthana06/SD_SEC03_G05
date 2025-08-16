<?php
$conn = new mysqli("localhost", "root", "", "arjunancoffee");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // encrypt password
$role = "guest";

$sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";

if ($conn->query($sql) === TRUE) {
    echo "Signup berjaya!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
