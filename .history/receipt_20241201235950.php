<?php
session_start();
ob_start(); // Start output buffering

include("php/catering_db.php");
require_once('tcpdf/tcpdf.php');

$bookingID = $_GET['bookingID'];

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$stmt = $db->prepare("SELECT * FROM customer_detail cd INNER JOIN pending_orders po ON po.id = cd.pending_order_id INNER JOIN users u ON u.id = cd.user_id WHERE cd.id = :id ");
$stmt->execute([':id' => $bookingID]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            background-color: #a7d5ff;
            color: #333;
        }
        .receipt-container {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1b6ab2;
            padding-bottom: 10px;
        }
        .receipt-header h1 {
            margin: 0;
            color: ##1b6ab2;
            font-size: 24px;
        }
        .receipt-header p {
            margin: 5px 0;
            color: #555;
        }
        .order-summary, .customer-info, .delivery-info {
            margin-bottom: 30px;
        }
        .order-summary h2, .customer-info h2, .delivery-info h2 {
            color: #1b6ab2;
            margin-bottom: 15px;
            font-size: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .order-summary table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .order-summary th, .order-summary td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .order-summary th {
            background-color: #f2f2f2;
            color: #333;
        }
        .total {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 8px;
            vertical-align: top;
        }
        .info-table td strong {
            color: #1b6ab2;
        }
        .delivery-info {
            text-align: left;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
            font-size: 12px;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .button-container a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .button-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="receipt-header">
        <h1>Receipt</h1>
        <?php foreach($orders as $order) { 
         echo '<p>Order Number: <strong>'.$order['pending_order_id'].'</strong></p>';
         echo '<p>Date of Order: <strong>'.$order['date_of_delivery'].'</strong></p>';
         echo '<p>Time of Order: <strong>'.$order['time_of_delivery'].'</strong></p>';
       } ?>
    </div>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <table>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
            <?php
              foreach ($orders as $order) {
                echo '<tr>
                      <td>'.$order['item_name'].'</td>
                      <td>'.$order['quantity'].'</td>
                      <td>Php '.$order['price'].'</td>
                      <td>Php '.$order['price'] * $order['quantity'].'</td>
                    </tr>';
              }
            echo '<tr>
                    <td colspan="3" class="total">Total</td>
                    <td class="total">Php '.$order['total'].'</td>
                </tr>';
            ?>
        </table>
    </div>

    <div class="customer-info">
        <h2>Customer Information</h2>
        <table class="info-table">
        <?php
          foreach ($orders as $order) {
            echo '<tr>
                    <td><strong>Name:</strong> '.$order['full_name'].'</td>
                    <td><strong>Email:</strong> '.$order['email'].'</td>
                </tr>';
          }
          echo '<tr>
                <td><strong>Address:</strong> '.$order['address'].'</td>
                <td><strong>Phone:</strong> '.$order['contact_number'].'</td>
            </tr>';
        ?>
        </table>
    </div>

    <div class="delivery-info">
        <h2>Delivery Information</h2>
        <table class="info-table">
        <?php
          foreach ($orders as $order) {
            echo '<tr>
                    <td><strong>Delivery Address:</strong> '.$order['address'].'</td>
                </tr>';
          }
          if($order['mode_of_payment'] != '') {
            echo '<tr>
                    <td><strong>Mode of Payment:</strong> '.$order['mode_of_payment'].'</td>
                </tr>';
            echo '<tr>
                    <td><strong>Reference Number:</strong> '.$order['reference_number'].'</td>
                </tr>';
          } else {
            echo '<tr>
                    <td><strong>Mode of Payment:</strong> Cash on Delivery</td>
                </tr>';
          }
        ?>
        </table>
    </div>
</div>

<div class="button-container">
    <a href="home.php">Continue Ordering</a>
</div>

<div class="footer">
    <p>&copy; <?php echo date('Y'); ?> Catering Services. All rights reserved.</p>
</div>

</body>
</html>
