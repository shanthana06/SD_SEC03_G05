<?php include 'db.php'; ?>
<h2>Customer Feedback</h2>
<?php
$result = $conn->query("SELECT * FROM feedback ORDER BY submitted_at DESC");
while ($row = $result->fetch_assoc()) {
    echo "<p><strong>{$row['customer_name']}</strong>: {$row['message']} ({$row['submitted_at']})</p>";
}
?>