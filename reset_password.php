<?php
session_start();
require 'db_connection.php';

if(isset($_GET['token'])){
    $token = $_GET['token'];

    // Check token validity
    $stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();
        if(strtotime($user['reset_expires']) < time()){
            die("Token expired. Please request a new password reset.");
        }
    } else {
        die("Invalid token.");
    }
} else {
    die("No token provided.");
}

// Handle new password submit
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt2 = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
    $stmt2->bind_param("si", $newPassword, $user['id']);
    $stmt2->execute();

    echo "Password updated successfully! <a href='login.html'>Login now</a>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
<h2>Reset Your Password</h2>
<form method="POST">
    <input type="password" name="password" placeholder="New password" required><br><br>
    <button type="submit">Update Password</button>
</form>
</body>
</html>
