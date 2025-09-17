<?php
include 'db.php';
$result = mysqli_query($conn, "SELECT * FROM menu");
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
    echo '<a href="index.php" class="btn btn-warning" style="margin:10px;">⬅ Back to Home</a>';
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Staff Menu Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #fdfcf8; }
    .menu-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
  </style>
</head>
<body class="container mt-5">

  <h2 class="mb-4">Menu List</h2>
  <a href="add_menu_staff.php" class="btn btn-success mb-3">+ Add Menu</a>

  <table class="table table-bordered table-striped align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Price (RM)</th>
        <th>Description</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $row['id']; ?></td>
        <td>
          <?php if ($row['image']) { ?>
            <img src="uploads/<?= $row['image']; ?>" class="menu-img">
          <?php } else { ?>
            <span>No image</span>
          <?php } ?>
        </td>
        <td><?= $row['name']; ?></td>
        <td><?= $row['price']; ?></td>
        <td><?= $row['description']; ?></td>
        <td>
          <a href="edit_menu_staff.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="delete_menu_staff.php?id=<?= $row['id']; ?>" 
             class="btn btn-danger btn-sm"
             onclick="return confirm('Are you sure to delete this item?');">Delete</a>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>

</body>
</html>
