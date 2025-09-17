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
$query = mysqli_query($conn, "SELECT * FROM menu WHERE id=$id");
$menu = mysqli_fetch_assoc($query);
if (!$menu) { 
    die("Menu item not found."); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // Keep old image if no new one uploaded
    $imageName = $menu['image']; 
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = "uploads/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $update = "UPDATE menu 
               SET name='$name', price='$price', category='$category', description='$description', image='$imageName' 
               WHERE id='$id'";

    if (mysqli_query($conn, $update)) {
        header("Location: menu_list_staff.php?msg=updated");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Edit Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.menu-preview{width:100px; height:100px; object-fit:cover; margin-top:10px;}</style>
</head>
<body class="container mt-5">

  <h2>Edit Menu Item</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" value="<?= $menu['name']; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Price (RM)</label>
      <input type="number" step="0.01" name="price" value="<?= $menu['price']; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?= $menu['description']; ?></textarea>
    </div>
    <div class="mb-3">
      <label>Change Image</label>
      <input type="file" name="image" class="form-control">
      <?php if ($menu['image']) { ?>
        <img src="uploads/<?= $menu['image']; ?>" class="menu-preview">
      <?php } ?>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="menu_list_staff.php" class="btn btn-secondary">Cancel</a>
  </form>

</body>
</html>
