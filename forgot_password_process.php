<?php
session_start();
require 'db_connection.php'; // sambung ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50)); // generate unique token
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Save token & expiry in database
        $stmt2 = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
        $stmt2->bind_param("ssi", $token, $expires, $user['id']);
        $stmt2->execute();

        // Send email
        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Hi,\n\nClick this link to reset your password:\n$resetLink\n\nLink expires in 1 hour.";
        $headers = "From: noreply@arjunancoffee.com";

        if(mail($email, $subject, $message, $headers)){
            echo "success";
        } else {
            echo "Email sending failed";
        }
    } else {
        echo "Email not found";
    }
}
?>
