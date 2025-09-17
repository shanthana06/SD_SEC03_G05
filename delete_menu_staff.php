<?php
include 'db.php';
session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
    echo '<a href="index.php" class="btn btn-warning" style="margin:10px;">⬅ Back to Home</a>';
}

if (!isset($_GET['id'])) { 
    die("ID not provided."); 
}
$id = intval($_GET['id']);

// ✅ Fetch image from menu table
$result = mysqli_query($conn, "SELECT image FROM menu WHERE id=$id");
$row = mysqli_fetch_assoc($result);

if ($row && $row['image']) {
    $filePath = "uploads/" . $row['image'];
    if (file_exists($filePath)) {
        unlink($filePath); // delete image file
    }
}

// ✅ Delete from menu table
$sql = "DELETE FROM menu WHERE id=$id";

if (mysqli_query($conn, $sql)) {
    header("Location: menu_list_staff.php?msg=deleted");
    exit();
} else {
    echo "Error deleting: " . mysqli_error($conn);
}
?>
