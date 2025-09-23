<?php 
session_start();
include 'db.php';

// Only staff or admin can access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    header("Location: login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE user_id = $id AND role='customer'");
    header("Location: customer_list.php?msg=deleted");
    exit;
}

// Fetch all customers
$result = mysqli_query($conn, "SELECT user_id, name, email FROM users WHERE role='customer'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer List (Staff)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      position: relative;
      overflow: hidden;
    }
    .bg-blur {
      background: url('images/coffee1.jpg') no-repeat center center fixed;
      background-size: cover;
      filter: blur(8px);
      -webkit-filter: blur(8px);
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
    }
    .container-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.3);
      width: 100%;
    }
    h2 {
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      text-align: center;
      margin-bottom: 25px;
      font-size: 2rem;
      color: #3e2723;
    }
    table {
      font-size: 0.95rem;
    }
    table th {
      font-weight: 500;
    }

    /* Coffee aesthetic buttons */
    .btn-coffee {
      border: none;
      border-radius: 8px;
      padding: 6px 14px;
      font-weight: 500;
      transition: 0.3s ease;
    }
    .btn-edit {
      background-color: #C8A27C; color: #fff;
    }
    .btn-edit:hover {
      background-color: #b38c66; color: #fff;
    }
    .btn-delete {
      background-color: #8B5E3C; color: #fff;
    }
    .btn-delete:hover {
      background-color: #6f4a2d; color: #fff;
    }
    .btn-back {
      background-color: #D9B99B; color: #4B2E2E;
    }
    .btn-back:hover {
      background-color: #c9a887; color: #fff;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="bg-blur"></div>

<div class="container-md my-5">
  <div class="container-box">
    <h2>Customer List (Staff)</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
      <div class="alert alert-success text-center">Customer updated successfully!</div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
      <div class="alert alert-danger text-center">🗑 Customer deleted successfully!</div>
    <?php endif; ?>

    <table class="table table-hover table-bordered text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['user_id']; ?></td>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td>
              <a href="edit_customer.php?id=<?= $row['user_id']; ?>" class="btn btn-sm btn-coffee btn-edit">✏ Edit</a>
              <a href="customer_list.php?delete=<?= $row['user_id']; ?>" 
                 onclick="return confirm('Delete this customer?');" 
                 class="btn btn-sm btn-coffee btn-delete">🗑 Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="text-center mt-3">
      <a href="staff_dashboard.php" class="btn btn-coffee btn-back">⬅ Back to Dashboard</a>
    </div>
  </div>
</div>

</body>
</html>
