<?php
session_start();
include 'db.php';

// --- Access Control (Admin Only) ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  echo '<div class="text-center mt-5">⚠ Access denied. Admin only.</div>';
  exit;
}

// --- Add Staff ---
if (isset($_POST['add_staff'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = 'staff';

  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, is_verified) VALUES (?, ?, ?, ?, 1)");
  $stmt->bind_param("ssss", $name, $email, $password, $role);
  
  if ($stmt->execute()) {
    $_SESSION['success_message'] = "Staff member added successfully!";
  } else {
    $_SESSION['error_message'] = "Error adding staff member: " . $conn->error;
  }
  $stmt->close();
  header("Location: manage_staff.php");
  exit;
}

// --- Edit Staff ---
if (isset($_POST['edit_staff'])) {
  $id = $_POST['staff_id'];
  $name = $_POST['name'];
  $email = $_POST['email'];

  $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE user_id=?");
  $stmt->bind_param("ssi", $name, $email, $id);
  
  if ($stmt->execute()) {
    $_SESSION['success_message'] = "Staff member updated successfully!";
  } else {
    $_SESSION['error_message'] = "Error updating staff member: " . $conn->error;
  }
  $stmt->close();
  header("Location: manage_staff.php");
  exit;
}

// --- Delete Staff ---
if (isset($_GET['delete_id'])) {
  $id = $_GET['delete_id'];
  $stmt = $conn->prepare("DELETE FROM users WHERE user_id=? AND role='staff'");
  $stmt->bind_param("i", $id);
  
  if ($stmt->execute()) {
    $_SESSION['success_message'] = "Staff member deleted successfully!";
  } else {
    $_SESSION['error_message'] = "Error deleting staff member: " . $conn->error;
  }
  $stmt->close();
  header("Location: manage_staff.php");
  exit;
}

// --- Fetch Staff List ---
$result = $conn->query("SELECT user_id, name, email, created_at FROM users WHERE role='staff' ORDER BY created_at DESC");

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Management | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
    
    .staff-container {
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
      animation: fadeIn 0.5s ease;
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
    
    .staff-count {
      font-family: 'Lora', serif;
      color: var(--secondary-color);
      margin-bottom: 20px;
      font-size: 1.1rem;
    }
    
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
    
    .password-note {
      font-size: 0.9rem;
      color: var(--secondary-color);
      font-style: italic;
      margin-top: 5px;
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--secondary-color);
    }
    
    .empty-state-icon {
      font-size: 3rem;
      margin-bottom: 20px;
      opacity: 0.5;
    }
    
    .empty-state-text {
      font-size: 1.2rem;
      font-family: 'Lora', serif;
    }
    
    .date-cell {
      white-space: nowrap;
    }
    
    /* Modal styling */
    .modal-content {
      border-radius: 8px;
      border: 1px solid var(--border-color);
    }
    
    .modal-header {
      border-bottom: 1px solid var(--border-color);
      font-family: 'Playfair Display', serif;
    }
    
    .modal-footer {
      border-top: 1px solid var(--border-color);
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
      .page-container {
        padding: 20px 15px;
      }
      
      .staff-container, .add-form-container {
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
      
      .table-responsive {
        font-size: 0.9rem;
      }
    }
    
    /* Animation for new rows */
    .fade-in {
      animation: fadeInUp 0.6s ease forwards;
      opacity: 0;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>


<div class="page-container">
 

  <!-- Success/Error Messages -->
  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>
      <?= $_SESSION['success_message'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <?= $_SESSION['error_message'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
  <?php endif; ?>

  <!-- Add New Staff Form Section -->
  <div class="add-form-container">
    <h3 class="section-title">Add New Staff Member</h3>
    
    <form method="POST">
      <div class="row mb-3">
        <div class="col-md-4">
          <label for="name" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="col-md-4">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="col-md-4">
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
      </div>
      
      <div class="d-flex gap-2">
        <button type="submit" name="add_staff" class="btn btn-primary-custom">Add Staff Member</button>
      </div>
    </form>
  </div>

  <!-- Staff List Table Section -->
  <div class="staff-container">
    <h3 class="section-title">Staff List</h3>
    
    <?php
    $staff_count = $result->num_rows;
    $result->data_seek(0); // Reset result pointer
    ?>
    <div class="staff-count">
      Total Staff Members: <?= $staff_count ?>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
            <th class="action-header">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($staff_count > 0): ?>
            <?php while ($staff = $result->fetch_assoc()): ?>
              <tr class="fade-in">
                <td class="text-center"><?= $staff['user_id'] ?></td>
                <td><?= htmlspecialchars($staff['name']) ?></td>
                <td><?= htmlspecialchars($staff['email']) ?></td>
                <td class="date-cell"><?= date("M j, Y g:i A", strtotime($staff['created_at'])) ?></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-edit btn-custom" data-bs-toggle="modal" data-bs-target="#editModal<?= $staff['user_id'] ?>">
                      <i class="bi bi-pencil-square me-1"></i>Edit
                    </button>
                    <a href="?delete_id=<?= $staff['user_id'] ?>" class="btn btn-delete btn-custom" onclick="return confirm('Are you sure you want to delete this staff member?');">
                      <i class="bi bi-trash me-1"></i>Delete
                    </a>
                  </div>
                </td>
              </tr>

              <!-- Edit Modal -->
              <div class="modal fade" id="editModal<?= $staff['user_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Staff Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="staff_id" value="<?= $staff['user_id'] ?>">
                        <div class="mb-3">
                          <label class="form-label">Name</label>
                          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($staff['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Email</label>
                          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($staff['email']) ?>" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_staff" class="btn btn-primary-custom">Save Changes</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="empty-state">
                <div class="empty-state-icon">👥</div>
                <div class="empty-state-text">No staff members yet</div>
                <p class="text-muted mt-2">Add staff members using the form above to get started.</p>
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
// Password toggle functionality
document.getElementById('passwordToggle').addEventListener('click', function() {
  const passwordField = document.getElementById('password');
  const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordField.setAttribute('type', type);
  this.classList.toggle('show');
});

// Add animation delays for rows
document.addEventListener('DOMContentLoaded', function() {
  const rows = document.querySelectorAll('.table tbody tr');
  rows.forEach((row, index) => {
    row.style.animationDelay = `${index * 0.1}s`;
  });
});

// Auto-dismiss alerts after 5 seconds
setTimeout(() => {
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    const bsAlert = new bootstrap.Alert(alert);
    bsAlert.close();
  });
}, 5000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>