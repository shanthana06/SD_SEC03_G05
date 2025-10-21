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
    
    .feedback-container {
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
    
    .alert {
      border-radius: 4px;
      border: none;
      padding: 12px 20px;
      margin-bottom: 25px;
      font-family: 'Lora', serif;
    }
    
    .alert-info {
      background-color: #f0f9ff;
      color: #055160;
      border-left: 4px solid #055160;
    }
    
    .form-control {
      border-radius: 4px;
      border: 1px solid var(--border-color);
      padding: 8px 12px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
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
    
    .feedback-count {
      font-family: 'Lora', serif;
      color: var(--secondary-color);
      margin-bottom: 20px;
      font-size: 1.1rem;
    }
    
    .message-cell {
      max-width: 300px;
      word-wrap: break-word;
    }
    
    .email-cell {
      max-width: 200px;
      word-wrap: break-word;
    }
    
    .date-cell {
      white-space: nowrap;
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
    
    @media (max-width: 768px) {
      .page-container {
        padding: 20px 15px;
      }
      
      .feedback-container {
        padding: 25px 20px;
      }
      
      .page-title {
        font-size: 2.5rem;
      }
      
      .table-responsive {
        font-size: 0.9rem;
      }
      
      .message-cell {
        max-width: 150px;
      }
      
      .email-cell {
        max-width: 120px;
      }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-container">
  
  <div class="feedback-container">
    <h3 class="section-title">Customer Messages</h3>
    
    <?php
    $feedback_count = $result->num_rows;
    $result->data_seek(0); // Reset result pointer
    ?>
    <div class="feedback-count">
      Total Feedback Messages: <?= $feedback_count ?>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date Received</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($feedback_count > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td class="text-center"><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td class="email-cell"><?= htmlspecialchars($row['email']) ?></td>
                <td class="message-cell"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                <td class="date-cell"><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="empty-state">
                <div class="empty-state-icon">💬</div>
                <div class="empty-state-text">No feedback messages yet</div>
                <p class="text-muted mt-2">Customer feedback will appear here once they submit messages through the contact form.</p>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>