<?php
session_start();
include 'db.php';
include 'navbar.php';
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
  $stmt->execute();
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
  $stmt->execute();
  $stmt->close();
  header("Location: manage_staff.php");
  exit;
}

// --- Delete Staff ---
if (isset($_GET['delete_id'])) {
  $id = $_GET['delete_id'];
  $stmt = $conn->prepare("DELETE FROM users WHERE user_id=? AND role='staff'");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  header("Location: manage_staff.php");
  exit;
}

// --- Fetch Staff List ---
$result = $conn->query("SELECT user_id, name, email, created_at FROM users WHERE role='staff' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Staff | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8f5f2;
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

    /* Background blur */
    .cart-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px) brightness(0.85);
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
    }

    .dashboard-container {
      background-color: rgba(255,255,255,0.95);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: -260px;
      width: 240px;
      height: 100%;
      background-color: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 15px;
      box-shadow: 2px 0 15px rgba(0,0,0,0.1);
      border-right: 1px solid rgba(200,180,160,0.3);
      z-index: 100;
      transition: all 0.3s ease;
    }
    .sidebar.show { left: 0; }

    .sidebar a {
      display: block;
      padding: 12px 15px;
      border-radius: 12px;
      text-align: center;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      color: #5C4033;
      background-color: #FDFCF9;
    }
    .sidebar a:hover {
      background-color: #D2B48C;
      color: #fff;
      transform: translateY(-2px);
    }

    .toggle-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 200;
      background-color: rgba(255,255,255,0.95);
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      cursor: pointer;
      transition: all 0.3s ease;
      color: #5C4033;
      font-weight: bold;
    }
    .toggle-btn:hover { background-color: rgba(255,255,255,1); transform: scale(1.05); }

    /* Add Staff Button */
    .btn-brown {
      background-color: #8B5E3C;
      color: white;
      border: none;
      border-radius: 8px;
      transition: 0.3s ease;
    }
    .btn-brown:hover {
      background-color: #6E4424;
      color: #fff;
    }

    /* Aesthetic Edit & Delete Buttons */
    .edit-btn {
      background-color: #c8a97e;
      color: #fff;
      border: none;
      border-radius: 20px;
      transition: 0.3s ease;
    }
    .edit-btn:hover {
      background-color: #b2875e;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .delete-btn {
      background-color: #8b5e3c;
      color: #fff;
      border: none;
      border-radius: 20px;
      transition: 0.3s ease;
    }
    .delete-btn:hover {
      background-color: #6f4628;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .table th {
      background-color: #f3e9dd;
      color: #5C4033;
    }
    .table td { vertical-align: middle; }
  </style>
</head>

<body>
  <div class="cart-bg-blur"></div>

  <button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

  <div class="sidebar" id="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="add_menu_staff.php">Add Menu</a>
    <a href="menu_list_staff.php">Edit Menu</a>
    <a href="customer_list.php">Customers</a>
    <a href="view_feedback.php">Feedback</a>
    <a href="vieworders.php">Orders</a>
    <a href="manage_staff.php">Manage Staff</a>
  </div>

  <div class="container mt-5">
    <div class="dashboard-container">
      <h2 class="text-center mb-4"> Manage Staff</h2>

      <!-- Add Staff Form -->
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light fw-bold">Add New Staff</div>
        <div class="card-body">
          <form method="POST">
            <div class="row g-3">
              <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
              </div>
              <div class="col-md-4">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
              </div>
              <div class="col-md-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
              </div>
              <div class="col-md-1 d-grid">
                <button type="submit" name="add_staff" class="btn btn-brown">Add</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Staff List -->
      <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">Staff List</div>
        <div class="card-body">
          <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($staff = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $staff['user_id'] ?></td>
                    <td><?= htmlspecialchars($staff['name']) ?></td>
                    <td><?= htmlspecialchars($staff['email']) ?></td>
                    <td><?= $staff['created_at'] ?></td>
                    <td>
                      <button class="btn btn-sm edit-btn px-3 py-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $staff['user_id'] ?>">
                        <i class="bi bi-pencil-square"></i> Edit
                      </button>
                      <a href="?delete_id=<?= $staff['user_id'] ?>" class="btn btn-sm delete-btn px-3 py-1" onclick="return confirm('Are you sure you want to delete this staff?');">
                        <i class="bi bi-trash"></i> Delete
                      </a>
                    </td>
                  </tr>

                  <!-- Edit Modal -->
                  <div class="modal fade" id="editModal<?= $staff['user_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <form method="POST">
                          <div class="modal-header">
                            <h5 class="modal-title">Edit Staff</h5>
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
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="edit_staff" class="btn btn-brown">Save Changes</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="5">No staff found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('show');
    }
  </script>
</body>
</html>
