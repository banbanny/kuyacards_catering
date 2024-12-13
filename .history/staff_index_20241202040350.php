<?php
    include("php/catering_db.php");

    // Query to fetch customer details
    $stmt = $db->prepare("SELECT id, email, full_name, address, contact_number, role FROM users");
    $stmt->execute();
    $customer_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to fetch pending orders
    $stmt = $db->prepare("SELECT id, item_name, quantity, price, total, user_id, created_at, status FROM pending_orders");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle order status update (if the form is submitted)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
        $order_id = $_POST['order_id'];
        $new_status = $_POST['new_status'];

        // Ensure the status is either 'Pending' or 'Paid'
        if (in_array($new_status, ['Pending', 'Paid'])) {
            $updateStmt = $db->prepare("UPDATE pending_orders SET status = :status WHERE id = :order_id");
            $updateStmt->execute([':status' => $new_status, ':order_id' => $order_id]);
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

      .content-page {
        display: none;
      }

      .content-page.active {
        display: block;
      }

      /* Style for table headers */
      table thead th {
        background-color: lightblue !important;
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
        <a href="#package" class="nav-link active" data-page="package">Package Products</a>
        <a href="#customers" class="nav-link" data-page="customers">Customer's Info</a>
        <a href="#orders" class="nav-link" data-page="orders">Orders</a>
        <a href="#logout" class="nav-link text-danger" data-page="logout">Logout</a>
      </nav>
    </div>
    <div class="main-content">
      <!-- Customer Info Section -->
      <div id="customers" class="content-page active">
        <h1 class="text-center mb-4">Customer's Info</h1>
        <div class="container mt-3">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Customer ID</th>
                <th>Email Address</th>
                <th>Full Name</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                // Display customer details in table rows
                foreach ($customer_results as $customer) {
                  echo '<tr>
                          <td>'.$customer['id'].'</td>
                          <td>'.$customer['email'].'</td>
                          <td>'.$customer['full_name'].'</td>
                          <td>'.$customer['address'].'</td>
                          <td>'.$customer['contact_number'].'</td>
                          <td>'.$customer['role'].'</td>
                          <td>
                            <a href="customers.php?delete_id='.$customer['id'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this customer?\')">Delete</a>
                          </td>
                        </tr>';
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Orders Section -->
      <div id="orders" class="content-page">
        <h1 class="text-center mb-4">Orders</h1>
        <div class="container mt-3">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>User ID</th>
                <th>Created At</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                // Display order details in table rows
                foreach ($orders as $order) {
                  echo '<tr>
                          <td>'.$order['id'].'</td>
                          <td>'.$order['item_name'].'</td>
                          <td>'.$order['quantity'].'</td>
                          <td>'.$order['price'].'</td>
                          <td>'.$order['total'].'</td>
                          <td>'.$order['user_id'].'</td>
                          <td>'.$order['created_at'].'</td>
                          <td>'.$order['status'].'</td>
                          <td>
                            <form method="POST" action="">
                              <input type="hidden" name="order_id" value="'.$order['id'].'">
                              <select name="new_status" class="form-select" required>
                                <option value="Pending" '.($order['status'] == 'Pending' ? 'selected' : '').'>Pending</option>
                                <option value="Paid" '.($order['status'] == 'Paid' ? 'selected' : '').'>Paid</option>
                              </select>
                              <button type="submit" class="btn btn-primary btn-sm mt-2">Update Status</button>
                            </form>
                          </td>
                        </tr>';
                }
              ?>
            </tbody>
          </table>
        </div>
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

          // Show the corresponding content page
          const pageId = link.getAttribute('data-page');
          document.querySelectorAll('.content-page').forEach((page) => {
            page.classList.remove('active');
          });
          document.getElementById(pageId).classList.add('active');
        });
      });
    </script>
  </body>
</html>
