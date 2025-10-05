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
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
    :root{--bg:#f6eaea;--accent:#8b5a53;--muted:#e9d6d4}
    body{font-family:'Poppins',sans-serif;background:var(--muted);display:flex;flex-direction:column;align-items:center;padding:30px}
    .card{width:720px;background:#fff;border-radius:6px;padding:28px 36px;box-shadow:0 6px 20px rgba(0,0,0,0.06);position:relative;margin-top:20px}
    .border-pattern{position:absolute;inset:0;border-radius:6px;padding:18px;background-image:linear-gradient(45deg, rgba(139,90,83,0.05) 25%, transparent 25%, transparent 75%, rgba(139,90,83,0.05) 75%), linear-gradient(45deg, rgba(139,90,83,0.05) 25%, transparent 25%, transparent 75%, rgba(139,90,83,0.05) 75%);background-size:18px 18px,18px 18px;pointer-events:none}
    .content{position:relative;background:transparent}

    /* Header */
    .logo{display:flex;align-items:center;gap:18px}
    .logo svg{width:68px;height:68px;opacity:0.9}
    .title{font-weight:700;color:var(--accent);font-size:28px;letter-spacing:1px}
    .hr{height:3px;background:var(--accent);width:80px;margin:12px 0;border-radius:3px}

    /* Customer */
    .customer{margin-top:6px;margin-bottom:18px;color:#333}
    .customer-name{font-weight:700}
    .small{font-size:13px;color:#666}

    /* Table */
    table{width:100%;border-collapse:collapse;margin-top:14px}
    th,td{padding:10px 8px;text-align:left;font-size:14px}
    thead th{background:#faf7f6;color:#777;font-weight:600;border-bottom:1px solid #eee}
    tbody tr td{border-bottom:1px dashed #eee}
    .right{text-align:right}

    /* Summary box */
    .summary{float:right;width:240px;margin-top:12px;background:#f0dbd8;padding:12px;border-radius:4px;color:#4b2f2c;font-weight:600}
    .summary .row{display:flex;justify-content:space-between;padding:6px 0}

    /* Footer */
    .footer{display:flex;justify-content:space-between;align-items:center;margin-top:46px;padding-top:14px;border-top:6px solid #f5eaea}
    .footer .left{font-weight:700;color:var(--accent)}
    .footer .right small{display:block;color:#555}

    /* Print button */
    .actions{margin-top:18px}
    .btn{background:var(--accent);color:#fff;padding:10px 14px;border-radius:6px;border:none;cursor:pointer;font-weight:600}

    @media print{
      body{background:transparent}
      .card{box-shadow:none}
      .actions{display:none}
    }
  </style>
</head>
<body>


  <div class="card">
    <div class="border-pattern"></div>
    <div class="content">
      <div class="logo">
        <!-- simple coffee icon -->
        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="8" y="12" width="36" height="28" rx="3" stroke="#8b5a53" stroke-width="2" fill="none"/>
          <path d="M44 18h6a6 6 0 0 1 0 12h-6" stroke="#8b5a53" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          <path d="M16 10v-4" stroke="#8b5a53" stroke-width="2" stroke-linecap="round"/>
          <circle cx="14" cy="50" r="2" fill="#8b5a53"/>
        </svg>
        <div>
          <div class="title">ARJUNA<br> N CO-FFEE</div>
          <div class="hr"></div>
        </div>
      </div>

      <div style="display:flex;justify-content:flex-end;align-items:center;margin-top:-10px">
        <div style="text-align:right">
          <div class="customer-name"><?=htmlspecialchars($order['customer_name'])?></div>
          <div class="small"><?=htmlspecialchars($order['email'])?></div>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width:62%">DESCRIPTION</th>
            <th style="width:8%">QTY</th>
            <th style="width:12%" class="right">PRICE</th>
            <th style="width:18%" class="right">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order['items'] as $it): ?>
            <tr>
              <td><?=htmlspecialchars($it['description'])?></td>
              <td><?=htmlspecialchars($it['qty'])?></td>
              <td class="right">$<?=number_format($it['price'],2)?></td>
              <td class="right">$<?=number_format($it['qty']*$it['price'],2)?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="summary">
        <div class="row"><span>Sub.total</span><span>$<?=number_format($subtotal,2)?></span></div>
        <div class="row"><span>Tax</span><span><?=$order['tax_percent']?>%</span></div>
        <div class="row" style="font-size:16px;margin-top:6px"><span>Amount Due</span><span>$<?=number_format($amount_due,2)?></span></div>
      </div>

      <div style="clear:both"></div>

      <div style="margin-top:36px">
        <div style="display:flex;justify-content:space-between;align-items:center">
          <div class="left">@ARJUNACOFFEE2025</div>
          <div class="right" style="text-align:right">
           
          
          </div>
        </div>
      </div>

      <div class="footer">
        <div>ARJUNACOFFEE.COM | +6012 7445200</div>
        <div class="address">
  <div>Lot 17, Jalan Sutera Merah 3</div>
  <div>Taman Sutera Utama</div>
  <div>81300 Skudai</div>
  <div>Johor Bahru</div>
  <div>Johor, Malaysia</div>
</div>

      </div>

      <div class="actions">
        <button class="btn" onclick="window.print()">Print Receipt</button>
      </div>

    </div>
  </div>
</body>
</html>
