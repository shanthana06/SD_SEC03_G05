<?php include 'db.php'; ?>
<form method="POST">
  <input type="text" name="item_name" placeholder="Item Name" required>
  <textarea name="description" placeholder="Description"></textarea>
  <input type="number" step="0.01" name="price" placeholder="Price" required>
  <button type="submit">Add Item</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("INSERT INTO menu (item_name, description, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $_POST['item_name'], $_POST['description'], $_POST['price']);
    $stmt->execute();
    echo "Menu item added!";
}
?>