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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_name'], $_POST['item_price'])) {
    $itemName = $_POST['item_name'];
    $itemPrice = (int)$_POST['item_price'];
    $quantity = isset($_POST['item_quantity']) && is_numeric($_POST['item_quantity']) && $_POST['item_quantity'] > 0
        ? (int)$_POST['item_quantity']
        : 1;

    $itemExists = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['name'] === $itemName) {
            $cartItem['quantity'] += $quantity;
            $itemExists = true;
            break;
        }
    }

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
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Handle updating item quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $index = (int)$_POST['update'];
    $change = isset($_POST['change']) ? (int)$_POST['change'] : 0;

    if (isset($_SESSION['cart'][$index])) {
        // Ensure the quantity exists and is valid
        if (!isset($_SESSION['cart'][$index]['quantity'])) {
            $_SESSION['cart'][$index]['quantity'] = 1; // Default to 1 if not set
        }

        $currentQuantity = (int)$_SESSION['cart'][$index]['quantity'];
        $newQuantity = $currentQuantity + $change;

        if ($newQuantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $newQuantity;
        } else {
            unset($_SESSION['cart'][$index]);
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Handle cart submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proceed_to_payment'])) {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        foreach ($_SESSION['cart'] as $item) {
            $quantity = (int)$item['quantity'];

            $stmt = $db->prepare("INSERT INTO pending_orders (item_name, quantity, price, total, user_id) 
                                  VALUES (:item_name, :quantity, :price, :total, :user_id)");
            $stmt->execute([
                ':item_name' => $item['name'],
                ':quantity' => $quantity,
                ':price' => $item['price'],
                ':total' => $item['price'] * $quantity,
                ':user_id' => $user_id
            ]);
        }

        unset($_SESSION['cart']);
        echo "<script>alert('Your order has been placed in the pending orders!'); window.location.href = 'book.php';</script>";
    } catch (PDOException $e) {
        echo "Error: Unable to place the order. " . $e->getMessage();
    }
}

// Handle canceling an order
if (isset($_GET['cancel_order'])) {
    $order_id = (int)$_GET['cancel_order'];

    try {
        $stmt = $db->prepare("DELETE FROM pending_orders WHERE id = :id AND status = 'pending'");
        $stmt->execute([':id' => $order_id]);
        echo "<script>alert('Order canceled successfully!'); window.location.href = 'cart.php';</script>";
    } catch (PDOException $e) {
        echo "Error: Unable to cancel the order. " . $e->getMessage();
    }
}

// Fetch user's orders
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$orders = [];

if ($user_id) {
    try {
        $stmt = $db->prepare("SELECT id, item_name, quantity, price, total, status FROM pending_orders WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: Unable to fetch orders. " . $e->getMessage();
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
                // Ensure quantity exists and is valid
                $itemQuantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                $itemTotal = $item['price'] * $itemQuantity;
                $total += $itemTotal;

                echo "<tr>
                        <td>{$item['name']}</td>
                        <td>
                            <form method='POST' class='d-inline'>
                                <button class='btn btn-secondary btn-sm' type='submit' name='update' value='$index'>-</button>
                                <input type='hidden' name='change' value='-1' />
                            </form>
                            <span>{$itemQuantity}</span>
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

    <form method="POST" class="d-inline">
        <?php
        
        <button class="btn btn-primary" type="submit" name="proceed_to_payment">Proceed to Payment</button>
    </form>
    <a href="home.php" class="btn btn-secondary">Back to Home</a>

    <h2 class="mt-5">Your Orders</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (empty($orders)) {
            echo '<tr><td colspan="6">You have no orders yet.</td></tr>';
        } else {
            foreach ($orders as $order) {
                echo "<tr>
                        <td>{$order['item_name']}</td>
                        <td>{$order['quantity']}</td>
                        <td>₱{$order['price']}</td>
                        <td>₱{$order['total']}</td>
                        <td>{$order['status']}</td>
                        <td>";
                if ($order['status'] === 'pending') {
                    echo "<a href='?cancel_order={$order['id']}' class='btn btn-danger btn-sm'>Cancel</a>";
                } else {
                    echo "N/A";
                }
                echo "</td></tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
