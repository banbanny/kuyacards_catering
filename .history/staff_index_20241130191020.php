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
    <style>
      body {
        display: flex;
        height: 100vh;
        margin: 0;
        overflow: hidden;
        background-color: #f4f4f4;
      }

      .sidebar {
        width: 250px;
        background-color: #343a40;
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding-top: 20px;
        position: fixed;
        height: 100%;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
      }

      .sidebar:hover {
        box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
      }

      .profile-image img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-left: 20px;
      }

      .sidebar-nav .nav-link {
        color: #adb5bd;
        padding: 10px 20px;
        font-size: 16px;
        text-align: left;
        width: 100%;
        border-radius: 5px;
        transition: all 0.2s ease;
      }

      .sidebar-nav .nav-link.active,
      .sidebar-nav .nav-link:hover {
        background-color: #495057;
        color: #fff;
      }

      .main-content {
        margin-left: 250px;
        padding: 20px;
        flex: 1;
        overflow-y: auto;
      }
    </style>
  </head>
  <body>
    <div class="sidebar">
      <div class="profile-section text-start">
        <div class="profile-image">
          <img
            src="https://via.placeholder.com/80"
            alt="Profile"
            class="rounded-circle"
          />
        </div>
        <h5 class="text-white mt-2 ms-3">Admin</h5>
      </div>
      <nav class="nav flex-column sidebar-nav mt-3">
        <a href="#lechon" class="nav-link active">Packages Products</a>
        <a href="#viand" class="nav-link">V</a>
        <a href="#staff" class="nav-link">Staff</a>
        <a href="#customers" class="nav-link">Customer's Info</a>
        <a href="#orders" class="nav-link">Orders</a>
        <a href="#logout" class="nav-link text-danger">Logout</a>
      </nav>
    </div>
    <div class="main-content">
      <div id="content">
        <h1>Welcome to Kuya Card's Catering Dashboard</h1>
        <p>Select an option from the sidebar to view content.</p>
      </div>
    </div>

    <script>
      document.querySelectorAll('.sidebar-nav .nav-link').forEach((link) => {
        link.addEventListener('click', (e) => {
          e.preventDefault();

          // Remove active class from all links
          document
            .querySelectorAll('.sidebar-nav .nav-link')
            .forEach((navLink) => navLink.classList.remove('active'));

          // Add active class to the clicked link
          link.classList.add('active');

          // Update main content dynamically
          const content = document.getElementById('content');
          content.innerHTML = `<h1>${link.textContent}</h1><p>Content for ${link.textContent} will be displayed here.</p>`;
        });
      });
    </script>
  </body>
</html>