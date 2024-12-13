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
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex the cart after removal
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

// Handle selecting a package for checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_checkout'])) {
    $selectedIndex = (int)$_POST['select_checkout'];
    if (isset($_SESSION['cart'][$selectedIndex])) {
        $_SESSION['selected_checkout'] = $selectedIndex; // Store the selected package index
    }
}

// Handle changing the selected package (de-selecting)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_selection'])) {
    unset($_SESSION['selected_checkout']); // Reset the selected package
}

// Handle updating the quantity of a specific cart item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'], $_POST['item_quantity'])) {
    $index = $_POST['update_quantity'];
    $newQuantity = (int)$_POST['item_quantity'];

    if ($newQuantity > 0 && isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $newQuantity; // Update the quantity
    }
}

// Handle proceeding to payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_payment'])) {
    if (!isset($_SESSION['selected_checkout'])) {
        echo "<script>alert('Please select a package for checkout before proceeding.');</script>";
    } else {
        $selectedIndex = $_SESSION['selected_checkout'];
        $selectedItem = $_SESSION['cart'][$selectedIndex];

        // Ensure quantity is set and valid
        $quantity = isset($selectedItem['quantity']) && is_numeric($selectedItem['quantity']) && $selectedItem['quantity'] > 0
            ? $selectedItem['quantity']
            : 1;

        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Save the selected item to the database
        try {
            $stmt = $db->prepare("INSERT INTO pending_orders (item_name, quantity, price, total, user_id) 
                                  VALUES (:item_name, :quantity, :price, :total, :user_id)");
            $stmt->execute([
                ':item_name' => $selectedItem['name'],
                ':quantity' => $quantity,
                ':price' => $selectedItem['price'],
                ':total' => $selectedItem['price'] * $quantity,
                ':user_id' => $user_id
            ]);

            // Clear the cart and selection after successful checkout
            unset($_SESSION['cart']);
            unset($_SESSION['selected_checkout']);

            echo "<script>alert('Your order has been placed in the pending orders!'); window.location.href = 'book.php';</script>";
        } catch (PDOException $e) {
            echo "Error: Unable to place the order. " . $e->getMessage();
        }
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
        $total = 0; // Initialize total to zero
        if (empty($_SESSION['cart'])) {
            echo '<tr><td colspan="5">Your cart is empty.</td></tr>';
        } else {
            foreach ($_SESSION['cart'] as $index => $item) {
                // Ensure quantity exists before referencing it
                $itemQuantity = isset($item['quantity']) ? (int)$item['quantity'] : 1; // Default to 1 if not set
                $itemTotal = $item['price'] * $itemQuantity;

                echo "<tr>
                        <td>{$item['name']}</td>
                        <td>
                            <form method='POST' class='d-inline'>
                                <input type='number' name='item_quantity' value='{$itemQuantity}' min='1' class='form-control' required>
                                <button type='submit' name='update_quantity' value='$index' class='btn btn-warning btn-sm mt-2'>Update Quantity</button>
                            </form>
                        </td>
                        <td>₱{$item['price']}</td>
                        <td>₱{$itemTotal}</td>
                        <td>
                            <form method='POST' class='d-inline'>
                                " . (isset($_SESSION['selected_checkout']) && $_SESSION['selected_checkout'] === $index
                    ? "<button class='btn btn-warning' type='submit' name='change_selection'>Change Selected Package</button>"
                    : "<button class='btn btn-success' type='submit' name='select_checkout' value='$index'>Select for Checkout</button>") . "
                            </form>
                            <a href='?remove=$index' class='btn btn-danger btn-sm'>Remove</a>
                        </td>
                    </tr>";
            }
        }

        // Check if an item is selected for checkout and calculate its total
        if (isset($_SESSION['selected_checkout'])) {
            $selectedIndex = $_SESSION['selected_checkout'];
            $selectedItem = $_SESSION['cart'][$selectedIndex];
            $selectedItemQuantity = isset($selectedItem['quantity']) ? (int)$selectedItem['quantity'] : 1; // Default to 1
            $selectedItemTotal = $selectedItem['price'] * $selectedItemQuantity;
            $total = $selectedItemTotal;
            echo "<tr><td colspan='4'>Total for Selected Package</td><td>₱{$total}</td></tr>";
        } else {
            echo "<tr><td colspan='4'>Total</td><td>₱0</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <?php if (!empty($_SESSION['cart']) && isset($_SESSION['selected_checkout'])): ?>
        <form method="POST" class="d-inline">
            <button class="btn btn-primary" type="submit" name="proceed_to_payment">Proceed to Payment</button>
        </form>
    <?php endif; ?>

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
