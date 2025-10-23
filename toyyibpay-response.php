<?php
// toyyibpay-response.php - FIXED VERSION
session_start();

// Get parameters from ToyyibPay
$status_id = $_GET['status_id'] ?? '';
$billcode = $_GET['billcode'] ?? '';
$order_id_param = $_GET['order_id'] ?? '';
$message = $_GET['msg'] ?? '';
$transaction_id = $_GET['transaction_id'] ?? '';

// Extract actual order ID from ORDER_XX format
$order_id = str_replace('ORDER_', '', $order_id_param);

// Store ToyyibPay response in session
$_SESSION['toyyibpay_response'] = [
    'status_id' => $status_id,
    'billcode' => $billcode,
    'order_id' => $order_id,
    'message' => $message,
    'transaction_id' => $transaction_id
];

// Build redirect URL with ALL parameters
$redirect_url = "receipt.php?order_id=" . urlencode($order_id);
if ($status_id) $redirect_url .= "&status_id=" . urlencode($status_id);
if ($billcode) $redirect_url .= "&billcode=" . urlencode($billcode);
if ($message) $redirect_url .= "&msg=" . urlencode($message);
if ($transaction_id) $redirect_url .= "&transaction_id=" . urlencode($transaction_id);

// Debug output (remove in production)
error_log("Redirecting to: " . $redirect_url);

// Redirect immediately
header("Location: " . $redirect_url);
exit;
?>