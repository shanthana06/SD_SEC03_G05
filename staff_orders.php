<?php include 'db.php'; ?>
<h2>Orders</h2>
<?php
$result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
while ($row = $result->fetch_assoc()) {
    echo "<p>{$row['customer_name']} ordered {$row['item_name']} ({$row['quantity']}) on {$row['order_date']}</p>";
}
?>