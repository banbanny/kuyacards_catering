<?php
    include("php/conn.php");
    

  
    // Query for Pending and Approved Transactions
    $sql = "SELECT b.bookingID, c.name, c.band, s.serviceName, b.date, b.time, b.hours, b.refNumber, bl.status 
            FROM bookingdates as b 
            INNER JOIN client c ON b.clientID = c.clientID 
            INNER JOIN sevices s ON s.servicesID = b.servicesID 
            INNER JOIN billing bl ON bl.bookingID = b.bookingID 
            WHERE bl.status = 'Pending'";
    $query = mysqli_query($conn, $sql);
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);

    $sql1 = "SELECT b.bookingID, c.name, c.band, s.serviceName, b.date, b.time, b.hours, b.refNumber, bl.status, ap.approvedDate 
             FROM bookingdates as b 
             INNER JOIN client c ON b.clientID = c.clientID 
             INNER JOIN sevices s ON s.servicesID = b.servicesID 
             INNER JOIN billing bl ON bl.bookingID = b.bookingID 
             INNER JOIN approveddates ap ON ap.bookingID = b.bookingID 
             WHERE bl.status = 'Approved'";
    $query1 = mysqli_query($conn, $sql1);
    $result1 = mysqli_fetch_all($query1, MYSQLI_ASSOC);

    // Handling Approval and Decline
    if(isset($_POST['approve'])) {
        $bookingID = $_POST['bookingID'];
        $sql = "UPDATE billing SET status = 'Approved' WHERE bookingID = $bookingID AND status = 'Pending'";
        $conn->query($sql);
        header("Location: staff_index.php");
    }

    if(isset($_POST['decline'])) {
        $bookingID = $_POST['bookingID'];
        $sql = "UPDATE billing SET status = 'Decline' WHERE bookingID = $bookingID AND status = 'Pending'";
        $conn->query($sql);
        header("Location: staff_index.php");
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
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="style.css" />
    <title>Kuya Card's Catering</title>
  </head>
  <body>
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="profile-section text-center">
        <div class="profile-image">
          <img
            src="https://via.placeholder.com/80"
            alt="Profile"
            class="rounded-circle"
          />
        </div>
        <h5 class="text-white mt-2">Admin</h5>
      </div>
      <nav class="nav flex-column sidebar-nav mt-3">
        <a href="#" class="nav-link active">Lechon Products</a>
        <a href="#" class="nav-link">Viand Products</a>
        <a href="#" class="nav-link">Staff</a>
        <a href="#" class="nav-link">Customer's Info</a>
        <a href="#" class="nav-link">Orders</a>
        <a href="#" class="nav-link text-danger">Logout</a>
      </nav>
    </div>

    
  </body>
</html>
