
<?php
    include("php/catering_db.php");

    // Prepare and execute the query to fetch customer details
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    
    // Fetch all customer data
    $customer_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt1 = $db->prepare("SELECT * FROM packages");
    $stmt1->execute();
    $package_results = $stmt1->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $db->prepare("SELECT * FROM customer_detail cd INNER JOIN pending_orders po ON po.id = cd.pending_order_id INNER JOIN users u ON u.id = cd.user_id");
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
    <!-- Bootstrap CSS -->

<!-- Bootstrap JS (For Modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

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
      <div id="package" class="content-page active">
        <h1 class="text-center mb-4">Package Products</h1>
        <div class="container">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>PACKAGE TYPE</th>
                <th>DESCRIPTION</th>
                <th>PRICE</th>
                <th>ITEMS</th>
                <th>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
            <?php 
          // Iterate through the customer results and display in table rows
          foreach ($package_results as $package) {
            echo '<tr>
                    <td>'.$package['id']. '</td>
                    <td>' .$package['package_name']. '</td>
                    <td>' .$package['description']. '</td>
                    <td>' .$package['price']. '</td>
                    <td>' .$package['items']. '</td>
                    <td>
                    <a href="update_package.php?update_id='.$package['id'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to update this package?\')">Update</a>
                    <a href="delete_package.php?delete_id='.$package['id'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this package?\')">Delete</a>
                  </td>
                  </tr>';
          }
        ?>
            </tbody>
          </table>
        </div>
      </div>
<div id="customers" class="content-page">
  <div class="container mt-3">
    <h1 class="text-center mb-4">Customer's Info</h1>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>CUSTOMER ID</th>
          <th>EMAIL ADDRESS</th>
          <th>FULL NAME</th>
          <th>ADDRESS</th>
          <th>CONTACT NUMBER</th>
          <th>ROLE</th>
          <th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          // Iterate through the customer results and display in table rows
          foreach ($customer_results as $customer) {
            echo '<tr>
                    <td>'.$customer['id']. '</td>
                    <td>' .$customer['email']. '</td>
                    <td>' .$customer['full_name']. '</td>
                    <td>' .$customer['address']. '</td>
                    <td>' .$customer['contact_number']. '</td>
                    <td>' .$customer['role'] . '</td>
                    <td>
                    <a href="delete_customer.php?delete_id='.$customer['id'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this customer?\')">Delete</a>
                  </td>
                  </tr>';
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

      <div id="orders" class="content-page">
  <h1 class="text-center mb-4">Orders</h1>

  <!-- Staff Table -->
  <div class="container mt-3">
  <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>ORDER ID</th>
                <th>CUSTOMER ID</th>
                <th>TOTAL AMOUNT</th>
                <th>DATE</th>
                <th>TIME</th>
                <th>STATUS</th>
                <th>PAYMENT METHOD</th>
                <th>DELIVERY ADDRESS</h>
              </tr>
            </thead>
            <tbody>
              <?php foreach($orders as $order) {?>
              <tr>
                  <td>1</td>
                  <td>Crissalyn Casuyon</td>
                  <td>Admin</td>
                  <td>099284724329</td>
                </tr>
                <?php } ?>
      </tbody>
    </table>
  </div>

      <div id="logout" class="content-page">
        <h1>Logout</h1>
        <p>You have successfully logged out.</p>
      </div>
    </div>

<!-- Modal Structure -->
<div class="modal fade" id="updateModal<?php echo $package['id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $package['id']; ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateModalLabel<?php echo $package['id']; ?>">Update Package</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Update Form -->
        <form action="update_package.php" method="POST">
          <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">

          <!-- Package Name -->
          <div class="mb-3">
            <label for="package_name" class="form-label">Package Name</label>
            <input type="text" class="form-control" id="package_name" name="package_name" value="<?php echo $package['name']; ?>" required>
          </div>

          <!-- Package Description -->
          <div class="mb-3">
            <label for="package_description" class="form-label">Package Description</label>
            <textarea class="form-control" id="package_description" name="package_description" rows="3" required><?php echo $package['description']; ?></textarea>
          </div>

          <!-- Package Price -->
          <div class="mb-3">
            <label for="package_price" class="form-label">Package Price</label>
            <input type="number" class="form-control" id="package_price" name="package_price" value="<?php echo $package['price']; ?>" required>
          </div>

          <button type="submit" class="btn btn-primary">Update Package</button>
        </form>
      </div>
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
