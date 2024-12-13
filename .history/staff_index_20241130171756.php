<?php
    include("php/conn.php");
    include("");

  
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
    <link rel="stylesheet" href="style.css">
    <title>Kuya Card's Catering</title>
  </head>
  <body>
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="logo-box">
        <!-- Your logo can go here -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.867 477.867" id="header-img">
          <!-- Insert logo or SVG here -->
        </svg>
      </div>
      <div class="sidebar-buttons">
        <button class="btn btn-primary">Home</button>
        <button class="btn btn-primary">Products</button>
        <button class="btn btn-primary">Customer's Info</button>
        <button class="btn btn-primary">Orders</button>
        <button class="btn btn-primary">Transactions</button>
        <button class="btn btn-primary">Services</button>
        <button class="btn btn-danger">Sign Out</button>
      </div>
    </div>

    <!-- Content Section -->
    <div class="content-section">
      <header>
        <h1 class="hero-title-primary">Kuya Card's Catering</h1>
        <p class="hero-title-sub">Home of Delicacies</p>
      </header>

      <!-- Pending Transactions -->
      <section class="content">
        <h4>Pending Transactions</h4>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th scope="col">Booking ID</th>
              <th scope="col">Name</th>
              <th scope="col">Service</th>
              <th scope="col">Date Booked</th>
              <th scope="col">Time</th>
              <th scope="col">Hours</th>
              <th scope="col">Reference Number</th>
              <th scope="col">Status</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($result as $booking) { ?>
              <tr>
                <th scope="row"><?php echo $booking['bookingID']?></th>
                <td><?php echo $booking['name']?></td>
                <td><?php echo $booking['serviceName']?></td>
                <td><?php echo $booking['date']?></td>
                <td><?php echo $booking['time']?></td>
                <td><?php echo $booking['hours']?></td>
                <td><?php echo $booking['refNumber']?></td>
                <td><?php echo $booking['status']?></td>
                <td>
                  <form method="post">
                    <input type="hidden" name="bookingID" value="<?php echo $booking['bookingID']?>">
                    <button type="submit" class="btn btn-success" name="approve">Approve</button>
                    <button type="submit" class="btn btn-danger" name="decline">Decline</button>
                  </form>    
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </section>
      
      <!-- Approved Transactions -->
      <section class="content">
        <h4>Approved Transactions</h4>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th scope="col">Booking ID</th>
              <th scope="col">Name</th>
              <th scope="col">Service</th>
              <th scope="col">Date Booked</th>
              <th scope="col">Time</th>
              <th scope="col">Hours</th>
              <!-- Continue for the Approved table columns... -->
            </tr>
          </thead>
          <tbody>
            <!-- Approved transactions data goes here -->
          </tbody>
        </table>
      </section>
    </div>

  </body>
</html>
