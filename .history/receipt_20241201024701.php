<?php
include("php/catering_db.php");

if (isset($_GET['bookingID'])) {
    $bookingID = $_GET['bookingID'];

    // Fetch the booking details
    $sql = "SELECT * FROM customer_detail WHERE id = :bookingID";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':bookingID', $bookingID);
    $stmt->execute();

    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <title>Booking Receipt</title>
  </head>
  <body>
    <div class="container mt-5">
      <h1 class="text-center">Booking Receipt</h1>
      <div class="mt-4">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['name']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($booking['address']); ?></p>
        <p><strong>Date of Delivery:</strong> <?php echo htmlspecialchars($booking['date_of_delivery']); ?></p>
        <p><strong>Time of Delivery:</strong> <?php echo htmlspecialchars($booking['time_of_delivery']); ?></p>
        <p><strong>Mode of Payment:</strong> <?php echo htmlspecialchars($booking['mode_of_payment']); ?></p>
        <?php if ($booking['mode_of_payment'] == 'Gcash'): ?>
          <p><strong>Reference Number:</strong> <?php echo htmlspecialchars($booking['reference_number']); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </body>
</html>
