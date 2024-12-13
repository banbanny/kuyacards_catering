<?php
include("php/conn.php");
require_once('tcpdf/tcpdf.php');

if (isset($_POST["next"])) {
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
        $sqlInsertBooking = "INSERT INTO bookingdates (date, time, refNo) VALUES (?, ?, ?)";
        $stmt1 = $conn->prepare($sqlInsertBooking);
        $stmt1->bind_param("sss", $dateFormat, $time, $refNo);

        if ($stmt1->execute()) {
            $bookingID = $stmt1->insert_id; // Get the last inserted booking ID
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
    $sql = "SELECT b.bookingID, b.date, b.time FROM bookingdates b WHERE b.bookingID = $bookingID";
    $sqlQuery = mysqli_query($conn, $sql);
    $result = mysqli_fetch_assoc($sqlQuery);

    $date = $result['date'];
    $time = $result['time'];

    $filename = 'BookingNo' . $bookingID . '.pdf';

    if (file_exists($filename)) {
        unlink($filename);
    }

    require_once('tcpdf/tcpdf.php');
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->writeHTML("Booking Details");
    $pdf->writeHTML("-------------------\n");
    $pdf->writeHTML("Booking ID: $bookingID\n");
    $pdf->writeHTML("Date: $date\n");
    $pdf->writeHTML("Time: $time\n");
    $pdf->Output($filename, 'D');

    return $filename;
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
    <main class="container">
      <section class="content container">
        <form method="post">
          <div class="container text-center">

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
                <input type="text" class="form-control w-50 mx-auto" name="refNo" placeholder="Enter Gcash reference number">
              </div>
            </div>

            <div class="mt-4">
              <button class="btn btn-primary" name="next">Next</button>
              <button type="button" class="btn btn-secondary mx-1" onclick="window.location.href='home.php'">Back</button>
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
