<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) die("Customer ID not provided");

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id AND role='customer'");
$customer = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];

    mysqli_query($conn, "UPDATE users 
                         SET name='$name', email='$email' 
                         WHERE user_id=$id AND role='customer'");
    header("Location: customers_list_staff.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Edit Customer</h2>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?= $customer['name']; ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= $customer['email']; ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="customers_list_staff.php" class="btn btn-secondary">Cancel</a>
  </form>
</body>
</html>
