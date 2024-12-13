<?php
include("php/conn.php");
require_once('tcpdf/tcpdf.php');

if (isset($_POST["next"])) {
    $name = $_POST["name"];
    $contact = $_POST["contact"];
    $address = $_POST["address"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $refNo = $_POST["refNo"];

    $dateFormat = date('Y-m-d', strtotime($_POST['date']));
    $dateFormat = mysqli_real_escape_string($conn, $dateFormat);
    $time = mysqli_real_escape_string($conn, $time);

    // Check if the date and time are available
    $sql1 = "SELECT * FROM bookingdates WHERE ('$dateFormat' < CURDATE() OR (date = ? AND time = ?))";
    $stmt = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt, "ss", $dateFormat, $time);
    mysqli_stmt_execute($stmt);
    $searchQuery = mysqli_stmt_get_result($stmt);
    $numRows = mysqli_num_rows($searchQuery);

    if ($numRows > 0) {
        echo "<script>alert('Time is not available!')</script>";
    } else {
        // Insert booking information
        $sqlInsertBooking = "INSERT INTO bookingdates (name, contact, address, date, time, refNo) 
                             VALUES (?, ?, ?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sqlInsertBooking);
        $stmt1->bind_param("ssssss", $name, $contact, $address, $dateFormat, $time, $refNo);

        if ($stmt1->execute()) {
            $bookingID = $stmt1->insert_id;  // Get the last inserted booking ID
            $stmt1->close();

            // Insert into billing table
            $sqlInsertBilling = "INSERT INTO billing (bookingID, status, amount) VALUES (?, 'Pending', '1')";
            $stmt2 = $conn->prepare($sqlInsertBilling);
            $stmt2->bind_param("i", $bookingID);
            $stmt2->execute();
            $stmt2->close();

            // Check for conflicts with approved dates
            $check1 = "SELECT date, time FROM bookingdates WHERE bookingID = $bookingID";
            $rcheck1 = $conn->query($check1);
            $c1 = $rcheck1->fetch_assoc();
            $date1 = $c1['date'] . ' ' . $c1['time'];

            $check2 = "SELECT bd.date, bd.time FROM approveddates ap, bookingdates bd WHERE ap.bookingID = bd.bookingID";
            $rcheck2 = $conn->query($check2);
            $c2 = $rcheck2->fetch_all(MYSQLI_ASSOC);

            foreach ($c2 as $row) {
                $date2 = $row['date'] . ' ' . $row['time'];
                if ($date1 == $date2) {
                    $update = "UPDATE billing SET status = 'Decline' WHERE bookingID = $bookingID";
                    $runUpdate = $conn->query($update);
                    if ($runUpdate) {
                        echo "<script>alert('UNAVAILABLE! Date and Time are already reserved!')</script>";
                        break;
                    }
                }
            }

            if (!isset($date2) || $date1 != $date2) {
                generatePdf($conn, $bookingID);
            }
        }
    }
}

function generatePdf($conn, $bookingID)
{
    // Retrieve the data for the invoice using the provided bookingID
    $sql = "SELECT b.bookingID, c.clientID, c.name, s.serviceName, b.date, b.time, b.hours
            FROM bookingdates b
            INNER JOIN client c ON b.clientID = c.clientID
            INNER JOIN services s ON b.servicesID = s.servicesID
            WHERE b.bookingID = $bookingID";

    $sqlQuery = mysqli_query($conn, $sql);
    $result = mysqli_fetch_assoc($sqlQuery);

    $clientID = $result['clientID'];
    $clientName = $result['name'];
    $services = $result['serviceName'];
    $date = $result['date'];
    $time = $result['time'];
    $hours = $result['hours'];

    $filename = 'BookingNo' . $bookingID . '.pdf';

    if (file_exists($filename)) {
        unlink($filename);
    }

    // Use TCPDF to generate the PDF
    require_once('tcpdf/tcpdf.php');
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->writeHTML("Billing Information");
    $pdf->writeHTML("-------------------\n");
    $pdf->writeHTML("Client ID: $clientID\n");
    $pdf->writeHTML("Client Name: $clientName\n");
    $pdf->writeHTML("Services: $services\n");
    $pdf->writeHTML("Date: $date\n");
    $pdf->writeHTML("Time: $time\n");
    $pdf->writeHTML("Hours: $hours");
    $pdf->Output($filename, 'D');

    return $filename; // Return the filename for further use if needed
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
        <!-- Full Name -->
        <label><strong>Full Name</strong></label>
        <input type="text" class="form-control w-50 mx-auto" onkeypress="return ValidateAlpha(event)" name="name" required>
      </div>

      <div class="mb-3">
        <!-- Contact Number -->
        <label><strong>Contact Number</strong></label>
        <input type="text" class="form-control w-50 mx-auto" onkeypress="return onlyNumberKey(event)" maxlength="11" name="contact" required>
      </div>

      <div class="mb-3">
        <!-- Address -->
        <label><strong>Address</strong></label>
        <input type="text" class="form-control w-50 mx-auto" name="address" required>
      </div>

      <div class="mb-3">
        <!-- Reservation Date -->
        <label><strong>Date of de</strong></label>
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
          <option value="Gcash">Gcash</option>
          <option value="Cash on Delivery">Cash on Delivery</option>
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
  // JavaScript to toggle Gcash QR Code and additional fields
  document.getElementById('paymentMethod').addEventListener('change', function () {
    const gcashDetails = document.getElementById('gcashDetails');
    if (this.value === 'Gcash') {
      gcashDetails.style.display = 'block'; // Show Gcash section
    } else {
      gcashDetails.style.display = 'none'; // Hide Gcash section
    }
  });
</script>
