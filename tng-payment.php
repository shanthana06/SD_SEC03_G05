<?php
// tng-payment.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['tng_order_id'])) {
    header("Location: payment.php");
    exit;
}

$order_id = $_SESSION['tng_order_id'];
$tng_phone = $_SESSION['tng_phone'] ?? '';
$amount = $_SESSION['tng_amount'] ?? 0;

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Touch 'n Go Payment | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #000000;
      --secondary-color: #666666;
      --light-bg: #ffffff;
      --border-color: #e0e0e0;
      --accent-gray: #f8f9fa;
    }
    
    body {
      background: var(--light-bg);
      font-family: 'Cormorant Garamond', serif;
      color: var(--primary-color);
      line-height: 1.6;
      min-height: 100vh;
    }
    
    .page-container {
      max-width: 800px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .page-header {
      text-align: center;
      margin-bottom: 40px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border-color);
    }
    
    .page-title {
      font-family: 'Playfair Display', serif;
      font-weight: 500;
      font-size: 2.8rem;
      letter-spacing: -0.5px;
      margin-bottom: 10px;
      color: var(--primary-color);
    }
    
    .page-subtitle {
      color: var(--secondary-color);
      font-weight: 300;
      font-size: 1.2rem;
      max-width: 500px;
      margin: 0 auto;
      font-family: 'Lora', serif;
    }
    
    .payment-container {
      background-color: var(--light-bg);
      border-radius: 8px;
      padding: 40px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.08);
      border: 1px solid var(--border-color);
      margin-bottom: 30px;
    }
    
    .section-title {
      font-family: 'Playfair Display', serif;
      font-weight: 500;
      font-size: 1.8rem;
      margin-bottom: 25px;
      color: var(--primary-color);
      border-bottom: 1px solid var(--border-color);
      padding-bottom: 10px;
    }
    
    .order-card {
      background: var(--accent-gray);
      border-radius: 6px;
      padding: 25px;
      margin-bottom: 30px;
      border-left: 4px solid var(--primary-color);
    }
    
    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid var(--border-color);
    }
    
    .order-number {
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      font-size: 1.4rem;
      color: var(--primary-color);
    }
    
    .order-status {
      background: var(--primary-color);
      color: var(--light-bg);
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
      font-family: 'Lora', serif;
    }
    
    .order-details {
      display: grid;
      gap: 15px;
    }
    
    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
    }
    
    .detail-label {
      color: var(--secondary-color);
      font-weight: 400;
      font-family: 'Lora', serif;
    }
    
    .detail-value {
      font-weight: 600;
      color: var(--primary-color);
    }
    
    .amount-highlight {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary-color);
      font-family: 'Playfair Display', serif;
    }
    
    .instruction-text {
      text-align: center;
      color: var(--secondary-color);
      margin: 30px 0;
      font-size: 1.1rem;
      font-family: 'Lora', serif;
      line-height: 1.7;
    }
    
    .btn-tng {
      background: var(--primary-color);
      color: var(--light-bg);
      border: 2px solid var(--primary-color);
      border-radius: 6px;
      padding: 18px 30px;
      font-size: 1.2rem;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s ease;
      font-family: 'Lora', serif;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }
    
    .btn-tng:hover {
      background: transparent;
      color: var(--primary-color);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .tng-logo {
      width: 28px;
      height: 28px;
      object-fit: contain;
      
    }
    
   
    
    .instructions-box {
      background: var(--accent-gray);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 25px;
      margin: 30px 0;
    }
    
    .instructions-title {
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      font-size: 1.2rem;
      margin-bottom: 20px;
      color: var(--primary-color);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .instruction-steps {
      display: grid;
      gap: 15px;
    }
    
    .step {
      display: flex;
      align-items: flex-start;
      gap: 15px;
      font-size: 1rem;
      color: var(--secondary-color);
      line-height: 1.5;
    }
    
    .step-number {
      background: var(--primary-color);
      color: var(--light-bg);
      width: 24px;
      height: 24px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      font-weight: 700;
      flex-shrink: 0;
      margin-top: 2px;
      font-family: 'Lora', serif;
    }
    
    .action-buttons {
      display: grid;
      gap: 12px;
      margin-top: 30px;
    }
    
    .btn-outline-custom {
      background: transparent;
      color: var(--primary-color);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 12px 24px;
      font-family: 'Lora', serif;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .btn-outline-custom:hover {
      background: var(--accent-gray);
      border-color: var(--primary-color);
    }
    
    .btn-primary-custom {
      background: var(--primary-color);
      color: var(--light-bg);
      border: 1px solid var(--primary-color);
      border-radius: 6px;
      padding: 12px 24px;
      font-family: 'Lora', serif;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
      background: transparent;
      color: var(--primary-color);
    }
    
    @media (max-width: 768px) {
      .page-container {
        padding: 20px 15px;
      }
      
      .payment-container {
        padding: 25px 20px;
      }
      
      .page-title {
        font-size: 2.2rem;
      }
      
      .order-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
      
      .amount-highlight {
        font-size: 1.5rem;
      }
      
      .btn-tng {
        padding: 15px 20px;
        font-size: 1.1rem;
      }
      
      .tng-logo {
        width: 24px;
        height: 24px;
      }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-container">
  <div class="page-header">
    <h1 class="page-title">Secure Payment</h1>
    <p class="page-subtitle">Complete your order with Touch 'n Go eWallet</p>
  </div>
  
  <div class="payment-container">
    <h3 class="section-title">Payment Details</h3>
    
    <div class="order-card">
      <div class="order-header">
        <div class="order-number">Order #<?= $order_id ?></div>
        <div class="order-status">Pending Payment</div>
      </div>
      <div class="order-details">
        <div class="detail-row">
          <span class="detail-label">Amount to Pay</span>
          <span class="detail-value amount-highlight">RM <?= number_format($amount, 2) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Phone Number</span>
          <span class="detail-value"><?= htmlspecialchars($tng_phone) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Payment Method</span>
          <span class="detail-value">Touch 'n Go eWallet</span>
        </div>
      </div>
    </div>
    
    <p class="instruction-text">
      Click the button below to complete your payment securely through Touch 'n Go eWallet. 
      You will be redirected to their official payment gateway.
    </p>
    
    <a href="https://payment.tngdigital.com.my/sc/bDLokHK3Pz" 
       class="btn btn-tng" 
       target="_blank">
      <img src="images/touchngo.png" alt="Touch 'n Go" class="tng-logo">
      Pay with Touch 'n Go eWallet
    </a>
    
    <div class="instructions-box">
      <div class="instructions-title">
        <span></span>
        Important Instructions
      </div>
      <div class="instruction-steps">
        <div class="step">
          <div class="step-number">1</div>
          <div>Complete the payment process in your Touch 'n Go eWallet app when redirected</div>
        </div>
        <div class="step">
          <div class="step-number">2</div>
          <div>Take a screenshot of your payment confirmation for your records</div>
        </div>
        <div class="step">
          <div class="step-number">3</div>
          <div>Your order will be processed immediately after payment verification</div>
        </div>
      </div>
    </div>
    
    <div class="action-buttons">
      <a href="receipt.php?order_id=<?= $order_id ?>" class="btn btn-primary-custom">
        View Order Details
      </a>
      <a href="index.php" class="btn btn-outline-custom">
        Return to Home
      </a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>