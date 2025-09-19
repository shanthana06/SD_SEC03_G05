<?php
include 'db.php';
session_start();

// Only staff can delete
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

// Check if ID is passed
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Optional: delete the image file if exists
    $res = mysqli_query($conn, "SELECT image FROM menu_items WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['image']) {
        @unlink("uploads/" . $row['image']); // @ suppresses warning if file not found
    }

    // Delete menu item from database
    mysqli_query($conn, "DELETE FROM menu_items WHERE id=$id");

    // Redirect back to menu list page
    // Option 1: static redirect (replace with your actual menu page)
    // header("Location: staff_menu_management.php");

    // Option 2: dynamic redirect to the previous page (recommended)
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    echo "No menu item selected to delete.";
}
?>


