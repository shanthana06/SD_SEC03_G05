<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

// Handle inline update
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    mysqli_query($conn, "UPDATE menu_items SET name='$name', price='$price', description='$description' WHERE id=$id");
}

// Handle delete directly on this page
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // Optional: delete image file
    $res = mysqli_query($conn, "SELECT image FROM menu_items WHERE id=$delete_id");
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['image']) {
        @unlink("uploads/" . $row['image']);
    }

    mysqli_query($conn, "DELETE FROM menu_items WHERE id=$delete_id");
}

// Fetch updated menu items
$result = mysqli_query($conn, "SELECT * FROM menu_items");

// Check edit mode
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Staff Menu Management | Arjuna n Co-ffee</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .menu-container {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      padding: 30px;
      margin: 40px auto;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    .menu-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
    }
    .black-btn {
      background-color: rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.2); 
      color: #fff;
      backdrop-filter: blur(6px);
      padding: 8px 20px;
      border-radius: 12px;
      transition: all 0.3s ease;
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    .black-btn:hover {
      background-color: rgba(0, 0, 0, 0.7);
      border-color: rgba(255, 255, 255, 0.4);
      color: #f1f1f1;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .cart-bg-blur {
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

<!-- Blurred coffee background -->
<div class="cart-bg-blur"></div>

<div class="container">
  <div class="menu-container">
    <h2 class="mb-4 text-center">Staff Menu Management</h2>

    <div class="mb-3 d-flex flex-wrap gap-2 justify-content-center">
      <a href="add_menu_staff.php" class="btn black-btn">Add Menu</a>
      <a href="menu.php" class="btn black-btn">Next to Menu</a>
      <a href="staff_dashboard.php" class="btn black-btn">Back to Staff Dashboard</a>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark text-center">
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

            <?php if ($row['id'] == $edit_id) { ?>
              <!-- EDIT MODE -->
              <form method="POST">
                <td><input type="text" name="name" value="<?= $row['name']; ?>" class="form-control"></td>
                <td><input type="number" step="0.01" name="price" value="<?= $row['price']; ?>" class="form-control"></td>
                <td><input type="text" name="description" value="<?= $row['description']; ?>" class="form-control"></td>
                <td>
                  <button type="submit" name="update" class="btn btn-success btn-sm">Save</button>
                  <a href="staff_menu_management.php" class="btn btn-secondary btn-sm">Cancel</a>
                </td>
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
              </form>
            <?php } else { ?>
              <!-- NORMAL VIEW MODE -->
              <td><?= $row['name']; ?></td>
              <td><?= $row['price']; ?></td>
              <td><?= $row['description']; ?></td>
              <td class="text-center">
                <a href="?edit=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?delete=<?= $row['id']; ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure to delete this item?');">Delete</a>
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
</html>
