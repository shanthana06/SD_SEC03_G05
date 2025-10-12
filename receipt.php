<?php
// receipt.php

include 'navbar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user info
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}

$user = $result->fetch_assoc();

// Check if cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
    exit;
}

// Map cart to receipt items
$items = [];
foreach ($_SESSION['cart'] as $cartItem) {
    $items[] = [
        'description' => $cartItem['name'],        
        'qty' => $cartItem['quantity'],
        'price' => $cartItem['price']
    ];
}

// Set up order array
$order = [
    'id' => rand(10000, 99999), // generate random order ID
    'customer_name' => $user['name'],
    'email' => $user['email'],
    'items' => $items,
    'tax_percent' => 6
];

// Calculate totals
$subtotal = 0;
foreach ($order['items'] as $item) {
    $subtotal += $item['qty'] * $item['price'];
}
$tax = round($subtotal * $order['tax_percent'] / 100, 2);
$amount_due = round($subtotal + $tax, 2);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receipt #<?=htmlspecialchars($order['id'])?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Parisienne&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Cormorant Garamond', serif;
      background: #fefefe;
      color: #333;
      line-height: 1.6;
      padding: 40px 20px;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .receipt-container {
      max-width: 800px;
      width: 100%;
      background: white;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      border-radius: 12px;
      overflow: hidden;
      position: relative;
    }

    /* Header with elegant background */
    .receipt-header {
      background: linear-gradient(135deg, #8B7355 0%, #A89276 100%);
      padding: 40px 40px 25px;
      color: white;
      text-align: center;
      position: relative;
    }

    .receipt-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #D4AF37, #F4E4B3, #D4AF37);
    }

    .brand-name {
      font-family: 'Playfair Display', serif;
      font-size: 2.5rem;
      font-weight: 400;
      letter-spacing: 2px;
      margin-bottom: 8px;
    }

    .brand-subtitle {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
      font-weight: 300;
      letter-spacing: 3px;
      text-transform: uppercase;
      opacity: 0.9;
    }

    .receipt-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      font-weight: 400;
      margin-top: 20px;
      letter-spacing: 1px;
    }

    /* Content area */
    .receipt-content {
      padding: 35px;
    }

    /* Customer info section */
    .customer-section {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid #eaeaea;
    }

    .customer-info h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 8px;
      color: #8B7355;
    }

    .customer-info p {
      font-size: 1rem;
      color: #666;
    }

    .order-info {
      text-align: right;
    }

    .order-id {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem;
      font-weight: 600;
      color: #8B7355;
      margin-bottom: 5px;
    }

    .order-date {
      font-size: 0.95rem;
      color: #888;
    }

    /* Items table */
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
    }

    .items-table th {
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      text-align: left;
      padding: 12px 8px;
      border-bottom: 2px solid #eaeaea;
      color: #8B7355;
      font-size: 1rem;
    }

    .items-table td {
      padding: 14px 8px;
      border-bottom: 1px solid #f0f0f0;
      font-size: 1rem;
    }

    .items-table tr:last-child td {
      border-bottom: none;
    }

    .text-right {
      text-align: right;
    }

    .item-name {
      font-weight: 500;
      color: #444;
    }

    /* Summary section */
    .summary-section {
      background: #faf9f7;
      padding: 20px 25px;
      border-radius: 8px;
      margin-top: 10px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      font-size: 1rem;
    }

    .summary-total {
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      font-size: 1.2rem;
      color: #8B7355;
      border-top: 1px solid #eaeaea;
      padding-top: 10px;
      margin-top: 6px;
    }

    /* Footer */
    .receipt-footer {
      margin-top: 35px;
      padding-top: 25px;
      border-top: 1px solid #eaeaea;
      text-align: center;
    }

    .contact-info {
      font-size: 0.95rem;
      color: #666;
      margin-bottom: 15px;
    }

    .address {
      font-style: normal;
      font-size: 0.95rem;
      color: #888;
      line-height: 1.5;
    }

    /* Action buttons */
    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 25px;
    }

    .btn {
      padding: 10px 24px;
      border-radius: 6px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      cursor: pointer;
      border: none;
    }

    .btn-primary {
      background: #8B7355;
      color: white;
    }

    .btn-primary:hover {
      background: #756048;
      transform: translateY(-2px);
    }

    .btn-secondary {
      background: transparent;
      color: #8B7355;
      border: 1px solid #8B7355;
    }

    .btn-secondary:hover {
      background: #f8f6f3;
    }

    /* Print styles - FIXED to prevent blank page */
    @media print {
      body {
        padding: 0;
        background: white;
        margin: 0;
        height: auto;
        display: block;
      }
      
      .receipt-container {
        box-shadow: none;
        max-width: 100%;
        margin: 0;
        border-radius: 0;
        page-break-after: avoid;
        page-break-inside: avoid;
      }
      
      .action-buttons {
        display: none;
      }
      
      /* Ensure everything fits on one page */
      .receipt-header {
        padding: 30px 30px 20px;
        page-break-after: avoid;
      }
      
      .receipt-content {
        padding: 25px 30px;
        page-break-inside: avoid;
      }
      
      .customer-section {
        margin-bottom: 20px;
        padding-bottom: 15px;
        page-break-after: avoid;
      }
      
      .items-table {
        margin-bottom: 20px;
        page-break-inside: avoid;
      }
      
      .items-table th,
      .items-table td {
        padding: 10px 6px;
        font-size: 0.95rem;
      }
      
      .summary-section {
        padding: 15px 20px;
        page-break-before: avoid;
      }
      
      .receipt-footer {
        margin-top: 25px;
        padding-top: 20px;
        page-break-before: avoid;
      }
      
      /* Force single page */
      html, body {
        height: auto !important;
        overflow: visible !important;
      }
      
      .receipt-container {
        height: auto !important;
        min-height: auto !important;
      }
    }

    /* Responsive design */
    @media (max-width: 768px) {
      body {
        padding: 20px 10px;
      }
      
      .receipt-header {
        padding: 25px 20px 15px;
      }
      
      .brand-name {
        font-size: 2rem;
      }
      
      .receipt-content {
        padding: 20px 15px;
      }
      
      .customer-section {
        flex-direction: column;
        gap: 12px;
      }
      
      .order-info {
        text-align: left;
      }
      
      .action-buttons {
        flex-direction: column;
        align-items: center;
      }
      
      .btn {
        width: 100%;
        max-width: 200px;
      }
    }

    /* Extra small screens */
    @media (max-width: 480px) {
      .items-table {
        font-size: 0.9rem;
      }
      
      .items-table th,
      .items-table td {
        padding: 8px 4px;
      }
      
      .brand-name {
        font-size: 1.8rem;
      }
    }
  </style>
</head>
<body>

<div class="receipt-container">
  <div class="receipt-header">
    <div class="brand-name">Arjuna n Co-ffee</div>
    <div class="brand-subtitle">Artisanal Coffee Experience</div>
    <div class="receipt-title">ORDER RECEIPT</div>
  </div>
  
  <div class="receipt-content">
    <div class="customer-section">
      <div class="customer-info">
        <h3><?=htmlspecialchars($order['customer_name'])?></h3>
        <p><?=htmlspecialchars($order['email'])?></p>
      </div>
      <div class="order-info">
        <div class="order-id">Order #<?=htmlspecialchars($order['id'])?></div>
        <div class="order-date"><?=date('F j, Y')?></div>
      </div>
    </div>
    
    <table class="items-table">
      <thead>
        <tr>
          <th>ITEM</th>
          <th class="text-right">QTY</th>
          <th class="text-right">PRICE</th>
          <th class="text-right">TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($order['items'] as $it): ?>
          <tr>
            <td class="item-name"><?=htmlspecialchars($it['description'])?></td>
            <td class="text-right"><?=htmlspecialchars($it['qty'])?></td>
            <td class="text-right">$<?=number_format($it['price'],2)?></td>
            <td class="text-right">$<?=number_format($it['qty']*$it['price'],2)?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    
    <div class="summary-section">
      <div class="summary-row">
        <span>Subtotal</span>
        <span>$<?=number_format($subtotal,2)?></span>
      </div>
      <div class="summary-row">
        <span>Tax (<?=$order['tax_percent']?>%)</span>
        <span>$<?=number_format($tax,2)?></span>
      </div>
      <div class="summary-row summary-total">
        <span>Total Amount</span>
        <span>$<?=number_format($amount_due,2)?></span>
      </div>
    </div>
    
    <div class="receipt-footer">
      <div class="contact-info">
        arjunacoffee.com | +6012 7445200
      </div>
      <div class="address">
        Lot 17, Jalan Sutera Merah 3, Taman Sutera Utama<br>
        81300 Skudai, Johor Bahru, Johor, Malaysia
      </div>
      
      <div class="action-buttons">
        <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
        <a href="index.php" class="btn btn-secondary">Back to Home</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>