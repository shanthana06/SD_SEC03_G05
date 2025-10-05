<?php
// Start session only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php'; // database connection


if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'staff' && $_SESSION['role'] != 'admin')) {
    header("Location: login.php");
    exit();
}

// Fetch feedback from contacts
$sql = "SELECT id, fullname, email, message, created_at 
        FROM contacts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Feedback | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      color: #333;
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

    /* Feedback box */
    .form-box {
      max-width: 800px;
      margin: 80px auto;
      background: rgba(255, 255, 255, 1);
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

    /* Table styling */
    .table thead {
      background-color: rgba(0,0,0,0.7);
      color: #fff;
    }
    .table td, .table th {
      vertical-align: middle;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="bg-blur"></div>

<div class="form-box">
  <h2>Customer Feedback</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr class="text-center">
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Message</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td class="text-center"><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['fullname']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
              <td class="text-center"><?= $row['created_at'] ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">No feedback yet.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
