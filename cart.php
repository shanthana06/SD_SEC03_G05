<?php
// cart.php
session_start();

// If cart doesn't exist, create it
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to calculate total
function calculateTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Handle actions (update, remove, clear)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $index = $_POST['index'];
                $qty = max(1, (int)$_POST['quantity']);
                $_SESSION['cart'][$index]['quantity'] = $qty;
                break;

            case 'remove':
                $index = $_POST['index'];
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
                break;

            case 'clear':
                $_SESSION['cart'] = [];
                break;
        }
    }
    // If it's AJAX, return JSON (no reload)
    if (isset($_POST['ajax'])) {
        echo json_encode([
            "total" => number_format(calculateTotal(), 2)
        ]);
        exit;
    }

    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    .cart-bg-blur {
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
      filter: blur(6px);
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
    }
    .cart-container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="cart-bg-blur"></div>

<section class="py-5">
  <div class="container cart-container">
    <h2 class="section-title text-center mb-4">Your Cart</h2>

    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price (RM)</th>
            <th>Total (RM)</th>
            <th>Remove</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
              <tr data-index="<?= $index ?>">
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>
                  <input type="number" 
                         value="<?= $item['quantity'] ?>" 
                         min="1" 
                         class="form-control w-50 mx-auto quantity-input" 
                         data-index="<?= $index ?>">
                </td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td class="subtotal"><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                <td>
                  <form method="post">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" class="btn btn-sm btn-danger">X</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5">Your cart is empty</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
      <form method="post">
        <input type="hidden" name="action" value="clear">
        <button type="submit" class="btn btn-outline-danger">Clear Cart</button>
      </form>
      <div class="text-end">
        <h5>Total: RM <span id="cart-total"><?= number_format(calculateTotal(), 2) ?></span></h5>
        <a href="payment.php?total=<?php echo calculateTotal(); ?>" class="btn btn-primary">Proceed to Payment</a>

      </div>
    </div>

    <div class="text-center mt-5">
      <a href="menu.php" class="btn btn-primary me-2">Add More Items</a>
      <a href="index.php" class="btn btn-outline-dark">Return to Home</a>
    </div>
  </div>
</section>
<form action="payment.php" method="POST">
  <label for="order_type">Order Type:</label>
  <select name="order_type" required>
    <option value="Dine-In">Dine-In</option>
    <option value="Pickup">Pickup</option>
  </select>
  
  <button type="submit" name="proceed_payment" class="btn btn-primary">Proceed to Payment</button>
</form>

<script>
  // Auto update when quantity changes
  document.querySelectorAll(".quantity-input").forEach(input => {
    input.addEventListener("change", function() {
      let index = this.dataset.index;
      let qty = this.value;

      fetch("cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `ajax=1&action=update&index=${index}&quantity=${qty}`
      })
      .then(res => res.json())
      .then(data => {
        let row = document.querySelector(`tr[data-index='${index}']`);
        let price = parseFloat(row.children[2].innerText);
        row.querySelector(".subtotal").innerText = (price * qty).toFixed(2);
        document.getElementById("cart-total").innerText = data.total;
      });
    });
  });
</script>
</body>
</html>
