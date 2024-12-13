<?php
session_start(); // Start the session

// Include the database connection file (Ensure the path is correct)
require_once('php/catering_db.php');  // Include database connection

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ensure the order ID is set in the session, create one if it doesn't exist
if (!isset($_SESSION['order_id'])) {
    $_SESSION['order_id'] = 1; // You can use a more dynamic approach for order ID, e.g., by using a unique ID or based on user session
}

// Handle adding items to the cart (if POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_name'], $_POST['item_price'], $_POST['item_quantity'])) {
    // Validate the quantity
    $quantity = filter_var($_POST['item_quantity'], FILTER_VALIDATE_INT);
    $quantity = ($quantity && $quantity > 0) ? $quantity : 1;

    $newItem = [
        'name' => $_POST['item_name'],
        'price' => $_POST['item_price'],
        'quantity' => $quantity,
    ];

    // Insert into orders table
    $orderId = $_SESSION['order_id']; // Use dynamic order ID

    try {
        $stmt = $db->prepare("INSERT INTO orders (order_id, item_name, item_price, quantity) VALUES (:order_id, :item_name, :item_price, :quantity)");
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':item_name', $newItem['name']);
        $stmt->bindParam(':item_price', $newItem['price']);
        $stmt->bindParam(':quantity', $newItem['quantity']);
        $stmt->execute();
    } catch (PDOException $e) {
        die('Error inserting order item: ' . $e->getMessage());
    }

    // Add item to session cart
    $found = false;
    foreach ($_SESSION['cart'] as &$existingItem) {
        if ($existingItem['name'] === $newItem['name']) {
            $existingItem['quantity'] += $newItem['quantity'];
            $found = true;
            break;
        }
    }
    unset($existingItem);

    if (!$found) {
        $_SESSION['cart'][] = $newItem;
    }
}

// Fetch cart items from the database for the current order
$orderId = $_SESSION['order_id']; // Use dynamic order ID

try {
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = :order_id");
    $stmt->bindParam(':order_id', $orderId);
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Load cart items into session
    if (!empty($cartItems)) {
        $_SESSION['cart'] = [];
        foreach ($cartItems as $item) {
            $_SESSION['cart'][] = [
                'name' => $item['item_name'],
                'price' => $item['item_price'],
                'quantity' => $item['quantity']
            ];
        }
    }
} catch (PDOException $e) {
    die('Error fetching order items: ' . $e->getMessage());
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        $itemName = $_SESSION['cart'][$index]['name'];
        $orderId = $_SESSION['order_id']; // Use dynamic order ID

        // Remove from the database
        try {
            $stmt = $db->prepare("DELETE FROM orders WHERE order_id = :order_id AND item_name = :item_name");
            $stmt->bindParam(':order_id', $orderId);
            $stmt->bindParam(':item_name', $itemName);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Error removing order item: ' . $e->getMessage());
        }

        unset($_SESSION['cart'][$index]);
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Handle updating item quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'], $_POST['quantity'])) {
    $index = $_POST['update'];
    $newQuantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $newQuantity = ($newQuantity && $newQuantity > 0) ? $newQuantity : 1;

    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $newQuantity;

        // Update the database
        $orderId = $_SESSION['order_id']; // Use dynamic order ID
        $itemName = $_SESSION['cart'][$index]['name'];

        try {
            $stmt = $db->prepare("UPDATE orders SET quantity = :quantity WHERE order_id = :order_id AND item_name = :item_name");
            $stmt->bindParam(':order_id', $orderId);
            $stmt->bindParam(':item_name', $itemName);
            $stmt->bindParam(':quantity', $newQuantity);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Error updating order item: ' . $e->getMessage());
        }

        if ($newQuantity <= 0) {
            unset($_SESSION['cart'][$index]);

            // Remove from the database
            try {
                $stmt = $db->prepare("DELETE FROM orders WHERE order_id = :order_id AND item_name = :item_name");
                $stmt->bindParam(':order_id', $orderId);
                $stmt->bindParam(':item_name', $itemName);
                $stmt->execute();
            } catch (PDOException $e) {
                die('Error removing order item: ' . $e->getMessage());
            }
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
    }
    .wave {
        position: relative;
        width: 100%;
        height: 150px;
        background: #6c63ff;
        overflow: hidden;
    }
    .wave svg {
        position: absolute;
        top: 0;
        left: 0;
        width: 200%;
        height: 100%;
        animation: wave-motion 10s linear infinite;
    }

    @keyframes wave-motion {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }

    .container {
        margin-top: 0px; /* Adjusted so the content no longer overlaps with the wave */
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        color: #6c63ff;
    }
    .btn-primary {
        background-color: #6c63ff;
        border-color: #6c63ff;
    }
    .btn-primary:hover {
        background-color: #5847c5;
        border-color: #5847c5;
    }
  </style>
</head>
<body>

<!-- Wave Animation -->
<div class="wave">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
            <linearGradient id="wave-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color: lightblue; stop-opacity: 1" />
                <stop offset="100%" style="stop-color: darkblue; stop-opacity: 1" />
            </linearGradient>
            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
        </defs>
        <g class="parallax">
        <use xlink:href="#gentle-wave" x="48" y="0" fill="url(#wave-gradient)" />
        <use xlink:href="#gentle-wave" x="48" y="3" fill="url(#wave-gradient)" />
        <use xlink:href="#gentle-wave" x="48" y="5" fill="url(#wave-gradient)" />
        </g>
    </svg>
</div>

<!-- Main content -->
<div class="container">
    <h2>Your Cart</h2>
    <div class="row">
        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>₱<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="99" />
                                <button type="submit" name="update" value="<?= $index ?>" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                        <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        <td>
                            <a href="?remove=<?= $index ?>" class="btn btn-sm btn-danger">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>