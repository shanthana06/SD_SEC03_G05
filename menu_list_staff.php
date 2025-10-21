<?php
include 'db.php';
session_start();

// Restrict access to staff and admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    header("Location: login.php");
    exit;
}

// Handle add new menu item
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

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

    $sql = "INSERT INTO menu_items (name, price, description, image, category) 
            VALUES ('$name', '$price', '$description', " . 
            ($imageName ? "'$imageName'" : "NULL") . ", '$category')";

    if (mysqli_query($conn, $sql)) {
        header("Location: menu_list_staff.php?msg=added");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
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
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    mysqli_query($conn, "UPDATE menu_items SET name='$name', price='$price', description='$description', category='$category' WHERE id=$id");
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Elegant Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #1a1a1a;
      --secondary-color: #4a4a4a;
      --accent-color: #d4af37;
      --light-bg: #f9f9f9;
      --border-color: #e8e8e8;
    }
    
    body {
      background: var(--light-bg);
      font-family: 'Cormorant Garamond', serif;
      color: var(--primary-color);
      line-height: 1.6;
      min-height: 100vh;
    }
    
    .page-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .page-header {
      text-align: center;
      margin-bottom: 50px;
      padding-bottom: 20px;
    }
    
    .page-title {
      font-family: 'Playfair Display', serif;
      font-weight: 500;
      font-size: 3.2rem;
      letter-spacing: -0.5px;
      margin-bottom: 15px;
      color: #2c2c2c;
    }
    
    .page-subtitle {
      color: var(--secondary-color);
      font-weight: 300;
      font-size: 1.3rem;
      max-width: 600px;
      margin: 0 auto;
      font-family: 'Lora', serif;
    }
    
    .menu-container {
      background-color: #fff;
      border-radius: 8px;
      padding: 40px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.05);
      border: 1px solid var(--border-color);
      margin-bottom: 40px;
    }
    
    .add-form-container {
      background-color: #fff;
      border-radius: 8px;
      padding: 40px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.05);
      border: 1px solid var(--border-color);
      margin-bottom: 40px;
    }
    
    .section-title {
      font-family: 'Playfair Display', serif;
      font-weight: 500;
      font-size: 2rem;
      margin-bottom: 25px;
      color: #2c2c2c;
      border-bottom: 1px solid var(--border-color);
      padding-bottom: 10px;
    }
    
    .menu-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
    }
    
    .table th {
      font-weight: 600;
      color: var(--secondary-color);
      border-bottom: 1px solid var(--border-color);
      padding: 15px 10px;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-family: 'Lora', serif;
    }
    
    .table td {
      padding: 20px 10px;
      vertical-align: middle;
      border-bottom: 1px solid #f5f5f5;
      font-size: 1.1rem;
    }
    
    .btn-custom {
      padding: 8px 16px;
      border-radius: 4px;
      font-weight: 500;
      font-size: 0.9rem;
      transition: all 0.2s ease;
      border: none;
      text-decoration: none;
      display: inline-block;
      font-family: 'Lora', serif;
    }
    
    .btn-edit {
      background-color: transparent;
      color: var(--primary-color);
      border: 1px solid var(--border-color);
    }
    
    .btn-edit:hover {
      background-color: #f5f5f5;
    }
    
    .btn-delete {
      background-color: transparent;
      color: #dc3545;
      border: 1px solid #f8d7da;
    }
    
    .btn-delete:hover {
      background-color: #dc3545;
      color: white;
    }
    
    .btn-save {
      background-color: var(--primary-color);
      color: #fff;
    }
    
    .btn-save:hover {
      background-color: #333;
    }
    
    .btn-cancel {
      background-color: transparent;
      color: var(--secondary-color);
      border: 1px solid var(--border-color);
    }
    
    .btn-cancel:hover {
      background-color: #f5f5f5;
    }
    
    .btn-primary-custom {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 10px 20px;
      font-family: 'Lora', serif;
    }
    
    .btn-primary-custom:hover {
      background-color: #333;
    }
    
    .btn-secondary-custom {
      background-color: transparent;
      color: var(--primary-color);
      border: 1px solid var(--border-color);
      padding: 10px 20px;
      font-family: 'Lora', serif;
    }
    
    .btn-secondary-custom:hover {
      background-color: #f5f5f5;
    }
    
    .action-buttons {
      display: flex;
      gap: 8px;
      justify-content: center;
    }
    
    .alert {
      border-radius: 4px;
      border: none;
      padding: 12px 20px;
      margin-bottom: 25px;
      font-family: 'Lora', serif;
    }
    
    .alert-success {
      background-color: #f0f9f4;
      color: #0f5132;
      border-left: 4px solid #0f5132;
    }
    
    .alert-danger {
      background-color: #fdf2f2;
      color: #842029;
      border-left: 4px solid #842029;
    }
    
    .form-control {
      border-radius: 4px;
      border: 1px solid var(--border-color);
      padding: 8px 12px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
    }
    
    .form-control:focus {
      box-shadow: 0 0 0 2px rgba(26, 26, 26, 0.1);
      border-color: var(--primary-color);
    }
    
    .form-label {
      font-family: 'Lora', serif;
      font-weight: 500;
      margin-bottom: 8px;
    }
    
    .table-responsive {
      border-radius: 8px;
      overflow: hidden;
    }
    
    .table {
      margin-bottom: 0;
    }
    
    .table-hover tbody tr:hover {
      background-color: rgba(0,0,0,0.02);
    }
    
    .action-header {
      text-align: center;
    }
    
    .toggle-form-btn {
      background-color: transparent;
      color: var(--primary-color);
      border: 1px solid var(--border-color);
      padding: 10px 20px;
      font-family: 'Lora', serif;
      width: 100%;
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }
    
    .toggle-form-btn:hover {
      background-color: #f5f5f5;
    }
    
    .add-form {
      display: none;
      animation: fadeIn 0.5s ease;
    }

    /* Category Button Group - Simple addition */
    .category-btn-group {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }
    
    .category-btn {
      padding: 10px 20px;
      border: 1px solid var(--border-color);
      background: white;
      border-radius: 4px;
      font-family: 'Lora', serif;
      font-weight: 500;
      transition: all 0.2s ease;
      cursor: pointer;
      flex: 1;
    }
    
    .category-btn:hover {
      background-color: #f5f5f5;
    }
    
    .category-btn.active {
      background-color: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
    }
    
    .category-badge {
      display: inline-block;
      padding: 4px 12px;
      background-color: #f8f9fa;
      color: var(--secondary-color);
      border-radius: 4px;
      font-size: 0.85rem;
      font-weight: 500;
      border: 1px solid var(--border-color);
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
      .page-container {
        padding: 20px 15px;
      }
      
      .menu-container, .add-form-container {
        padding: 25px 20px;
      }
      
      .page-title {
        font-size: 2.5rem;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .btn-custom {
        text-align: center;
      }
      
      .category-btn-group {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-container">
  <!-- Add New Item Form Section -->
  <div class="add-form-container">
    <button class="toggle-form-btn" id="toggleFormBtn">+ Add New Menu Item</button>
    
    <div class="add-form" id="addForm">
      <h3 class="section-title">Add New Item</h3>
      
      <form method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="col-md-6">
            <label for="price" class="form-label">Price (RM)</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
          </div>
        </div>
        
        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        
        <!-- Category Selection Buttons - Simple addition -->
        <div class="mb-3">
          <label class="form-label">Category</label>
          <div class="category-btn-group">
            <button type="button" class="category-btn active" data-category="coffee">Coffee</button>
            <button type="button" class="category-btn" data-category="food">Food</button>
            <button type="button" class="category-btn" data-category="dessert">Dessert</button>
          </div>
          <input type="hidden" id="category" name="category" value="coffee" required>
        </div>
        
        <div class="mb-4">
          <label for="image" class="form-label">Item Image</label>
          <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>
        
        <div class="d-flex gap-2">
          <button type="submit" name="add" class="btn btn-primary-custom">Add Item</button>
          <button type="button" class="btn btn-secondary-custom" id="cancelAddBtn">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Menu Items Table Section -->
  <div class="menu-container">
    <h3 class="section-title">Current Menu Items</h3>
    
    <!-- Success Messages -->
    <?php if (isset($_GET['msg'])): ?>
      <?php if ($_GET['msg'] === 'added'): ?>
        <div class="alert alert-success text-center">Menu item added successfully</div>
      <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="alert alert-success text-center">Menu item updated successfully</div>
      <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-danger text-center">Menu item deleted successfully</div>
      <?php endif; ?>
    <?php endif; ?>

    <div class="mb-4 text-center">
      <a href="menu.php" class="btn btn-secondary-custom btn-custom">View Public Menu</a>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price (RM)</th>
            <th>Description</th>
            <th>Category</th>
            <th class="action-header">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php 
        // Reset result pointer to start
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) { 
          $category = isset($row['category']) ? $row['category'] : 'coffee';
        ?>
          <tr>
            <td><?= $row['id']; ?></td>
            <td>
              <?php if ($row['image']) { ?>
                <img src="uploads/<?= $row['image']; ?>" class="menu-img">
              <?php } else { ?>
                <span class="text-muted">No image</span>
              <?php } ?>
            </td>

            <?php if ($row['id'] == $edit_id) { ?>
              <!-- Edit Mode -->
              <form method="POST">
                <td><input type="text" name="name" value="<?= $row['name']; ?>" class="form-control" required></td>
                <td><input type="number" step="0.01" name="price" value="<?= $row['price']; ?>" class="form-control" required></td>
                <td><input type="text" name="description" value="<?= $row['description']; ?>" class="form-control" required></td>
                <td>
                  <select name="category" class="form-control" required>
                    <option value="coffee" <?= $category == 'coffee' ? 'selected' : '' ?>>Coffee</option>
                    <option value="food" <?= $category == 'food' ? 'selected' : '' ?>>Food</option>
                    <option value="dessert" <?= $category == 'dessert' ? 'selected' : '' ?>>Dessert</option>
                  </select>
                </td>
                <td>
                  <div class="action-buttons">
                    <button type="submit" name="update" class="btn btn-save btn-custom">Save</button>
                    <a href="menu_list_staff.php" class="btn btn-cancel btn-custom">Cancel</a>
                  </div>
                </td>
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
              </form>
            <?php } else { ?>
              <!-- View Mode -->
              <td><?= htmlspecialchars($row['name']); ?></td>
              <td><?= number_format($row['price'], 2); ?></td>
              <td><?= htmlspecialchars($row['description']); ?></td>
              <td>
                <span class="category-badge"><?= ucfirst($category) ?></span>
              </td>
              <td>
                <div class="action-buttons">
                  <a href="?edit=<?= $row['id']; ?>" class="btn btn-edit btn-custom">Edit</a>
                  <a href="?delete=<?= $row['id']; ?>" 
                     class="btn btn-delete btn-custom"
                     onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                </div>
              </td>
            <?php } ?>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // Toggle add form visibility
  document.getElementById('toggleFormBtn').addEventListener('click', function() {
    const form = document.getElementById('addForm');
    const isVisible = form.style.display === 'block';
    
    form.style.display = isVisible ? 'none' : 'block';
    this.textContent = isVisible ? '+ Add New Menu Item' : '- Hide Form';
  });
  
  // Cancel add form
  document.getElementById('cancelAddBtn').addEventListener('click', function() {
    document.getElementById('addForm').style.display = 'none';
    document.getElementById('toggleFormBtn').textContent = '+ Add New Menu Item';
  });

  // Category button selection - Simple addition
  document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      // Remove active class from all buttons
      document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
      
      // Add active class to clicked button
      this.classList.add('active');
      
      // Update hidden input value
      document.getElementById('category').value = this.dataset.category;
    });
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>