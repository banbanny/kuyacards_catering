<?php
include("php/conn.php");
require_once('tcpdf/tcpdf.php');

if (isset($_POST["next"])) {
    $name = $_POST["name"];
    $contact = $_POST["contact"];
    $address = $_POST["address"];
    $mode_of_payment = $_POST["mode_of_payment"];
    $date_delivery = $_POST["date"];
    $time_delivery = $_POST["time"];
    $ref_no = $_POST["ref_no"] ?? null; // Optional
    $order_id = $_POST["order_id"] ?? null; // Optional

    // Prepare the datetime for delivery
    $delivery_datetime = date('Y-m-d H:i:s', strtotime("$date_delivery $time_delivery"));

    // Insert into `customer_detail` table
    $sqlInsert = "INSERT INTO customer_detail (full_name, contact_number, address, mode_of_payment, date_time_delivery, reference_number, order_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param("ssssssi", $name, $contact, $address, $mode_of_payment, $delivery_datetime, $ref_no, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Reservation successfully added!')</script>";
    } else {
        echo "<script>alert('Error adding reservation. Please try again.')</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
    <title>Kuya Card's Catering</title>
</head>
<body>
    <header id="header" class="menu-container">
        <!-- Navigation omitted for brevity -->
    </header>

    <main class="container">
        <section class="content container">
            <form method="post">
                <div class="container text-center">
                    <div class="mb-3">
                        <label><strong>Full Name</strong></label>
                        <input type="text" class="form-control w-50 mx-auto" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label><strong>Contact Number</strong></label>
                        <input type="text" class="form-control w-50 mx-auto" name="contact" maxlength="11" required>
                    </div>
                    <div class="mb-3">
                        <label><strong>Address</strong></label>
                        <input type="text" class="form-control w-50 mx-auto" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label><strong>Reservation Date</strong></label>
                        <input type="date" class="form-control w-50 mx-auto" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label><strong>Time of Delivery</strong></label>
                        <select name="time" class="form-control w-50 mx-auto" required>
                            <option value="08:00:00">08:00 AM</option>
                            <option value="09:00:00">09:00 AM</option>
                            <option value="10:00:00">10:00 AM</option>
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label><strong>Mode of Payment</strong></label>
                        <select name="mode_of_payment" class="form-control w-50 mx-auto" id="paymentMethod" required>
                            <option value="Gcash">Gcash</option>
                            <option value="Cash on Delivery">Cash on Delivery</option>
                        </select>
                    </div>
                    <div id="gcashDetails" style="display: none;" class="mt-4">
                        <div class="text-center mb-3">
                            <img src="pics/GcashPayment.jpg" alt="Gcash QR Code" class="img-fluid" style="max-width: 200px;">
                        </div>
                        <div class="mb-3">
                            <label><strong>Reference Number</strong></label>
                            <input type="text" class="form-control w-50 mx-auto" name="ref_no">
                        </div>
                        <div class="mb-3">
                            <label><strong>Order ID</strong></label>
                            <input type="text" class="form-control w-50 mx-auto" name="order_id">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary" name="next">Submit</button>
                    </div>
                </div>
            </form>
        </section>
    </main>

    <script>
        document.getElementById('paymentMethod').addEventListener('change', function () {
            const gcashDetails = document.getElementById('gcashDetails');
            if (this.value === 'Gcash') {
                gcashDetails.style.display = 'block';
            } else {
                gcashDetails.style.display = 'none';
            }
        });
    </script>
</body>
</html>
