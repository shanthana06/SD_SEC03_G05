<?php 
session_start();
include 'db.php';

// Only staff or admin can access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    header("Location: login.php");
    exit;
}

// Handle add new customer
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'customer')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: customer_list.php?msg=added");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE user_id = $id AND role='customer'");
    header("Location: customer_list.php?msg=deleted");
    exit;
}

// Handle edit/update request
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;

if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // If password is provided, update it
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name='$name', email='$email', password='$password' WHERE user_id=$id";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email' WHERE user_id=$id";
    }
    
    mysqli_query($conn, $sql);
    header("Location: customer_list.php?msg=updated");
    exit;
}

// Fetch customer data for editing
$edit_customer = null;
if ($edit_id > 0) {
    $edit_result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $edit_id");
    $edit_customer = mysqli_fetch_assoc($edit_result);
}

// Fetch all customers
$result = mysqli_query($conn, "SELECT user_id, name, email FROM users WHERE role='customer' ORDER BY user_id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Management | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Elegant Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
      max-width: 1200px;
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
    
    .customer-container {
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
    
    .customer-count {
      font-family: 'Lora', serif;
      color: var(--secondary-color);
      margin-bottom: 20px;
      font-size: 1.1rem;
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
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .password-note {
      font-size: 0.9rem;
      color: var(--secondary-color);
      font-style: italic;
      margin-top: 5px;
    }
    
    /* Password toggle styles */
    .password-input-group {
      position: relative;
    }
    
    .password-toggle {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--secondary-color);
      cursor: pointer;
      padding: 5px;
      border-radius: 3px;
      transition: color 0.2s ease;
    }
    
    .password-toggle:hover {
      color: var(--primary-color);
      background-color: rgba(0,0,0,0.05);
    }
    
    .password-toggle .bi-eye-slash {
      display: none;
    }
    
    .password-toggle.show .bi-eye {
      display: none;
    }
    
    .password-toggle.show .bi-eye-slash {
      display: inline-block;
    }
    
    .password-field {
      padding-right: 40px !important;
    }
    
    .edit-password-group {
      position: relative;
      margin-top: 8px;
    }
    
    .edit-password-toggle {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--secondary-color);
      cursor: pointer;
      padding: 3px;
      border-radius: 3px;
      font-size: 0.9rem;
    }
    
    .edit-password-toggle:hover {
      color: var(--primary-color);
      background-color: rgba(0,0,0,0.05);
    }
    
    .edit-password-field {
      padding-right: 35px !important;
      font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
      .page-container {
        padding: 20px 15px;
      }
      
      .customer-container, .add-form-container {
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
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-container">
  

  <!-- Add New Customer Form Section -->
  <div class="add-form-container">
    <button class="toggle-form-btn" id="toggleFormBtn">+ Add New Customer</button>
    
    <div class="add-form" id="addForm">
      <h3 class="section-title">Add New Customer</h3>
      
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <div class="password-input-group">
            <input type="password" class="form-control password-field" id="password" name="password" required>
            <button type="button" class="password-toggle" id="passwordToggle">
              <i class="bi bi-eye"></i>
              <i class="bi bi-eye-slash"></i>
            </button>
          </div>
          <div class="password-note">Password will be securely encrypted</div>
        </div>
        
        <div class="d-flex gap-2">
          <button type="submit" name="add" class="btn btn-primary-custom">Add Customer</button>
          <button type="button" class="btn btn-secondary-custom" id="cancelAddBtn">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Customer List Table Section -->
  <div class="customer-container">
    <h3 class="section-title">Customer List</h3>
    
    <!-- Success Messages -->
    <?php if (isset($_GET['msg'])): ?>
      <?php if ($_GET['msg'] === 'added'): ?>
        <div class="alert alert-success text-center">Customer added successfully</div>
      <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="alert alert-success text-center">Customer updated successfully</div>
      <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-danger text-center">Customer deleted successfully</div>
      <?php endif; ?>
    <?php endif; ?>

    <?php
    $customer_count = mysqli_num_rows($result);
    mysqli_data_seek($result, 0); // Reset result pointer
    ?>
    <div class="customer-count">
      Total Customers: <?= $customer_count ?>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th class="action-header">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($customer_count > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $row['user_id']; ?></td>
                
                <?php if ($row['user_id'] == $edit_id && $edit_customer): ?>
                  <!-- Edit Mode -->
                  <form method="POST">
                    <td><input type="text" name="name" value="<?= $edit_customer['name']; ?>" class="form-control" required></td>
                    <td><input type="email" name="email" value="<?= $edit_customer['email']; ?>" class="form-control" required></td>
                    <td>
                      <div class="action-buttons">
                        <button type="submit" name="update" class="btn btn-save btn-custom">Save</button>
                        <a href="customer_list.php" class="btn btn-cancel btn-custom">Cancel</a>
                      </div>
                      <div class="edit-password-group">
                        <input type="password" name="password" placeholder="New password (optional)" class="form-control edit-password-field" id="editPassword<?= $edit_customer['user_id'] ?>">
                        <button type="button" class="edit-password-toggle" data-target="editPassword<?= $edit_customer['user_id'] ?>">
                          <i class="bi bi-eye"></i>
                        </button>
                        <div class="password-note">Leave blank to keep current password</div>
                      </div>
                    </td>
                    <input type="hidden" name="id" value="<?= $edit_customer['user_id']; ?>">
                  </form>
                <?php else: ?>
                  <!-- View Mode -->
                  <td><?= htmlspecialchars($row['name']); ?></td>
                  <td><?= htmlspecialchars($row['email']); ?></td>
                  <td>
                    <div class="action-buttons">
                      <a href="?edit=<?= $row['user_id']; ?>" class="btn btn-edit btn-custom">Edit</a>
                      <a href="?delete=<?= $row['user_id']; ?>" 
                         onclick="return confirm('Are you sure you want to delete this customer?');" 
                         class="btn btn-delete btn-custom">Delete</a>
                    </div>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center py-4">
                <p class="text-muted" style="font-size: 1.1rem;">No customers found</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <a href="staff_dashboard.php" class="btn btn-secondary-custom">← Back to Dashboard</a>
    </div>
  </div>
</div>

<script>
  // Toggle add form visibility
  document.getElementById('toggleFormBtn').addEventListener('click', function() {
    const form = document.getElementById('addForm');
    const isVisible = form.style.display === 'block';
    
    form.style.display = isVisible ? 'none' : 'block';
    this.textContent = isVisible ? '+ Add New Customer' : '- Hide Form';
  });
  
  // Cancel add form
  document.getElementById('cancelAddBtn').addEventListener('click', function() {
    document.getElementById('addForm').style.display = 'none';
    document.getElementById('toggleFormBtn').textContent = '+ Add New Customer';
  });
  
  // Password toggle for add form
  document.getElementById('passwordToggle').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.classList.toggle('show');
  });
  
  // Password toggle for edit forms
  document.querySelectorAll('.edit-password-toggle').forEach(button => {
    button.addEventListener('click', function() {
      const targetId = this.getAttribute('data-target');
      const passwordField = document.getElementById(targetId);
      const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordField.setAttribute('type', type);
      
      // Toggle eye icon
      const eyeIcon = this.querySelector('.bi-eye');
      const eyeSlashIcon = this.querySelector('.bi-eye-slash');
      
      if (eyeIcon && eyeSlashIcon) {
        if (type === 'text') {
          eyeIcon.style.display = 'none';
          eyeSlashIcon.style.display = 'inline-block';
        } else {
          eyeIcon.style.display = 'inline-block';
          eyeSlashIcon.style.display = 'none';
        }
      }
    });
  });
  
  // Auto-show form if there's an error
  <?php if (isset($error)): ?>
    document.getElementById('addForm').style.display = 'block';
    document.getElementById('toggleFormBtn').textContent = '- Hide Form';
  <?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>