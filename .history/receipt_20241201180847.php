<?php
ob_start(); // Start output buffering

include("php/catering_db.php");
require_once('tcpdf/tcpdf.php');

if (isset($_POST["next"])) {
    // Sanitize and format inputs using PDO
    $dateFormat = date('Y-m-d', strtotime($_POST['date']));
    $time = $_POST["time"];
    $refNo = isset($_POST["ref_no"]) ? $_POST["ref_no"] : null;
    $paymentMethod = $_POST["mode_of_payment"];

    // Ensure required fields are not empty
    if (empty($dateFormat) || empty($time) || empty($paymentMethod)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        exit;
    }24an

    // Check if the selected date and time are available
    $sql1 = "SELECT * FROM bookingdates WHERE ('$dateFormat' < CURDATE() OR (date = :date AND time = :time))";
    $stmt = $db->prepare($sql1);
    $stmt->bindParam(':date', $dateFormat);
    $stmt->bindParam(':time', $time);
    $stmt->execute();
    $numRows = $stmt->rowCount();

    if ($numRows > 0) {
        echo "<script>alert('The selected date and time are not available.');</script>";
    } else {
        // Insert booking information into customer_detail using PDO
        $sqlInsertBooking = "INSERT INTO customer_detail (date_of_delivery, time_of_delivery, mode_of_payment, reference_number) 
                             VALUES (:date_of_delivery, :time_of_delivery, :mode_of_payment, :reference_number)";
        $stmt1 = $db->prepare($sqlInsertBooking);
        $stmt1->bindParam(':date_of_delivery', $dateFormat);
        $stmt1->bindParam(':time_of_delivery', $time);
        $stmt1->bindParam(':mode_of_payment', $paymentMethod);
        $stmt1->bindParam(':reference_number', $refNo);

        if ($stmt1->execute()) {
            $bookingID = $db->lastInsertId();  // Get the last inserted booking ID

            // Insert into billing table
            $sqlInsertBilling = "INSERT INTO billing (bookingID, status, amount) VALUES (:bookingID, 'Pending', '1')";
            $stmt2 = $db->prepare($sqlInsertBilling);
            $stmt2->bindParam(':bookingID', $bookingID);
            $stmt2->execute();

            // Redirect to receipt.php with bookingID
            header("Location: receipt.php?bookingID=" . $bookingID);
            exit;
        } else {
            echo "<script>alert('An error occurred while processing your booking. Please try again later.');</script>";
        }
    }
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

