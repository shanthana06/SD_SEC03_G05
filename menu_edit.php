<?php include 'db.php'; ?>
<h2>Edit Menu</h2>
<?php
$result = $conn->query("SELECT * FROM menu");
while ($row = $result->fetch_assoc()) {
    echo "<form method='POST'>
        <input type='hidden' name='id' value='{$row['id']}'>
        <input type='text' name='item_name' value='{$row['item_name']}'>
        <input type='number' step='0.01' name='price' value='{$row['price']}'>
        <button type='submit'>Update</button>
    </form>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("UPDATE menu SET item_name=?, price=? WHERE id=?");
    $stmt->bind_param("sdi", $_POST['item_name'], $_POST['price'], $_POST['id']);
    $stmt->execute();
    echo "Menu updated!";
}
?>