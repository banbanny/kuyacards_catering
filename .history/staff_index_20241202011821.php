<?php
include("php/catering_db.php");

// Query the database to fetch staff members
$stmt = $db->prepare("SELECT * FROM users WHERE role = 'admin'"); 
$stmt->execute();
$staff_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css" />
    <title>Kuya Card's Catering</title>
    <style>
        /* Sidebar and main content styles are retained from your design */
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

        table thead th {
            background-color: lightblue !important;
        }

        .modal-dialog {
            max-width: 500px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="profile-section text-start">
            <div class="profile-image">
                <img src="https://via.placeholder.com/80" alt="Profile" class="rounded-circle" />
            </div>
            <h5 class="text-white mt-2 ms-3">Admin</h5>
        </div>
        <nav class="nav flex-column sidebar-nav mt-3">
            <a href="#package" class="nav-link active" data-page="package">Package Products</a>
            <a href="#staff" class="nav-link" data-page="staff">Staff</a>
            <a href="#customers" class="nav-link" data-page="customers">Customer's Info</a>
            <a href="#orders" class="nav-link" data-page="orders">Orders</a>
            <a href="#logout" class="nav-link text-danger" data-page="logout">Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <!-- Package Products -->
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
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Basic Package</td>
                            <td>Includes 3 viands and rice.</td>
                            <td>$50</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn" data-id="1">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="1">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Premium Package</td>
                            <td>Includes 5 viands, rice, and dessert.</td>
                            <td>$80</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn" data-id="2">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="2">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Staff Management -->
        <div id="staff" class="content-page">
            <h1 class="text-center mb-4">Staff</h1>
            <div class="container mt-3">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>STAFF ID</th>
                            <th>NAME</th>
                            <th>POSITION</th>
                            <th>CONTACT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through the fetched staff data
                        foreach ($staff_results as $sresult) {
                            echo '<tr>
                                    <td>' . htmlspecialchars($sresult['id']) . '</td>
                                    <td>' . htmlspecialchars($sresult['full_name']) . '</td>
                                    <td>' . htmlspecialchars($sresult['role']) . '</td>
                                    <td>' . htmlspecialchars($sresult['contact_number']) . '</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm edit-btn" data-id="' . htmlspecialchars($sresult['id']) . '">Edit</button>
                                        <button class="btn btn-danger btn-sm delete-btn" data-id="' . htmlspecialchars($sresult['id']) . '">Delete</button>
                                    </td>
                                  </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Customer Info -->
        <div id="customers" class="content-page">
            <div class="container mt-3">
                <h1 class="text-center mb-4">Customer's Info</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>CUSTOMER ID</th>
                            <th>FULL NAME</th>
                            <th>EMAIL</th>
                            <th>CONTACT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populate customer data from the database -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders -->
        <div id="orders" class="content-page">
            <h1 class="text-center mb-4">Orders</h1>
            <div class="container mt-3">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ORDER ID</th>
                            <th>CUSTOMER</th>
                            <th>PACKAGE</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populate orders data from the database -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Edit/Delete Modal -->
    <div class="modal fade" id="modalAction" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Action Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to perform this action?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmAction">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    
    <script>
        const pages = document.querySelectorAll('.content-page');
        const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
        
        // Switch between pages
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = e.target.getAttribute('data-page');
                pages.forEach(pageElement => {
                    pageElement.classList.remove('active');
                });
                document.getElementById(page).classList.add('active');
                
                // Set active link
                navLinks.forEach(navLink => {
                    navLink.classList.remove('active');
                });
                e.target.classList.add('active');
            });
        });

        // Edit and delete button actions
        const editBtns = document.querySelectorAll('.edit-btn');
        const deleteBtns = document.querySelectorAll('.delete-btn');

        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Show edit form and pre-fill data
                console.log(`Editing staff ID: ${btn.dataset.id}`);
            });
        });

        deleteBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('modalAction'));
                modal.show();

                const confirmBtn = document.getElementById('confirmAction');
                confirmBtn.addEventListener('click', () => {
                    console.log(`Deleting staff ID: ${btn.dataset.id}`);
                    modal.hide();
                });
            });
        });
    </script>
</body>
</html>
