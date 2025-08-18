<?php
session_start(); // Start session

// Hapus semua session
$_SESSION = array(); // kosongkan semua session variables

// Hapus session cookie kalau ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hapus session
session_destroy();

// Redirect ke logout confirmation page
header("Location: logout.html");
exit();
?>
