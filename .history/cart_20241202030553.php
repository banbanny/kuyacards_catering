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

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $itemId = (int)$_POST['item_id'];
    $quantity = isset($_POST['item_quantity']) && is_numeric($_POST['item_quantity']) && $_POST['item_quantity'] > 0
        ? (int)$_POST['item_quantity']
        : 1;

    // Fetch item details from the database
    try {
        $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Add the item to the cart
            $_SESSION['cart'][] = [
                'name' => $item['item_name'],
                'price' => (float)$item['price'],
                'quantity' => $quantity
            ];
        }
    } catch (PDOException $e) {
        die('Error fetching item: ' . $e->getMessage());
    }
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex the cart after removal
    }
}

// Other handlers remain unchanged...

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
        $total = 0;
        if (empty($_SESSION['cart'])) {
            echo '<tr><td colspan="5">Your cart is empty.</td></tr>';
        } else {
            foreach ($_SESSION['cart'] as $index => $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                echo "<tr>
                        <td>{$item['name']}</td>
                        <td>{$item['quantity']}</td>
                        <td>₱{$item['price']}</td>
                        <td>₱{$itemTotal}</td>
                        <td>
                            <a href='?remove=$index' class='btn btn-danger btn-sm'>Remove</a>
                        </td>
                    </tr>";
                $total += $itemTotal;
            }
        }
        ?>
        <tr>
            <td colspan="3">Total</td>
            <td>₱<?= $total ?></td>
            <td></td>
        </tr>
        </tbody>
    </table>

    <h3>Available Packages</h3>
    <table class="table">
        <thead>
        <tr>
            <th>Item</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        try {
            $stmt = $db->query("SELECT * FROM orders");
            $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($packages as $package) {
                echo "<tr>
                        <td>{$package['item_name']}</td>
                        <td>₱{$package['price']}</td>
                        <td>
                            <form method='POST'>
                                <input type='hidden' name='item_id' value='{$package['order_id']}'>
                                <input type='number' name='item_quantity' value='1' min='1' class='form-control d-inline' style='width: 80px;' required>
                                <button type='submit' name='add_to_cart' class='btn btn-primary'>Add to Cart</button>
                            </form>
                        </td>
                    </tr>";
            }
        } catch (PDOException $e) {
            echo '<tr><td colspan="3">Error fetching packages: ' . $e->getMessage() . '</td></tr>';
        }
        ?>
        </tbody>
    </table>

    <a href="home.php" class="btn btn-secondary">Back to Home</a>
</div>
</body>
</html>
