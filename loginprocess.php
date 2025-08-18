<?php
session_start(); // untuk simpan session kalau login berjaya

// Database connection
$servername = "localhost";
$username = "root";  // ganti ikut database awak
$password = "";
$dbname = "arjuna_n_coffee";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrUsername = trim($_POST['email_or_username']);
    $passwordInput = trim($_POST['password']);
    $role = $_POST['role']; // staff/admin

    // Cari user ikut email atau username
    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email=? OR username=?");
    $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();

        // Check password
        if(password_verify($passwordInput, $user['password'])){
            // Check role
            if($role === $user['role']){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect
                if($role === 'staff'){
                    header("Location: staff_dashboard.php");
                } elseif($role === 'admin'){
                    header("Location: admin_dashboard.php");
                }
                exit();
            } else {
                echo "Role mismatch. Please select correct role.";
            }
        } else {
            echo "Wrong password.";
        }
    } else {
        echo "User not found.";
    }
    $stmt->close();
}
$conn->close();
?>
