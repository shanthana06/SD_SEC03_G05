<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("No menu item selected.");
}

$id = intval($_GET['id']);

// Fetch current menu data
$res = mysqli_query($conn, "SELECT * FROM menu_items WHERE id=$id");
$item = mysqli_fetch_assoc($res);

if (!$item) die("Menu item not found.");

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    mysqli_query($conn, "UPDATE menu_items SET name='$name', price='$price', description='$description' WHERE id=$id");

    // Redirect back to menu list page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Menu Item</title>
</head>
<body>
<h2>Edit Menu Item</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Name:</label>
    <input type="text" name="name" value="<?= $item['name']; ?>" required><br>

    <label>Price (RM):</label>
    <input type="number" step="0.01" name="price" value="<?= $item['price']; ?>" required><br>

    <label>Description:</label>
    <textarea name="description"><?= $item['description']; ?></textarea><br>

    <button type="submit" name="update">Update</button>
</form>
</body>
</html>
