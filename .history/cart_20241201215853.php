<?php
session_start();

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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['select_for_checkout'])) {
    $selectedIndex = (int)$_POST['select_for_checkout'];
    if (isset($_SESSION['cart'][$selectedIndex])) {
        $_SESSION['checkout_selection'] = $selectedIndex;
    }
}

// Handle deselecting a package
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deselect'])) {
    unset($_SESSION['checkout_selection']);
}

// Handle cart submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proceed_to_payment'])) {
    if (!isset($_SESSION['checkout_selection'])) {
        echo "<script>alert('Please select a package to proceed to payment.'); window.location.href = 'cart.php';</script>";
        exit;
    }

    $selectedIndex = $_SESSION['checkout_selection'];
    $item = $_SESSION['cart'][$selectedIndex];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        $stmt = $db->prepare("INSERT INTO pending_orders (item_name, quantity, price, total, user_id) 
                              VALUES (:item_name, :quantity, :price, :total, :user_id)");
        $stmt->execute([
            ':item_name' => $item['name'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price'],
            ':total' => $item['price'] * $item['quantity'],
            ':user_id' => $user_id
        ]);

        // Clear the selected item from cart and reset selection
        unset($_SESSION['cart'][$selectedIndex]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        unset($_SESSION['checkout_selection']);

        echo "<script>alert('Your order has been placed in the pending orders!'); window.location.href = 'book.php';</script>";
    } catch (PDOException $e) {
        echo "Error: Unable to place the order. " . $e->getMessage();
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
                $itemQuantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                $itemTotal = $item['price'] * $itemQuantity;
                $total += $itemTotal;

                echo "<tr>
                        <td>{$item['name']}
                            " . (isset($_SESSION['checkout_selection']) && $_SESSION['checkout_selection'] === $index
                    ? "<span class='badge bg-success'>Selected for Checkout</span>"
                    : "") . "
                        </td>
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
                        <td>
                            <a href='?remove=$index' class='btn btn-danger btn-sm'>Remove</a>
                            <form method='POST' class='d-inline'>
                                <button class='btn btn-info btn-sm' type='submit' name='select_for_checkout' value='$index' 
                                    " . (isset($_SESSION['checkout_selection']) && $_SESSION['checkout_selection'] === $index ? "disabled" : "") . ">
                                    Select for Checkout
                                </button>
                            </form>
                        </td>
                    </tr>";
            }
            echo "<tr><td colspan='4'>Total</td><td>₱{$total}</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <form method="POST" class="d-inline">
        <button class="btn btn-primary" type="submit" name="proceed_to_payment">Proceed to Payment</button>
    </form>
    <form method="POST" class="d-inline">
        <button class="btn btn-warning" type="submit" name="deselect">Deselect</button>
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
                        <td><a href='#' class='btn btn-secondary btn-sm'>View</a></td>
                    </tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
