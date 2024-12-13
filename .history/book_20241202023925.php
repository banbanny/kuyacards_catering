<?php
session_start();
ob_start(); // Start output buffering

$user_id = $_SESSION['user_id'];

include("php/catering_db.php");

if (isset($_POST["next"])) {
    // Sanitize inputs using PDO and validate required fields
    $dateFormat = !empty($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : null;
    $time = $_POST["time"] ?? null;
    $paymentMethod = $_POST["mode_of_payment"] ?? null;
    $refNo = $_POST["ref_no"] ?? null; // For Gcash reference number
    $newAddress = !empty($_POST["address"]) ? trim($_POST["address"]) : null;

    if (!$dateFormat || !$time || !$paymentMethod) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        exit;
    }

    // Update address if provided
    if ($newAddress) {
        $sqlUpdateAddress = "UPDATE users SET address = :address WHERE id = :user_id";
        $stmtUpdateAddress = $db->prepare($sqlUpdateAddress);
        $stmtUpdateAddress->execute([':address' => $newAddress, ':user_id' => $user_id]);
    }

    // Validate the selected date and time
    $sqlCheckAvailability = "SELECT * FROM bookingdates WHERE ('$dateFormat' < CURDATE() OR (date = :date AND time = :time))";
    $stmtCheckAvailability = $db->prepare($sqlCheckAvailability);
    $stmtCheckAvailability->execute([':date' => $dateFormat, ':time' => $time]);

    if ($stmtCheckAvailability->rowCount() > 0) {
        echo "<script>alert('The selected date and time are not available.');</script>";
        exit;
    }

    // Get the latest pending order ID for the user
    $stmtPending = $db->prepare("SELECT id FROM pending_orders WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1");
    $stmtPending->execute([':user_id' => $user_id]);
    $pendingOrder = $stmtPending->fetch(PDO::FETCH_ASSOC);

    // Insert booking details
    $sqlInsertBooking = "INSERT INTO customer_detail (date_of_delivery, time_of_delivery, mode_of_payment, reference_number, pending_order_id, user_id) 
                         VALUES (:date_of_delivery, :time_of_delivery, :mode_of_payment, :reference_number, :pending_order_id, :user_id)";
    $stmtInsertBooking = $db->prepare($sqlInsertBooking);

    if ($stmtInsertBooking->execute([
        ':date_of_delivery' => $dateFormat,
        ':time_of_delivery' => $time,
        ':mode_of_payment' => $paymentMethod,
        ':reference_number' => $refNo,
        ':pending_order_id' => $pendingOrder['id'],
        ':user_id' => $user_id
    ])) {
        $bookingID = $db->lastInsertId(); // Get the last inserted booking ID

        // Insert billing information
        $sqlInsertBilling = "INSERT INTO billing (bookingID, status, amount) VALUES (:bookingID, 'Pending', 1)";
        $stmtInsertBilling = $db->prepare($sqlInsertBilling);
        $stmtInsertBilling->execute([':bookingID' => $bookingID]);

        // Redirect to receipt page
        header("Location: receipt.php?bookingID=" . $bookingID);
        exit;
    } else {
        echo "<script>alert('An error occurred while processing your booking. Please try again later.');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="style.css">
    <title>Kuya Card's Catering</title>
  </head>
  <body>
    <header id="header" class="menu-container">
      <div class="logo-box">
        <svg id="header-img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 477.867 477.867" style="enable-background:new 0 0 477.867 477.867;" xml:space="preserve"><g><g>
    </g></g> </svg>
      </div>

      <!--   navbar -->
      <nav id="nav-bar">
        <input class="menu-btn" type="checkbox" id="menu-btn" />
        <label class="menu-icon" for="menu-btn"><span class="nav-icon"></span></label>
        <ul class="menu">
          <li><a href="index.php#">HOME</a></li>
          <li><a href="index.php#about" class="nav-link">ABOUT US</a></li>
          <li><a href="index.php#features" class="nav-link">SERVICES</a></li>
          <li><a href="index.php#pricing" class="nav-link">FAQ'S</a></li>
        </ul>
      </nav>
      <!--   navbar -->
    </header>
    <!-- header ends -->
    
    <main class="container">
      <section class="hero container">
        <h1 class="hero-title-primary"></h1>
        <p class="hero-title-sub"></p>
      </section>
    
    </main>

    <div>
      <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
        <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
        </defs>
        <g class="parallax">
        <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7" />
        <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
        <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
        <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
        </g>
      </svg>
    </div>
    <!--Waves end-->
    <!--Header ends-->
    
    <!--Content starts-->
    <section class="content container">
  <br>
  <form method="post">
    <div class="container text-center">
        <!-- Date -->
        <div class="mb-3">
            <label><strong>Date of Delivery</strong></label>
            <input type="date" class="form-control w-50 mx-auto" name="date" required>
        </div>

        <!-- Time -->
        <div class="mb-3">
            <label><strong>Time of Delivery</strong></label>
            <select name="time" class="form-control w-50 mx-auto" required>
                <option value="">Select a time</option>
                <option value="08:00 AM">08:00 AM</option>
                <option value="09:00 AM">09:00 AM</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="12:00 PM">12:00 PM</option>
                <option value="01:00 PM">01:00 PM</option>
                <option value="02:00 PM">02:00 PM</option>
                <option value="03:00 PM">03:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
                <option value="05:00 PM">05:00 PM</option>
                <option value="06:00 PM">06:00 PM</option>
                <option value="07:00 PM">07:00 PM</option>
            </select>
        </div>

        <!-- Payment Method -->
        <div class="mb-3">
            <label><strong>Mode of Payment</strong></label>
            <select name="mode_of_payment" class="form-control w-50 mx-auto" id="paymentMethod" required>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="Gcash">Gcash</option>
            </select>
        </div>

        <!-- Gcash Reference Number -->
        <div id="gcashDetails" class="mt-4" style="display: none;">
            <div class="mb-3">
                <label><strong>Reference Number</strong></label>
                <input type="text" class="form-control w-50 mx-auto" name="ref_no" placeholder="Enter Gcash reference number">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-4">
            <button class="btn btn-primary" name="next">Next</button>
            <button type="button" class="btn btn-secondary mx-1" onclick="window.location.href='home.php'">Back</button>
        </div>
    </div>
</form>

<script>
    // Show Gcash details based on payment method
    document.getElementById('paymentMethod').addEventListener('change', function () {
        const gcashDetails = document.getElementById('gcashDetails');
        const refNo = document.querySelector('input[name="ref_no"]');

        if (this.value === 'Gcash') {
            gcashDetails.style.display = 'block';
            refNo.setAttribute('required', 'true');
        } else {
            gcashDetails.style.display = 'none';
            refNo.removeAttribute('required');
            refNo.value = '';
        }
    });
</script>




