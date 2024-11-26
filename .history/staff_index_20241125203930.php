<?php
    include("php/conn.php");
    $user_data = check_login($conn);

    $sql = "SELECT b.bookingID, c.name, c.band, s.serviceName, b.date, b.time, b.hours, b.refNumber, bl.status FROM bookingdates as b INNER JOIN client c ON b.clientID = c.clientID INNER JOIN sevices s ON s.servicesID = b.servicesID INNER JOIN billing bl ON bl.bookingID = b.bookingID WHERE bl.status = 'Pending'";
    $query = mysqli_query($conn, $sql);
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);

    $sql1 = "SELECT b.bookingID, c.name, c.band, s.serviceName, b.date, b.time, b.hours, b.refNumber, bl.status, ap.approvedDate FROM bookingdates as b INNER JOIN client c ON b.clientID = c.clientID INNER JOIN sevices s ON s.servicesID = b.servicesID INNER JOIN billing bl ON bl.bookingID = b.bookingID INNER JOIN approveddates ap ON ap.bookingID = b.bookingID WHERE bl.status = 'Approved'";
    $query1 = mysqli_query($conn, $sql1);
    $result1 = mysqli_fetch_all($query1, MYSQLI_ASSOC);

    $sql2 = "SELECT b.bookingID, c.name, c.band, s.serviceName, b.date, b.time, b.hours, b.refNumber, bl.status, dp.declinedDate FROM bookingdates as b INNER JOIN client c ON b.clientID = c.clientID INNER JOIN sevices s ON s.servicesID = b.servicesID INNER JOIN billing bl ON bl.bookingID = b.bookingID INNER JOIN declineddates dp ON dp.bookingID = b.bookingID WHERE bl.status = 'Decline'";
    $query2 = mysqli_query($conn, $sql2);
    $result2 = mysqli_fetch_all($query2, MYSQLI_ASSOC);

    $sql3 = "SELECT * FROM sevices";
    $query3 = mysqli_query($conn, $sql3);
    
    $result3 = mysqli_fetch_all($query3, MYSQLI_ASSOC);

    if(isset($_POST['approve'])) {
        $bookingID = $_POST['bookingID'];
        $sql = "UPDATE billing SET status = 'Approved' WHERE bookingID = $bookingID AND status = 'Pending'";
        $insertSQL = $conn->query($sql);
        header("Location: staff_index.php");
    }

    if(isset($_POST['decline'])) {
        $bookingID = $_POST['bookingID'];
        $sql = "UPDATE billing SET status = 'Decline' WHERE bookingID = $bookingID AND status = 'Pending'";
        $insertSQL = $conn->query($sql);
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
          <li><a href="#">HOME</a></li>
          <li><a href="#about" class="nav-link">TRANSACTIONS</a></li>
          <li><a href="#features" class="nav-link">SERVICES</a></li>
          <li><a href="signout.php" class="nav-link">Sign Out</a></li>
        </ul>
      </nav>
      <!--   navbar -->
    </header>
    <!-- header ends -->
    
    <main class="container">
      <section class="hero container">
        <h1 class="hero-title-primary">Kuya Card's Catering</h1>
        <p class="hero-title-sub">Home of Delicacies</p>
    
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
    <section class="content">
      <br><br>
        <!-- client modal -->
          <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">Transaction Record</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="text-center">

                  </div>
                </div>
                <div class="modal-footer">
                </div>
              </div>
            </div>
          </div>
				<!--  -->
        <h4>Pending Transactions</h4>
      <div id="about">
        <div>
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
            <?php foreach($result as $booking) { ?>
                
                <tbody>
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

            <?php }?>
                </table>
        </div>
      </div>

      <br><br>
        <h4>Approved Transactions</h4>
        <div>
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
                    <th scope="col">Date Approved</th>
                    <th scope="col">Status</th>
                    </tr>
                </thead>
            <?php foreach($result1 as $booking1) { ?>
                
                <tbody>
                    <tr>
                    <th scope="row"><?php echo $booking1['bookingID']?></th>
                    <td><?php echo $booking1['name']?></td>
                    <td><?php echo $booking1['serviceName']?></td>
                    <td><?php echo $booking1['date']?></td>
                    <td><?php echo $booking1['time']?></td>
                    <td><?php echo $booking1['hours']?></td>
                    <td><?php echo $booking1['refNumber']?></td>
                    <td><?php echo $booking1['approvedDate']?></td>
                    <td><?php echo $booking1['status']?></td>
                    </tr>

            <?php }?>
                </table>
        </div>

        <br><br>
        <h4>Decline Transactions</h4>
        <div>
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
                    <th scope="col">Date Declined</th>
                    <th scope="col">Status</th>
                    </tr>
                </thead>
            <?php foreach($result2 as $booking2) { ?>
                
                <tbody>
                    <tr>
                    <th scope="row"><?php echo $booking2['bookingID']?></th>
                    <td><?php echo $booking2['name']?></td>
                    <td><?php echo $booking2['band']?></td>
                    <td><?php echo $booking2['serviceName']?></td>
                    <td><?php echo $booking2['date']?></td>
                    <td><?php echo $booking2['time']?></td>
                    <td><?php echo $booking2['hours']?></td>
                    <td><?php echo $booking2['refNumber']?></td>
                    <td><?php echo $booking2['declinedDate']?></td>
                    <td><?php echo $booking2['status']?></td>
                    </tr>

            <?php }?>
                </table>
        </div>

    
      <div id="features">
      <br><br>
        <h4>List of Services</h4>
        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                    <th scope="col">Booking ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
            <?php foreach($result3 as $service) { ?>
                
                <tbody>
                    <tr>
                    <th scope="row"><?php echo $service['servicesID']?></th>
                    <td><input class="form-control border-none" value="<?php echo $service['serviceName']?>"></td>
                    <td><input class="form-control border-none" value="<?php echo $service['serviceRate']?>"></td>
                    <td>
                    <form method="post">
                        <input type="hidden" name="servicesID" value="<?php echo $service['servicesID']?>">
                        <button type="submit" class="btn btn-success" name="save">Save</button>
                        <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                    </form>    
                    </td>
                    </tr>

            <?php }?>
                </table>
        </div>
      </div>
      <br><br>
    
    </section>
    
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
      integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
      integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V"
      crossorigin="anonymous"
    ></script>
    
  </body>
</html>
