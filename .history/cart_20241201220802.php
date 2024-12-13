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

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding an item to the cart (only one package allowed)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_name'], $_POST['item_price'])) {
    $itemName = $_POST['item_name'];
    $itemPrice = (int)$_POST['item_price'];
    $quantity = isset($_POST['item_quantity']) && is_numeric($_POST['item_quantity']) && $_POST['item_quantity'] > 0
        ? (int)$_POST['item_quantity']
        : 1;

    // Replace any existing item in the cart with the new one
    $_SESSION['cart'] = [
        [
            'name' => $itemName,
            'price' => $itemPrice,
            'quantity' => $quantity
        ]
    ];
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    unset($_SESSION['cart']);
    $_SESSION['cart'] = [];
}

// Handle proceeding to payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_payment'])) {
    if (empty($_SESSION['cart'])) {
        echo "<script>alert('Your cart is empty. Please add a package before proceeding.');</script>";
    } else {
        $selectedItem = $_SESSION['cart'][0]; // There's only one item allowed in the cart
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Save the selected item to the database
        try {
            $stmt = $db->prepare("INSERT INTO pending_orders (item_name, quantity, price, total, user_id) 
                                  VALUES (:item_name, :quantity, :price, :total, :user_id)");
            $stmt->execute([
                ':item_name' => $selectedItem['name'],
                ':quantity' => $selectedItem['quantity'],
                ':price' => $selectedItem['price'],
                ':total' => $selectedItem['price'] * $selectedItem['quantity'],
                ':user_id' => $user_id
            ]);

            // Clear the cart after successful checkout
            unset($_SESSION['cart']);
            echo "<script>alert('Your order has been placed in the pending orders!'); window.location.href = 'book.php';</script>";
        } catch (PDOException $e) {
            echo "Error: Unable to place the order. " . $e->getMessage();
        }
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
            $item = $_SESSION['cart'][0]; // Only one item in the cart
            $itemTotal = $item['price'] * $item['quantity'];

            echo "<tr>
                    <td>{$item['name']}</td>
                    <td>{$item['quantity']}</td>
                    <td>₱{$item['price']}</td>
                    <td>₱{$itemTotal}</td>
                    <td><a href='?remove=1' class='btn btn-danger btn-sm'>Remove</a></td>
                </tr>";
            echo "<tr><td colspan='4'>Total</td><td>₱{$itemTotal}</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <form method="POST" class="d-inline">
        <button class="btn btn-primary" type="submit" name="proceed_to_payment">Proceed to Payment</button>
    </form>
    <a href="home.php" class="btn btn-secondary">Back to Home</a>
</div>
</body>
</html>
