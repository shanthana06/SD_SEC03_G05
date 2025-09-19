<?php
session_start();
include 'db.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle file upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = "uploads/" . $imageName;

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Menu | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
    }

    /* Background blur */
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

    /* Form container */
    .form-box {
      max-width: 500px;
      margin: 80px auto;
      background: rgba(255, 245, 245, 1);
      backdrop-filter: blur(6px);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }

    .form-box h2 {
      text-align: center;
      margin-bottom: 25px;
      font-weight: 600;
    }

    /* Buttons */
    .black-btn {
      background-color: rgba(0,0,0,0.6);
      color: #fff;
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 10px;
      transition: all 0.3s ease;
      padding: 8px 20px;
    }

    .black-btn:hover {
      background-color: rgba(0,0,0,0.85);
      color: #f1f1f1;
      border-color: rgba(255,255,255,0.4);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="bg-blur"></div>

<div class="form-box">
  <h2> Add Menu Item</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Price (RM)</label>
      <input type="number" step="0.01" name="price" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Upload Image</label>
      <input type="file" name="image" class="form-control">
    </div>
    <div class="d-flex gap-2 justify-content-center">
      <button type="submit" class="black-btn">Add</button>
      <a href="menu_list_staff.php" class="black-btn">Cancel</a>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
