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
    <title>Kuya Card's Catering</title>
</head>
<body>
    <header id="header" class="menu-container">
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
    
    <section class="content container">
        <form method="post">
            <div class="container text-center">
                <div class="mb-3">
                    <label><strong>Full Name</strong></label>
                    <input type="text" class="form-control w-50 mx-auto" onkeypress="return ValidateAlpha(event)" name="name" required>
                </div>
                <div class="mb-3">
                    <label><strong>Contact Number</strong></label>
                    <input type="text" class="form-control w-50 mx-auto" onkeypress="return onlyNumberKey(event)" maxlength="11" name="contact" required>
                </div>
                <div class="mb-3">
                    <label><strong>Address</strong></label>
                    <input type="text" class="form-control w-50 mx-auto" name="address" required>
                </div>
                <div class="mb-3">
                    <label><strong>Reservation Date</strong></label>
                    <input type="date" class="form-control w-50 mx-auto" name="date">
                </div>
                <div class="mb-3">
                    <label><strong>Time of Delivery</strong></label>
                    <input type="time" class="form-control w-50 mx-auto" name="time" required>
                </div>
                <div class="mb-3">
                    <label><strong>Reference Number</strong></label>
                    <input type="text" class="form-control w-50 mx-auto" name="refNo" required>
                </div>

                <div class="container d-flex justify-content-center mt-4">
                    <button type="submit" name="next" class="btn btn-primary mx-1">Submit</button>
                    <!-- Back Button -->
                    <button type="button" class="btn btn-secondary mx-1" onclick="window.location.href='home.php'">Back</button>
                </div>
            </div>
        </form>
    </section>
</body>
</html>
