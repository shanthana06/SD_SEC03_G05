<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Try to read JSON first
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// If JSON is empty, fallback to regular POST
if (!$data) {
    $data = $_POST;
}

error_log("ToyyibPay Callback Received: " . print_r($data, true));

if (isset($data['refno'])) {
    $ref_no = $data['refno'];
    $status = $data['status'];
    $billcode = $data['billcode'];

    $order_id = str_replace('ORDER_', '', $ref_no);

    try {
        if ($status == 1) { // success
            $stmt_order = $conn->prepare("UPDATE orders SET status = 'Paid' WHERE id = ?");
            $stmt_order->bind_param("i", $order_id);
            $stmt_order->execute();
            $stmt_order->close();

            $stmt_payment = $conn->prepare("UPDATE payments SET status = 'completed' WHERE order_id = ?");
            $stmt_payment->bind_param("i", $order_id);
            $stmt_payment->execute();
            $stmt_payment->close();

            error_log("✅ Payment completed for order: " . $order_id);
        } elseif ($status == 3) { // failed
            $stmt_order = $conn->prepare("UPDATE orders SET status = 'Payment Failed' WHERE id = ?");
            $stmt_order->bind_param("i", $order_id);
            $stmt_order->execute();
            $stmt_order->close();

            $stmt_payment = $conn->prepare("UPDATE payments SET status = 'failed' WHERE order_id = ?");
            $stmt_payment->bind_param("i", $order_id);
            $stmt_payment->execute();
            $stmt_payment->close();

            error_log("❌ Payment failed for order: " . $order_id);
        }

        http_response_code(200);
        echo 'OK';
    } catch (Exception $e) {
        error_log("Callback Error: " . $e->getMessage());
        http_response_code(500);
        echo 'ERROR';
    }
} else {
    error_log("⚠ Invalid callback data received");
    http_response_code(400);
    echo 'INVALID';
}
?>
