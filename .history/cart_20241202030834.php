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

// Fetch orders from the database
try {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Use user_id if logged in
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error fetching orders: ' . $e->getMessage());
}

// Handle selecting a package for checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_checkout'])) {
    $selectedOrderId = (int)$_POST['select_checkout'];
    $_SESSION['selected_checkout'] = $selectedOrderId; // Store the selected package ID
}

// Handle changing the selected package (de-selecting)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_selection'])) {
    unset($_SESSION['selected_checkout']); // Reset the selected package
}

// Handle proceeding to payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_payment'])) {
    if (!isset($_SESSION['selected_checkout'])) {
        echo "<script>alert('Please select a package for checkout before proceeding.');</script>";
    } else {
        $selectedOrderId = $_SESSION['selected_checkout'];
        try {
            // Update the order status or move it to pending_orders
            $stmt = $db->prepare("INSERT INTO pending_orders (item_name, quantity, price, total, user_id)
                                  SELECT item_name, quantity, price, total, user_id
                                  FROM orders
                                  WHERE order_id = :order_id");
            $stmt->execute([':order_id' => $selectedOrderId]);

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
        if (empty($orders)) {
            echo '<tr><td colspan="5">Your cart is empty.</td></tr>';
        } else {
            foreach ($orders as $order) {
                $isSelected = isset($_SESSION['selected_checkout']) && $_SESSION['selected_checkout'] == $order['order_id'];
                echo "<tr>
                        <td>{$order['item_name']}</td>
                        <td>{$order['quantity']}</td>
                        <td>₱{$order['price']}</td>
                        <td>₱{$order['total']}</td>
                        <td>
                            <form method='POST' class='d-inline'>
                                " . ($isSelected
                    ? "<button class='btn btn-warning' type='submit' name='change_selection'>Change Selected Package</button>"
                    : "<button class='btn btn-success' type='submit' name='select_checkout' value='{$order['order_id']}'>Select for Checkout</button>") . "
                            </form>
                        </td>
                    </tr>";
            }
        }

        // Display the total for the selected package
        if (isset($_SESSION['selected_checkout'])) {
            $selectedOrderId = $_SESSION['selected_checkout'];
            $selectedOrder = array_filter($orders, fn($order) => $order['order_id'] == $selectedOrderId);
            if ($selectedOrder) {
                $selectedOrder = array_values($selectedOrder)[0];
                echo "<tr><td colspan='4'>Total for Selected Package</td><td>₱{$selectedOrder['total']}</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Total</td><td>₱0</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <?php if (!empty($orders) && isset($_SESSION['selected_checkout'])): ?>
        <form method="POST" class="d-inline">
            <button class="btn btn-primary" type="submit" name="proceed_to_payment">Proceed to Payment</button>
        </form>
    <?php endif; ?>

    <a href="home.php" class="btn btn-secondary">Back to Home</a>
</div>
</body>
</html>
