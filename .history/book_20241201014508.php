<?php
include("php/conn.php");
require_once('tcpdf/tcpdf.php');

if (isset($_POST["next"])) {
    // Remove the name, contact, and address
    $dateFormat = date('Y-m-d', strtotime($_POST['date']));
    $time = mysqli_real_escape_string($conn, $_POST["time"]);
    $refNo = isset($_POST["ref_no"]) ? mysqli_real_escape_string($conn, $_POST["ref_no"]) : null;
    $orderID = isset($_POST["order_id"]) ? mysqli_real_escape_string($conn, $_POST["order_id"]) : null;
    $paymentMethod = mysqli_real_escape_string($conn, $_POST["mode_of_payment"]);

    // Ensure required fields are not empty
    if (empty($dateFormat) || empty($time) || empty($paymentMethod)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        exit;
    }

    // Check if the selected date and time are available
    $sql1 = "SELECT * FROM bookingdates WHERE ('$dateFormat' < CURDATE() OR (date = ? AND time = ?))";
    $stmt = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt, "ss", $dateFormat, $time);
    mysqli_stmt_execute($stmt);
    $searchQuery = mysqli_stmt_get_result($stmt);
    $numRows = mysqli_num_rows($searchQuery);

    if ($numRows > 0) {
        echo "<script>alert('The selected date and time are not available.');</script>";
    } else {
        // Insert booking information without name, contact, and address
        $sqlInsertBooking = "INSERT INTO bookingdates (date, time, refNo, paymentMethod) 
                             VALUES (?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sqlInsertBooking);
        $stmt1->bind_param("ssss", $dateFormat, $time, $refNo, $paymentMethod);

        if ($stmt1->execute()) {
            $bookingID = $stmt1->insert_id;  // Get the last inserted booking ID
            $stmt1->close();

            // Insert into billing table
            $sqlInsertBilling = "INSERT INTO billing (bookingID, status, amount) VALUES (?, 'Pending', '1')";
            $stmt2 = $conn->prepare($sqlInsertBilling);
            $stmt2->bind_param("i", $bookingID);
            $stmt2->execute();
            $stmt2->close();

            // Generate PDF after successful booking
            generatePdf($conn, $bookingID);
        } else {
            echo "<script>alert('An error occurred while processing your booking. Please try again later.');</script>";
        }
    }
}

function generatePdf($conn, $bookingID)
{
    // Retrieve the data for the invoice using the provided bookingID
    $sql = "SELECT b.bookingID, b.date, b.time
            FROM bookingdates b
            WHERE b.bookingID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $date = $result['date'];
    $time = $result['time'];

    $filename = 'BookingNo' . $bookingID . '.pdf';

    // Use TCPDF to generate the PDF
    $pdf = new TCPDF();
    $pdf->AddPage();
    $html = "
        <h1>Booking Confirmation</h1>
        <p><strong>Date:</strong> $date</p>
        <p><strong>Time:</strong> $time</p>
    ";
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output($filename, 'D');

    $stmt->close();
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
      <div class="mb-3">

      <div class="mb-3">
        <!-- Reservation Date -->
        <label><strong>Date of Delivery</strong></label>
        <input type="date" class="form-control w-50 mx-auto" name="date">
      </div>

      <div class="mb-3">
        <!-- Time of Delivery -->
        <label><strong>Time of Delivery</strong></label>
        <select name="time" class="form-control w-50 mx-auto">
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
          <option value="08:00 PM">08:00 PM</option>
          <option value="09:00 PM">09:00 PM</option>
          <option value="10:00 PM">10:00 PM</option>
          <option value="11:00 PM">11:00 PM</option>
        </select>
      </div>

      <div class="mb-3">
        <!-- Mode of Payment -->
        <label><strong>Mode of Payment</strong></label>
        <select name="mode_of_payment" class="form-control w-50 mx-auto" id="paymentMethod" required>
          <option value="Cash on Delivery">Cash on Delivery</option>
          <option value="Gcash">Gcash</option>
        </select>
      </div>

      <!-- Gcash Details Section (Hidden by Default) -->
      <div id="gcashDetails" style="display: none;" class="mt-4">
        <div class="text-center mb-3">
          <img src="pics/GcashPayment.jpg" alt="Gcash QR Code" class="img-fluid" style="max-width: 200px;">
        </div>

        <div class="mb-3">
          <!-- Reference Number -->
          <label><strong>Reference Number</strong></label>
          <input type="text" class="form-control w-50 mx-auto" name="ref_no" placeholder="Enter Gcash reference number">
        </div>

        <div class="mb-3">
          <!-- Order ID -->
          <label><strong>Order ID</strong></label>
          <input type="text" class="form-control w-50 mx-auto" name="order_id" placeholder="Enter your order ID">
        </div>
      </div>

      <!-- Submit Button -->
      <div class="mt-4">
        <button class="btn btn-primary" name="next">Next</button>
        <button type="button" class="btn btn-secondary mx-1" onclick="window.location.href='home.php'">Back</button>
      </div>
    </div>
  </form>
</section>

<script>
  // Toggle Gcash details based on payment method selection
  document.getElementById('paymentMethod').addEventListener('change', function () {
    const gcashDetails = document.getElementById('gcashDetails');
    const refNo = document.querySelector('input[name="ref_no"]');
    const orderID = document.querySelector('input[name="order_id"]');
    if (this.value === 'Gcash') {
      gcashDetails.style.display = 'block';
    } else {
      gcashDetails.style.display = 'none';
      refNo.value = ''; // Clear Gcash fields
      orderID.value = ''; // Clear Gcash fields
    }
  });
</script>

