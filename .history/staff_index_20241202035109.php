<?php
    include("php/catering_db.php");

    // Query to fetch customer details
    $stmt = $db->prepare("SELECT id, email, full_name, address, contact_number, role FROM users");
    $stmt->execute();
    $customer_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Fetch all customer data
      $customer_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query to fetch pending orders
    $stmt = $db->prepare("SELECT id, item_name, quantity, price, total, user_id, created_at, status FROM pending_orders");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
      /* Sidebar and main content styling omitted for brevity... */
    </style>
  </head>
  <body>
    <div class="sidebar">
      <!-- Sidebar content omitted for brevity... -->
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
                            <!-- Form to update the order status -->
                            <form method="POST" action="update_status.php" class="update-status-form">
                              <input type="hidden" name="order_id" value="'.$order['id'].'">
                              <select name="new_status">
                                <option value="Pending" '.($order['status'] == 'Pending' ? 'selected' : '').'>Pending</option>
                                <option value="Paid" '.($order['status'] == 'Paid' ? 'selected' : '').'>Paid</option>
                              </select>
                              <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
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

    <!-- Edit Modal omitted for brevity... -->

    <script>
      // Optional AJAX handling for status update form submission
      document.querySelectorAll('.update-status-form').forEach(form => {
          form.addEventListener('submit', function(event) {
              event.preventDefault();
              
              const formData = new FormData(form);
              
              fetch('update_status.php', {
                  method: 'POST',
                  body: formData
              })
              .then(response => response.text())
              .then(result => {
                  alert(result); // Show a message with the result
                  // Optionally, update the UI to reflect the new status
              })
              .catch(error => {
                  console.error('Error:', error);
              });
          });
      });
    </script>
  </body>
</html>