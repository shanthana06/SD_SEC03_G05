
<?php
session_start();

// Simulate user database
$users = [
  'admin@arjuna.com' => ['password' => 'admin123', 'role' => 'admin'],
  'staff@arjuna.com' => ['password' => 'staff123', 'role' => 'staff'],
  'user@arjuna.com'  => ['password' => 'user123',  'role' => 'customer'],
];

$username = $_POST['username'];
$password = $_POST['password'];

// Check if user exists
if (isset($users[$username]) && $users[$username]['password'] === $password) {
  $_SESSION['username'] = $username;
  $_SESSION['role'] = $users[$username]['role'];

  // Redirect based on role
  if ($_SESSION['role'] === 'staff' || $_SESSION['role'] === 'admin') {
    header("Location: staff_dashboard.html"); // Or staff_dashboard.php
  } else {
    header("Location: index.html");
  }
  exit();
} else {
  echo "<script>alert('Invalid login'); window.location.href='login.php';</script>";
}
?>
<?php
session_start();

// Simulate user database
$users = [
  'admin@arjuna.com' => ['password' => 'admin123', 'role' => 'admin'],
  'staff@arjuna.com' => ['password' => 'staff123', 'role' => 'staff'],
  'user@arjuna.com'  => ['password' => 'user123',  'role' => 'customer'],
];

$username = $_POST['username'];
$password = $_POST['password'];

// Check if user exists
if (isset($users[$username]) && $users[$username]['password'] === $password) {
  $_SESSION['username'] = $username;
  $_SESSION['role'] = $users[$username]['role'];

  // Redirect based on role
  if ($_SESSION['role'] === 'staff' || $_SESSION['role'] === 'admin') {
    header("Location: staff_dashboard.html"); // Or staff_dashboard.php
  } else {
    header("Location: index.html");
  }
  exit();
} else {
  echo "<script>alert('Invalid login'); window.location.href='login.php';</script>";
}
?>
