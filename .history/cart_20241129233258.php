<?php
session_start(); // Start the session

// Database connection setup
$db_user = "root";
$db_pass = "";
$db_name = "catering_db";

try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=' . $db_name . ';charset=utf8mb4', $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Ensure the user is logged in before proceeding
if (!isset($_SESSION['user_id'])) {
    // Redirect to index.php if the user is not logged in
    header("Location: login.php");
    exit();
}

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_name'], $_POST['item_price'])) {
    $itemName = trim($_POST['item_name']);
    $itemPrice = (int)$_POST['item_price'];
    $quantity = isset($_POST['item_quantity']) && is_numeric($_POST['item_quantity']) && $_POST['item_quantity'] > 0
        ? (int)$_POST['item_quantity']
        : 1;

    // Check if item already exists in the cart
    $itemExists = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['name'] === $itemName) {
            $cartItem['quantity'] += $quantity;
            $itemExists = true;
            break;
        }
    }

    // Add the item if it doesn't already exist
    if (!$itemExists) {
        $_SESSION['cart'][] = [
            'name' => $itemName,
            'price' => $itemPrice,
            'quantity' => $quantity
        ];
    }
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }
}

// Handle updating item quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $index = (int)$_POST['update'];
    $change = isset($_POST['change']) ? (int)$_POST['change'] : 0;

    if (isset($_SESSION['cart'][$index])) {
        $newQuantity = $_SESSION['cart'][$index]['quantity'] + $change;

        if ($newQuantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $newQuantity;
        } else {
            unset($_SESSION['cart'][$index]); // Remove item if quantity drops to zero
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }
}

// Handle cart submission (insert into orders table)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proceed_to_payment'])) {
    $user_id = $_SESSION['user_id'];

    if (!empty($_SESSION['cart'])) {
        try {
            // Insert each item into the orders table
            foreach ($_SESSION['cart'] as $item) {
                $stmt = $db->prepare("INSERT INTO orders (item_name, quantity, price, total, user_id) VALUES (:item_name, :quantity, :price, :total, :user_id)");
                $stmt->execute([
                    ':item_name' => $item['name'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price'],
                    ':total' => $item['price'] * $item['quantity'],
                    ':user_id' => $user_id
                ]);
            }

            // Clear the cart after successful submission
            unset($_SESSION['cart']);
            echo "<script>alert('Your order has been placed successfully!'); window.location.href = 'book.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Failed to process the order. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Your cart is empty. Please add items before proceeding to payment.');</script>";
    }
}
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

  <!-- Form to handle payment submission -->
  <form method="POST">
    <button class="btn btn-primary" type="submit" name="proceed_to_payment">Proceed to Payment</button>
  </form>
</div>
</body>
</html>
