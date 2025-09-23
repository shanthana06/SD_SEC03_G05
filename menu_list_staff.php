<?php
include 'db.php';
session_start();

// Restrict access to staff and admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    header("Location: login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM menu_items WHERE id = $delete_id");
    header("Location: menu_list_staff.php?msg=deleted");
    exit;
}

// Handle edit/update request
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;

if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    mysqli_query($conn, "UPDATE menu_items SET name='$name', price='$price', description='$description' WHERE id=$id");
    header("Location: menu_list_staff.php?msg=updated");
    exit;
}

// Fetch menu items
$result = mysqli_query($conn, "SELECT * FROM menu_items ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Menu Management | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .menu-container {
      background-color: #fff;
      border-radius: 12px;
      padding: 30px;
      margin: 50px auto;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .menu-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }
    .btn-custom {
      padding: 6px 14px;
      border-radius: 8px;
      font-weight: 500;
    }
    .btn-edit {
      background-color: #ffc107;
      color: #000;
    }
    .btn-delete {
      background-color: #dc3545;
      color: #fff;
    }
    .btn-save {
      background-color: #198754;
      color: #fff;
    }
    .btn-cancel {
      background-color: #6c757d;
      color: #fff;
    }
    .bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px);
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="bg-blur"></div>

<div class="container">
  <div class="menu-container">
    <h2 class="text-center mb-4">Menu Management</h2>

    <!-- Success Messages -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
      <div class="alert alert-success text-center">✅ Menu updated successfully</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="alert alert-danger text-center">🗑 Menu deleted successfully</div>
    <?php endif; ?>

    <div class="mb-3 text-center">
      <a href="add_menu_staff.php" class="btn btn-dark btn-custom me-2">➕ Add Menu</a>
      <a href="menu.php" class="btn btn-secondary btn-custom">📋 View Menu</a>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price (RM)</th>
            <th>Description</th>
            <th>Actions</th>
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

            <?php if ($row['id'] == $edit_id) { ?>
              <!-- Edit Mode -->
              <form method="POST">
                <td><input type="text" name="name" value="<?= $row['name']; ?>" class="form-control" required></td>
                <td><input type="number" step="0.01" name="price" value="<?= $row['price']; ?>" class="form-control" required></td>
                <td><input type="text" name="description" value="<?= $row['description']; ?>" class="form-control" required></td>
                <td>
                  <button type="submit" name="update" class="btn btn-save btn-sm btn-custom">💾 Save</button>
                  <a href="menu_list_staff.php" class="btn btn-cancel btn-sm btn-custom">❌ Cancel</a>
                </td>
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
              </form>
            <?php } else { ?>
              <!-- View Mode -->
              <td><?= htmlspecialchars($row['name']); ?></td>
              <td><?= number_format($row['price'], 2); ?></td>
              <td><?= htmlspecialchars($row['description']); ?></td>
              <td>
                <a href="?edit=<?= $row['id']; ?>" class="btn btn-edit btn-sm btn-custom me-1">✏ Edit</a>
                <a href="?delete=<?= $row['id']; ?>" 
                   class="btn btn-delete btn-sm btn-custom"
                   onclick="return confirm('Are you sure you want to delete this item?');">🗑 Delete</a>
              </td>
            <?php } ?>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>