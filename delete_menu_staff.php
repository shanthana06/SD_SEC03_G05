<?php
include 'db.php';
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    
    $res = mysqli_query($conn, "SELECT image FROM menu_items WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['image']) {
        @unlink("uploads/" . $row['image']); 
    }

    
    mysqli_query($conn, "DELETE FROM menu_items WHERE id=$id");

    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    echo "No menu item selected to delete.";
}
?>


