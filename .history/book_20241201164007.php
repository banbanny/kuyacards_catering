<?php
ob_start(); // Start output buffering

include("php/catering_db.php");

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

            // Redirect to receipt page after successful booking
            header("Location: receipt.php?bookingID=" . $bookingID);
            exit;
        } else {
            echo "<script>alert('An error occurred while processing your booking. Please try again later.');</script>";
        }
    }
}
?>


