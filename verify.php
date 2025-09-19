<<<<<<< HEAD
<?php
session_start();
include 'db.php'; // database connection

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Invalid verification link.");
}

// Find user with this token
$stmt = $conn->prepare("SELECT user_id, is_verified FROM users WHERE verification_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired verification link.");
}

$user = $result->fetch_assoc();

if ($user['is_verified']) {
    echo "<h2>Your account is already verified!</h2><p><a href='login.php'>Login here</a></p>";
} else {
    // Update user as verified
    $update = $conn->prepare("UPDATE users SET is_verified=1, verification_token=NULL WHERE user_id=?");
    $update->bind_param("i", $user['user_id']);
    if ($update->execute()) {
        echo "<h2>✅ Your account has been verified!</h2><p><a href='login.php'>Login now</a></p>";
    } else {
        echo "Something went wrong. Please try again later.";
    }
}
?>
<?php
session_start();
include 'db.php'; // Make sure this points to your database connection

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Invalid verification link.");
}

// Find user with this token
$stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE verification_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired verification link.");
}

$user = $result->fetch_assoc();

if ($user['is_verified']) {
    echo "<h2>Your account is already verified!</h2><p><a href='login.php'>Login here</a></p>";
} else {
    // Update user as verified
    $update = $conn->prepare("UPDATE users SET is_verified=1, verification_token=NULL WHERE id=?");
    $update->bind_param("i", $user['id']);
    if ($update->execute()) {
        echo "<h2>✅ Your account has been verified!</h2><p><a href='login.php'>Login now</a></p>";
    } else {
        echo "Something went wrong. Please try again later.";
    }
}
?>
=======
<?php
session_start();
include 'db.php'; // Make sure this points to your database connection

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Invalid verification link.");
}

// Find user with this token
$stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE verification_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired verification link.");
}

$user = $result->fetch_assoc();

if ($user['is_verified']) {
    echo "<h2>Your account is already verified!</h2><p><a href='login.php'>Login here</a></p>";
} else {
    // Update user as verified
    $update = $conn->prepare("UPDATE users SET is_verified=1, verification_token=NULL WHERE id=?");
    $update->bind_param("i", $user['id']);
    if ($update->execute()) {
        echo "<h2>✅ Your account has been verified!</h2><p><a href='login.php'>Login now</a></p>";
    } else {
        echo "Something went wrong. Please try again later.";
    }
}
?>
>>>>>>> 9c3cfdedaaf306ac261286e46793cbcf989f68c9
