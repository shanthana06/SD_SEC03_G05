<?php
// toyyibpay-process.php - FIXED RETURN URL
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// ToyyibPay Configuration
define('TOYYIBPAY_USER_SECRET_KEY', '5ym2mcoj-yc0r-6dx4-7984-h6yrha3f21pn');
define('TOYYIBPAY_CATEGORY_CODE', 'ijmycgdi');
define('TOYYIBPAY_BASE_URL', 'https://toyyibpay.com/');

// Get order ID from URL
$order_id = $_GET['order_id'] ?? 0;

if (!$order_id || !isset($_SESSION['toyyibpay_order_id']) || $_SESSION['toyyibpay_order_id'] != $order_id) {
    die("Invalid order ID");
}

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    die("User not logged in");
}

try {
    // Fetch order details with the correct column name (user_id)
    $stmt = $conn->prepare("
        SELECT o.*, u.email, u.name, u.phone 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.id = ?
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
        die("Order not found");
    }

} catch (Exception $e) {
    // If JOIN fails, try a different approach
    error_log("Join error: " . $e->getMessage());
    
    // Get order details without user join
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$order) {
        die("Order not found");
    }
    
    // Get user details using correct column name (user_id)
    $user_id = $order['user_id'];
    $stmt = $conn->prepare("SELECT email, name, phone FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($user_result) {
        $order['email'] = $user_result['email'];
        $order['name'] = $user_result['name'];
        $order['phone'] = $user_result['phone'];
    } else {
        // Fallback to session data
        $order['email'] = $_SESSION['email'] ?? 'customer@example.com';
        $order['name'] = $_SESSION['name'] ?? 'Customer';
        $order['phone'] = $_SESSION['phone'] ?? '01123456789';
    }
}

// Get your actual domain - FIXED: Use receipt.php as return URL
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$return_url = "http://localhost/arjunababy/receipt.php";

$callback_url = $base_url . '/toyyibpay-callback.php';

// Prepare ToyyibPay parameters
$some_data = array(
    'userSecretKey' => TOYYIBPAY_USER_SECRET_KEY,
    'categoryCode' => TOYYIBPAY_CATEGORY_CODE,
    'billName' => 'Arjuna n Co-ffee Order',
    'billDescription' => 'Food and Beverage Order #' . $order_id,
    'billPriceSetting' => 1,
    'billPayorInfo' => 1,
    'billAmount' => $order['total_amount'] * 100,
    'billReturnUrl' => $return_url,  // This will now redirect to receipt.php
    'billCallbackUrl' => $callback_url,
    'billExternalReferenceNo' => 'ORDER_' . $order_id,
    'billTo' => $order['name'],
    'billEmail' => $order['email'],
    'billPhone' => $order['phone'],
    'billSplitPayment' => 0,
    'billSplitPaymentArgs' => '',
    'billPaymentChannel' => 0,
    'billContentEmail' => 'Thank you for purchasing from Arjuna n Co-ffee!',
    'billChargeToCustomer' => 1
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_URL, TOYYIBPAY_BASE_URL . 'index.php/api/createBill');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

$result = curl_exec($curl);
$info = curl_getinfo($curl);

if (curl_error($curl)) {
    error_log("cURL Error: " . curl_error($curl));
    die("Connection error. Please try again.");
}

curl_close($curl);

$obj = json_decode($result, true);

if ($obj && isset($obj[0]['BillCode'])) {
    $_SESSION['toyyibpay_billcode'] = $obj[0]['BillCode'];
    $_SESSION['toyyibpay_order_id'] = $order_id;
    header("Location: " . TOYYIBPAY_BASE_URL . $obj[0]['BillCode']);
    exit;
} else {
    error_log("ToyyibPay API Error: " . $result);
    $error_message = "Error creating ToyyibPay bill. ";
    if ($obj && isset($obj[0]['Message'])) {
        $error_message .= "Message: " . $obj[0]['Message'];
    }
    $_SESSION['payment_error'] = $error_message;
    header("Location: payment.php?error=toyyibpay_failed");
    exit;
}
?>