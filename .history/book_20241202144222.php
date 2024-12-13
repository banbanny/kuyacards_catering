<?php
session_start();
ob_start(); // Start output buffering

$user_id = $_SESSION['user_id'];

include("php/catering_db.php");

if (isset($_POST["next"])) {
    // Sanitize and format inputs using PDO
    $dateFormat = date('Y-m-d', strtotime($_POST['date']));
    $time = $_POST["time"];
    $refNo = isset($_POST["ref_no"]) ? $_POST["ref_no"] : null; // Get Gcash reference number
    $paymentMethod = $_POST["mode_of_payment"];
    $newAddress = isset($_POST["address"]) ? trim($_POST["address"]) : null;

    // Ensure required fields are not empty
    if (empty($dateFormat) || empty($time) || empty($paymentMethod)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        exit;
    }

     // Update address if provided
     if (!empty($newAddress)) {
      $sqlUpdateAddress = "UPDATE users SET address = :address WHERE id = :user_id";
      $stmtUpdateAddress = $db->prepare($sqlUpdateAddress);
      $stmtUpdateAddress->bindParam(':address', $newAddress);
      $stmtUpdateAddress->bindParam(':user_id', $user_id);

      if (!$stmtUpdateAddress->execute()) {
          echo "<script>alert('Failed to update the address. Please try again.');</script>";
          exit;
      }
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
      $stmtPending = $db->prepare("SELECT id FROM pending_orders WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1");
      $stmtPending->execute([':user_id' => $user_id]);
      $pendingID = $stmtPending->fetch(PDO::FETCH_ASSOC);
        // Insert booking information into customer_detail using PDO
        $sqlInsertBooking = "INSERT INTO customer_detail (date_of_delivery, time_of_delivery, mode_of_payment, reference_number, pending_order_id, user_id) 
                             VALUES (:date_of_delivery, :time_of_delivery, :mode_of_payment, :reference_number, :pending_order_id, :user_id)";
        $stmt1 = $db->prepare($sqlInsertBooking);
        $stmt1->bindParam(':date_of_delivery', $dateFormat);
        $stmt1->bindParam(':time_of_delivery', $time);
        $stmt1->bindParam(':mode_of_payment', $paymentMethod);
        $stmt1->bindParam(':reference_number', $refNo);  // Add reference number for Gcash
        $stmt1->bindParam(':pending_order_id', $pendingID['id']);
        $stmt1->bindParam(':user_id', $user_id);

        if ($stmt1->execute()) {
            $bookingID = $db->lastInsertId();  // Get the last inserted booking ID

            // Insert into billing table
            $sqlInsertBilling = "INSERT INTO billing (bookingID, status, amount) VALUES (:bookingID, 'Pending', '1')";
            $stmt2 = $db->prepare($sqlInsertBilling);
            $stmt2->bindParam(':bookingID', $bookingID);
            $stmt2->execute();

            // Redirect to receipt page after successful booking
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
    <link rel="icon" type="image/png" href="pics/logo.png">
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
    
    // Show or hide Gcash reference number input based on payment method
    if (this.value === 'Gcash') {
      gcashDetails.style.display = 'block';
      refNo.setAttribute('required', 'true'); // Make reference number required
    } else {
      gcashDetails.style.display = 'none';
      refNo.removeAttribute('required'); // Remove requirement for reference number
      refNo.value = ''; // Clear the reference number field
    }
  });

  // Form validation before submission
  document.querySelector('form').addEventListener('submit', function(event) {
    const paymentMethod = document.getElementById('paymentMethod').value;
    const refNo = document.querySelector('input[name="ref_no"]');
    
    // If Gcash is selected and reference number is not provided, prevent form submission
    if (paymentMethod === 'Gcash' && !refNo.value) {
      alert('Please provide the Gcash reference number.');
      event.preventDefault(); // Prevent form submission
    }
  });
</script>

heres the receipt.php code 
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
    <link rel="icon" type="image/png" href="pics/logo.png">
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
    <a href="home.php">Back to</a>
</div>

<div class="footer">
    <p>&copy; <?php echo date('Y'); ?> Catering Services. All rights reserved.</p>
</div>

</body>
</html>