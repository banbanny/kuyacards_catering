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

              foreach ($orders as $order) {
                echo '<tr>
                      <td>'.$order['item_name'].'</td>
                      <td>'.$order['quantity'].'</td>
                      <td>Php '.$order['price'].'</td>
                      <td>Php '.$order['price'] * $order['quantity'].'</td>
                    </tr>';
              }
            echo '
                <!-- End of repeating items -->
                <tr>
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
          echo '
             <tr>
                <td><strong>Address:</strong>'.$order['address'].'</td>
                <td><strong>Phone:</strong>'.$order['contact_number'].'</td>
            </tr>';
        ?>
            
           
        </table>
    </div>

    <div class="delivery-info">
        <h2>Delivery Information</h2>
        <table class="info-table">

        <?php

          foreach ($orders as $order) {
            echo ' <tr>
                <td><strong>Delivery Address:</strong> '.$order['address'].'</td>
            </tr>';
          }

          if($order['mode_of_payment'] != '') {
            echo '
             <tr>
                <td><strong>Mode of Payment:</strong> '.$order['mode_of_payment'].'</td>
            </tr>';
            echo '
               <tr>
                  <td><strong>Reference Number:</strong> '.$order['reference_number'].'</td>
              </tr>';
          } else {
            echo '
             <tr>
                <td><strong>Mode of Payment:</strong> Cash on Delivery</td>
            </tr>';
          }
        ?>
        </table>
    </div>
</div>

</body>
</html>

