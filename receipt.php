<?php
// receipt.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
//include 'navbar.php';
// Debugging aid
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get order ID from URL or session
$order_id = $_GET['order_id'] ?? ($_SESSION['last_order_id'] ?? '');

// Remove 'ORDER_' prefix (ToyyibPay sends "ORDER_96")
$numeric_id = str_replace('ORDER_', '', $order_id);

// Check if this is a ToyyibPay response
$is_toyyibpay_response = isset($_GET['status_id']);
$toyyibpay_status = '';

if ($is_toyyibpay_response) {
    $status_id = $_GET['status_id'] ?? '';
    $transaction_id = $_GET['transaction_id'] ?? '';
    
    // Set status based on ToyyibPay response
    switch ($status_id) {
        case '1':
            $toyyibpay_status = 'success';
            break;
        case '2':
            $toyyibpay_status = 'pending';
            break;
        case '3':
            $toyyibpay_status = 'failed';
            break;
        default:
            $toyyibpay_status = 'unknown';
            break;
    }
}

if (!$numeric_id) {
    echo "No receipt found.";
    exit;
}

// FIXED: Use the simple query approach as specified in the instructions
$stmt = $conn->prepare("
    SELECT o.id, o.customer_name, o.total_amount, o.created_at, o.order_type, 
           o.payment_method, o.status, p.transaction_id, o.user_id
    FROM orders o
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE o.id = ?
");
$stmt->bind_param("i", $numeric_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='text-center mt-5'>⚠ No orders found for this order ID ($numeric_id).</div>";
    exit;
}

$order_data = $result->fetch_assoc();

// Security check: ensure the order belongs to the logged-in user
if ($order_data['user_id'] != $user_id) {
    echo "Access denied. This order doesn't belong to you.";
    exit;
}

// Get order items
$sql_items = "SELECT oi.product_name, oi.quantity, oi.price 
              FROM order_items oi 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $numeric_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$items = [];
while ($item = $result_items->fetch_assoc()) {
    $items[] = [
        'description' => $item['product_name'],
        'qty' => $item['quantity'],
        'price' => $item['price']
    ];
}

// Get user info
$sql_user = "SELECT name, email, phone FROM users WHERE user_id = ?";

$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Set up order array using data from the fixed query
$order = [
    'id' => $order_data['id'],
    'customer_name' => $order_data['customer_name'], // Use from orders table instead of user table
    'email' => $user['email'],
    'phone' => $user['phone'],
    'items' => $items,
    'tax_percent' => 6,
    'payment_method' => $order_data['payment_method'],
    'order_type' => $order_data['order_type'],
    'order_date' => $order_data['created_at'],
    'order_status' => $order_data['status'],
    'transaction_id' => $order_data['transaction_id'] ?? ($_GET['transaction_id'] ?? ''),
    'total_amount' => $order_data['total_amount']
];

// Calculate totals
$subtotal = 0;
foreach ($order['items'] as $item) {
    $subtotal += $item['qty'] * $item['price'];
}
$tax = round($subtotal * $order['tax_percent'] / 100, 2);
$amount_due = round($subtotal + $tax, 2);

// Use the actual total from database if available, otherwise use calculated total
if ($order_data['total_amount'] > 0) {
    $amount_due = $order_data['total_amount'];
    // Recalculate tax and subtotal based on the total amount
    $subtotal = round($amount_due / (1 + $order['tax_percent'] / 100), 2);
    $tax = $amount_due - $subtotal;
}

// Determine status display
$status_display = $order['order_status'];
$status_class = '';

if ($is_toyyibpay_response) {
    switch ($toyyibpay_status) {
        case 'success':
            $status_display = 'Payment Successful';
            $status_class = 'status-success';
            break;
        case 'pending':
            $status_display = 'Payment Pending';
            $status_class = 'status-pending';
            break;
        case 'failed':
            $status_display = 'Payment Failed';
            $status_class = 'status-failed';
            break;
    }
} else {
    switch ($order['order_status']) {
        case 'Paid':
        case 'Completed':
            $status_class = 'status-success';
            break;
        case 'Pending Payment':
        case 'Pending':
            $status_class = 'status-pending';
            break;
        case 'Payment Failed':
        case 'Failed':
            $status_class = 'status-failed';
            break;
        default:
            $status_class = 'status-default';
            break;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receipt #ORDER_<?=htmlspecialchars($order['id'])?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Parisienne&family=Cormorant+Garamond:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    /* Your existing CSS styles remain the same */
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

    /* Status Banner */
    .status-banner {
      padding: 15px 20px;
      text-align: center;
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 0;
    }

    .status-success {
      background: #d4edda;
      color: #155724;
      border-bottom: 3px solid #28a745;
    }

    .status-pending {
      background: #fff3cd;
      color: #856404;
      border-bottom: 3px solid #ffc107;
    }

    .status-failed {
      background: #f8d7da;
      color: #721c24;
      border-bottom: 3px solid #dc3545;
    }

    .status-default {
      background: #e2e3e5;
      color: #383d41;
      border-bottom: 3px solid #6c757d;
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

    /* Payment info */
    .payment-info {
      background: #f8f6f3;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      border-left: 4px solid #8B7355;
    }

    .payment-info h4 {
      font-family: 'Playfair Display', serif;
      color: #8B7355;
      margin-bottom: 5px;
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
      flex-wrap: wrap;
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
      display: inline-block;
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

    .btn-success {
      background: #28a745;
      color: white;
    }

    .btn-success:hover {
      background: #218838;
    }

    .btn-danger {
      background: #dc3545;
      color: white;
    }

    .btn-danger:hover {
      background: #c82333;
    }

    /* Print styles */
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
  
  <?php if ($is_toyyibpay_response || $order['order_status']): ?>
  <div class="status-banner <?= $status_class ?>">
    <?= htmlspecialchars($status_display) ?>
    <?php if ($order['transaction_id']): ?>
      <br><small>Transaction ID: <?= htmlspecialchars($order['transaction_id']) ?></small>
    <?php endif; ?>
  </div>
  <?php endif; ?>
  
  <div class="receipt-content">
    <div class="customer-section">
      <div class="customer-info">
       <h3><?= htmlspecialchars($order['customer_name'] ?? '') ?></h3>

        <p><?=htmlspecialchars($order['email'])?></p>
        <?php if ($order['phone']): ?>
          <p><?=htmlspecialchars($order['phone'])?></p>
        <?php endif; ?>
      </div>
      <div class="order-info">
        <div class="order-id">Order #ORDER_<?=htmlspecialchars($order['id'])?></div>
        <div class="order-date"><?=date('F j, Y g:i A', strtotime($order['order_date']))?></div>
        <?php if ($order['order_type']): ?>
          <div class="order-type">Type: <?=htmlspecialchars($order['order_type'])?></div>
        <?php endif; ?>
      </div>
    </div>
    
    <?php if (!empty($order['payment_method'])): ?>
    <div class="payment-info">
      <h4>Payment Method: <?=htmlspecialchars(ucfirst($order['payment_method']))?></h4>
    </div>
    <?php endif; ?>
    
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
        <?php if (!empty($order['items'])): ?>
          <?php foreach ($order['items'] as $it): ?>
            <tr>
              <td class="item-name"><?=htmlspecialchars($it['description'])?></td>
              <td class="text-right"><?=htmlspecialchars($it['qty'])?></td>
              <td class="text-right">RM <?=number_format($it['price'],2)?></td>
              <td class="text-right">RM <?=number_format($it['qty']*$it['price'],2)?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center">No items found in this order.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <div class="summary-section">
      <div class="summary-row">
        <span>Subtotal</span>
        <span>RM <?=number_format($subtotal,2)?></span>
      </div>
      <div class="summary-row">
        <span>Tax (<?=$order['tax_percent']?>%)</span>
        <span>RM <?=number_format($tax,2)?></span>
      </div>
      <div class="summary-row summary-total">
        <span>Total Amount</span>
        <span>RM <?=number_format($amount_due,2)?></span>
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
        
        <?php if ($is_toyyibpay_response && $toyyibpay_status === 'failed'): ?>
          <a href="payment.php" class="btn btn-danger">Try Payment Again</a>
        <?php endif; ?>
        
        <?php if ($is_toyyibpay_response && $toyyibpay_status === 'success'): ?>
          <a href="menu.php" class="btn btn-success">Continue Shopping</a>
        <?php else: ?>
          <a href="menu.php" class="btn btn-secondary">Continue Shopping</a>
        <?php endif; ?>
        
        <a href="index.php" class="btn btn-secondary">Back to Home</a>
      </div>
    </div>
  </div>
</div>

<script>
// Auto-redirect on success after 8 seconds (only for ToyyibPay success responses)
<?php if ($is_toyyibpay_response && $toyyibpay_status === 'success'): ?>
setTimeout(function() {
    // Remove ToyyibPay parameters to show clean receipt on refresh
    if (window.history.replaceState) {
        const cleanUrl = window.location.pathname + '?order_id=ORDER_<?= $order['id'] ?>';
        window.history.replaceState(null, '', cleanUrl);
    }
}, 8000);
<?php endif; ?>
</script>

</body>
</html>