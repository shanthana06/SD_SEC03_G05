<?php include 'db.php'; ?>
<h2>Sales Report</h2>
<?php
$result = $conn->query("SELECT * FROM sales ORDER BY sale_date DESC");
$total = 0;
while ($row = $result->fetch_assoc()) {
    echo "<p>{$row['item_name']} - {$row['quantity_sold']} sold - RM{$row['total_amount']} on {$row['sale_date']}</p>";
    $total += $row['total_amount'];
}
echo "<hr><strong>Total Sales: RM{$total}</strong>";
?>