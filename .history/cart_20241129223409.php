<?php
session_start(); // Start the session

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to the cart (if POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_name'], $_POST['item_price'])) {
    // Default quantity to 1 if not provided or invalid
    $quantity = isset($_POST['item_quantity']) && is_numeric($_POST['item_quantity']) && $_POST['item_quantity'] > 0
        ? (int)$_POST['item_quantity']
        : 1; // Default to 1

    // Check if the item already exists in the cart
    $itemExists = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['name'] === $_POST['item_name']) {
            $cartItem['quantity'] += $quantity; // Update the quantity if the item exists
            $itemExists = true;
            break;
        }
    }

    // If item doesn't exist, add it to the cart
    if (!$itemExists) {
        $_SESSION['cart'][] = [
            'name' => $_POST['item_name'],
            'price' => (int)$_POST['item_price'],
            'quantity' => $quantity // Default to 1 if no quantity provided
        ];
    }
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]); // Remove item from cart
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }
}

// Handle updating the quantity of an item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $index = $_POST['update'];
    $change = isset($_POST['change']) ? (int)$_POST['change'] : 0;

    if (isset($_SESSION['cart'][$index])) {
        // Ensure 'quantity' is set
        if (!isset($_SESSION['cart'][$index]['quantity']) || !is_numeric($_SESSION['cart'][$index]['quantity'])) {
            $_SESSION['cart'][$index]['quantity'] = 1; // Default quantity to 1 if not set
        }

        $newQuantity = $_SESSION['cart'][$index]['quantity'] + $change;

        // Update or remove item based on new quantity
        if ($newQuantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $newQuantity;
        } else {
            unset($_SESSION['cart'][$index]);
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }
}

// Rendering the cart content
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
  <h2>Your Cart</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Item</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (empty($_SESSION['cart'])) {
          echo '<tr><td colspan="5">Your cart is empty.</td></tr>';
      } else {
          $total = 0;
          foreach ($_SESSION['cart'] as $index => $item) {
              // Ensure the 'quantity' key exists and is valid
              if (!isset($item['quantity']) || !is_numeric($item['quantity'])) {
                  $item['quantity'] = 1; // Default to 1 if quantity is not set or invalid
              }
              $itemTotal = $item['price'] * $item['quantity'];
              $total += $itemTotal;

              echo "<tr>
                      <td>{$item['name']}</td>
                      <td>
                        <form method='POST' class='d-inline'>
                          <button class='btn btn-secondary btn-sm' type='submit' name='update' value='$index'>-</button>
                          <input type='hidden' name='change' value='-1' />
                        </form>
                        <span>{$item['quantity']}</span>
                        <form method='POST' class='d-inline'>
                          <button class='btn btn-secondary btn-sm' type='submit' name='update' value='$index'>+</button>
                          <input type='hidden' name='change' value='1' />
                        </form>
                      </td>
                      <td>₱{$item['price']}</td>
                      <td>₱{$itemTotal}</td>
                      <td><a href='?remove=$index' class='btn btn-danger btn-sm'>Remove</a></td>
                    </tr>";
          }
          echo "<tr><td colspan='4'>Total</td><td>₱{$total}</td></tr>";
      }
      ?>
    </tbody>
  </table>
  <button class="btn btn-primary" onclick="window.location.href='book.php';">Proceed to Payment</button>
</div>
</body>
</html>
