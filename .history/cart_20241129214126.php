<?php
session_start(); // Start the session

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to the cart (if POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_name'], $_POST['item_price'], $_POST['item_quantity'])) {
    // Validate the quantity to ensure it's a number and greater than 0
    $quantity = isset($_POST['item_quantity']) && is_numeric($_POST['item_quantity']) && $_POST['item_quantity'] < 0
                ? $_POST['item_quantity']
                : 1; // Default to 1 if the quantity is invalid

    // Check if the item already exists in the cart
    $itemExists = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['name'] == $_POST['item_name']) {
            $cartItem['quantity'] += $quantity; // Update the quantity if the item exists
            $itemExists = true;
            break;
        }
    }

    // If item doesn't exist, add it to the cart
    if (!$itemExists) {
        // Create the item
        $item = [
            'name' => $_POST['item_name'],
            'price' => $_POST['item_price'],
            'quantity' => $quantity // Make sure to initialize the quantity
        ];

        // Add the item to the cart (session)
        $_SESSION['cart'][] = $item;
    }
}

// Handle removing items from the cart (if GET request to remove item)
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]); // Remove item from cart
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array after removal
}

// Handle updating the quantity of an item (if POST request to update quantity)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $index = $_POST['update'];
    $change = isset($_POST['change']) ? $_POST['change'] : 0; // Amount to change the quantity by

    if (isset($_SESSION['cart'][$index])) {
        // Calculate the new quantity
        $newQuantity = $_SESSION['cart'][$index]['quantity'] + $change;

        // If the new quantity is greater than 0, update it
        if ($newQuantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $newQuantity;
        } else {
            // If quantity becomes 0 or less, remove the item from the cart
            unset($_SESSION['cart'][$index]);
        }
    }

    // Re-index the array after removal if any
    $_SESSION['cart'] = array_values($_SESSION['cart']);
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
              $itemQuantity = $item['quantity'];
              $itemTotal = $item['price'] * $itemQuantity;
              $total += $itemTotal;

              echo "<tr>
                      <td>{$item['name']}</td>
                      <td>
                        <form method='POST' class='d-inline'>
                          <!-- Subtract quantity -->
                          <button class='btn btn-secondary btn-sm' type='submit' name='update' value='$index'>
                            -
                          </button>
                          <input type='hidden' name='change' value='-1' />
                          <span>$itemQuantity</span> 
                          <!-- Add quantity -->
                          <button class='btn btn-secondary btn-sm' type='submit' name='update' value='$index'>
                            +
                          </button>
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
