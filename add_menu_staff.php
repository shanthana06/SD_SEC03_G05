<?php
session_start();
include 'db.php';

// Show back button only if staff is logged in
if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
    echo '<a href="index.php" class="btn btn-warning" style="margin:10px;">⬅ Back to Home</a>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle file upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = "uploads/" . $imageName;

        // Create uploads folder if not exist
        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // ✅ SQL insert query (fixed)
    $sql = "INSERT INTO menu_items (name, price, description, image) 
            VALUES ('$name', '$price', '$description', " . 
            ($imageName ? "'$imageName'" : "NULL") . ")";

    if (mysqli_query($conn, $sql)) {
        header("Location: menu_list_staff.php?msg=added");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

  <h2>Add Menu Item</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Price (RM)</label>
      <input type="number" step="0.01" name="price" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label>Upload Image</label>
      <input type="file" name="image" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Add</button>
    <a href="menu_list_staff.php" class="btn btn-secondary">Cancel</a>
  </form>

</body>
</html>
