<?php
session_start();
include 'db.php';

// --- Access control ---
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff','admin'])) {
    header("Location: login.php");
    exit;
}

// --- Get customer ID ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("No customer ID provided.");
}
$customer_id = intval($_GET['id']);

// --- Fetch customer ---
$result = mysqli_query($conn, "SELECT * FROM users WHERE user_id=$customer_id AND role='customer'");
$customer = mysqli_fetch_assoc($result);

if (!$customer) {
    die("Customer not found!");
}

$message = "";

// --- Handle form submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update
    if (isset($_POST['update'])) {
        $name  = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        $update = "UPDATE users SET name='$name', email='$email' WHERE user_id=$customer_id AND role='customer'";
        if (mysqli_query($conn, $update)) {
            $message = "<div class='alert alert-success text-center'>✅ Customer updated successfully</div>";
            $customer['name'] = $name;
            $customer['email'] = $email;
        } else {
            $message = "<div class='alert alert-danger text-center'>❌ Error updating: " . mysqli_error($conn) . "</div>";
        }
    }

    // Delete
    if (isset($_POST['delete'])) {
        $delete = "DELETE FROM users WHERE user_id=$customer_id AND role='customer'";
        if (mysqli_query($conn, $delete)) {
            $message = "<div class='alert alert-success text-center'>🗑️ Customer deleted successfully</div>";
            $customer = null;
        } else {
            $message = "<div class='alert alert-danger text-center'>❌ Error deleting: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Customer - Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
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
      max-width: 650px;
      margin: 80px auto;
    }
    h2 {
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      text-align: center;
      margin-bottom: 25px;
      color: #3e2723;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="bg-blur"></div>

<div class="container-box">
  <h2>Edit Customer</h2>
  <?= $message; ?>

  <?php if ($customer): ?>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']); ?>" required>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <!-- Back button -->
      <a href="customer_list.php" 
         class="btn" 
         style="background-color:#D9B99B; color:#4B2E2E; border:none; border-radius:8px; padding:8px 18px; font-weight:500;">
         ⬅ Back
      </a>

      <div>
        <!-- Save button -->
        <button type="submit" name="update" 
                class="btn" 
                style="background-color:#C8A27C; color:#fff; border:none; border-radius:8px; padding:8px 18px; font-weight:500;">
          ✏ Save
        </button>

        <!-- Delete button -->
        <button type="submit" name="delete" 
                class="btn" 
                style="background-color:#8B5E3C; color:#fff; border:none; border-radius:8px; padding:8px 18px; font-weight:500;"
                onclick="return confirm('Delete this customer?');">
          🗑 Delete
        </button>
      </div>
    </div>
  </form>
  <?php else: ?>
    <p class="text-center text-muted">No customer data available.</p>
  <?php endif; ?>
</div>
</body>
</html>
