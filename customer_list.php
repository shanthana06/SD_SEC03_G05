<?php include 'db.php'; ?>
<h2>Customer List</h2>
<?php
$result = $conn->query("SELECT * FROM customers ORDER BY joined_date DESC");
while ($row = $result->fetch_assoc()) {
    echo "<p>{$row['name']} ({$row['email']}) - Joined on {$row['joined_date']}</p>";
}
?>