<?php
ob_start(); // Start output buffering

include("php/catering_db.php");

if (isset($_POST["next"])) {
    // Sanitize and format inputs using PDO
    $name = $_POST["name"];
    $address = $_POST["address"];
    $dateFormat = date('Y-m-d', strtotime($_POST['date']));
    $time = $_POST["time"];
    $refNo = isset($_POST["ref_no"]) ? $_POST["ref_no"] : null;
    $paymentMethod = $_POST["mode_of_payment"];

    // Ensure required fields are not empty
    if (empty($name) || empty($address) || empty($dateFormat) || empty($time) || empty($paymentMethod)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        exit;
    }

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
        $sqlInsertBooking = "INSERT INTO customer_detail (name, address, date_of_delivery, time_of_delivery, mode_of_payment, reference_number) 
                             VALUES (:name, :address, :date_of_delivery, :time_of_delivery, :mode_of_payment, :reference_number)";
        $stmt1 = $db->prepare($sqlInsertBooking);
        $stmt1->bindParam(':name', $name);
        $stmt1->bindParam(':address', $address);
        $stmt1->bindParam(':date_of_delivery', $dateFormat);
        $stmt1->bindParam(':time_of_delivery', $time);
        $stmt1->bindParam(':mode_of_payment', $paymentMethod);
        $stmt1->bindParam(':reference_number', $refNo);

        if ($stmt1->execute()) {
            // Booking successful, redirect to receipt page with booking ID
            $bookingID = $db->lastInsertId();
            header("Location: receipt.php?bookingID=$bookingID");
            exit; // Ensure that no further code is executed after redirection
        } else {
            echo "<script>alert('An error occurred while processing your booking. Please try again later.');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css">
    <title>Kuya Card's Catering</title>
  </head>
  <body>
    <header id="header" class="menu-container">
      <div class="logo-box">
        <svg id="header-img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 477.867 477.867" style="enable-background:new 0 0 477.867 477.867;" xml:space="preserve"><g><g></g></g> </svg>
      </div>

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
    </header>
    
    <main class="container">
      <section class="hero container">
        <h1 class="hero-title-primary"></h1>
        <p class="hero-title-sub"></p>
      </section>
    </main>

    <section class="content container">
      <br>
      <form method="post">
        <div class="container text-center">
          <div class="mb-3">
            <label><strong>Name</strong></label>
            <input type="text" class="form-control w-50 mx-auto" name="name" required>
          </div>

          <div class="mb-3">
            <label><strong>Address</strong></label>
            <input type="text" class="form-control w-50 mx-auto" name="address" required>
          </div>

          <div class="mb-3">
            <label><strong>Date of Delivery</strong></label>
            <input type="date" class="form-control w-50 mx-auto" name="date" required>
          </div>

          <div class="mb-3">
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
              <label><strong>Reference Number</strong></label>
              <input type="text" class="form-control w-50 mx-auto" name="ref_no" placeholder="Enter Gcash reference number">
            </div>
          </div>

          <div class="mt-4">
            <button class="btn btn-primary" name="next">Next</button>
          </div>
        </div>
      </form>
    </section>

    <script>
      // Toggle Gcash details based on payment method selection
      document.getElementById('paymentMethod').addEventListener('change', function () {
        const gcashDetails = document.getElementById('gcashDetails');
        const refNo = document.querySelector('input[name="ref_no"]');
        if (this.value === 'Gcash') {
          gcashDetails.style.display = 'block';
        } else {
          gcashDetails.style.display = 'none';
          refNo.value = ''; // Clear Gcash fields
        }
      });
    </script>
  </body>
</html>
