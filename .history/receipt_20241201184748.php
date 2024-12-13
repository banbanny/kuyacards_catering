<?php
session_start();
ob_start(); // Start output buffering

include("php/catering_db.php");
require_once('tcpdf/tcpdf.php');

$bookingID = $_GET['bookingID'];

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

echo $user_id;

echo $bookingID;


$stmt = $db->prepare("SELECT * FROM customer_detail WHERE id = :id");
$stmt->execute([':id' => $bookingID]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($orders) {
  foreach ($orders as $order) {
      echo "<p><strong>id:</strong> " . htmlspecialchars($order['id']) . "<br>";
      echo "<strong>date:</strong> " . htmlspecialchars($order['date_of_delivery']) . "<br>";
      echo "<strong>time:</strong> $" . htmlspecialchars($order['time_of_delivery']) . "<br>";
      echo "<strong>method:</strong> $" . htmlspecialchars($order['mode_of_payment']) . "<br>";
  }
} else {
  echo "No orders found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-header h1 {
            margin: 0;
        }
        .receipt-header p {
            margin: 5px 0;
        }
        .order-summary, .customer-info, .delivery-info {
            margin-bottom: 20px;
        }
        .order-summary th, .order-summary td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .order-summary th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 5px;
        }
        .delivery-info {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="receipt-header">
        <h1>Receipt</h1>
        <p>Order Number: <strong>#ORDER_ID</strong></p>
        <p>Date of Order: <strong>MM/DD/YYYY</strong></p>
        <p>Time of Order: <strong>HH:MM AM/PM</strong></p>
    </div>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <table class="order-summary">
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
            <!-- Repeat for each ordered item -->
             <?php
            <tr>
                <td>Item Name</td>
                <td>1</td>
                <td>$10.00</td>
                <td>$10.00</td>
            </tr>
            <!-- End of repeating items -->
            <tr>
                <td colspan="3" class="total">Total</td>
                <td class="total">$10.00</td>
            </tr>
            
        </table>
    </div>

    <div class="customer-info">
        <h2>Customer Information</h2>
        <table class="info-table">
            <tr>
                <td><strong>Name:</strong> John Doe</td>
                <td><strong>Email:</strong> john@example.com</td>
            </tr>
            <tr>
                <td><strong>Address:</strong> 123 Main St, City</td>
                <td><strong>Phone:</strong> (123) 456-7890</td>
            </tr>
        </table>
    </div>

    <div class="delivery-info">
        <h2>Delivery Information</h2>
        <table class="info-table">
            <tr>
                <td><strong>Delivery Address:</strong> 456 Delivery Rd, City</td>
            </tr>
            <tr>
                <td><strong>Mode of Payment:</strong> GCash</td>
            </tr>
            <tr>
                <td><strong>Reference Number:</strong> 1234567890</td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>

