<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('php/catering_db.php');


// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php"); // Redirect to home if already logged in
    exit();
}

$active_tab = 'login'; // Default tab

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Sanitize email and password
        $email = htmlspecialchars(strip_tags($_POST['email']));
        $password = $_POST['password'];

        // Check if both email and password are provided
        if (empty($email) || empty($password)) {
            $login_error = "Please enter both email and password.";
        } else {
            // Fetch user data from the database
            try {
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Compare the plain text password (since you are using plain text for now)
                    if ($password === $user['password']) {
                        // Password matches, set session and redirect
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['email'] = $user['email'];

                        // Log session for debugging
                        error_log("Session Set: User ID: " . $_SESSION['user_id'] . ", Email: " . $_SESSION['email']);
                        if($user['role'] == 'admin' ) {
                            header("Location: staff_index.php");
                        } else {
                            header("Location: index.php");
                        }// Redirect to home after login
                        exit();
                    } else {
                        $login_error = "Incorrect password. Please try again.";
                    }
                } else {
                    $login_error = "Account not found. Please sign up.";
                }
            } catch (PDOException $e) {
                $login_error = "Database error. Please try again later.";
            }
        }
    }

    if (isset($_POST['signup'])) {
        // Check if all fields are provided
        if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['full_name']) && !empty($_POST['address']) && !empty($_POST['contact_number'])) {
            try {
                // Sanitize inputs
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password']; // Consider hashing this password in the future
                $full_name = htmlspecialchars(strip_tags($_POST['full_name']));
                $address = htmlspecialchars(strip_tags($_POST['address']));
                $contact_number = htmlspecialchars(strip_tags($_POST['contact_number']));

                // Prepare SQL statement to insert user data
                $sql = "INSERT INTO users (email, password, full_name, address, contact_number) VALUES (?, ?, ?, ?, ?)";
                $stmtinsert = $db->prepare($sql);

                // Execute insert statement
                $result = $stmtinsert->execute([$email, $password, $full_name, $address, $contact_number]);

                if ($result) {
                    $active_tab = 'login';
                    $signup_success = "Sign-up successful! Please log in.";
                } else {
                    $signup_error = "Error: Unable to sign up. Please try again later.";
                }
            } catch (PDOException $e) {
                $signup_error = "Database Error: " . htmlspecialchars($e->getMessage());
            }
        } else {
            $signup_error = "All fields are required.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="pics/logo.">
    <style>
        .card {
            border-radius: 15px;
            padding: 20px;
            background-color: #ffffff;
        }
        .logo-container {
            width: 100px;
            height: 100px;
            border: 2px solid #007bff;
            border-radius: 50%;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }
        .logo-container img {
            max-width: 80%;
            max-height: 80%;
            border-radius: 50%;
        }
        h4 {
            color: #007bff;
            font-weight: bold;
        }
        .nav-pills .nav-link {
            border-radius: 30px;
            padding: 10px 20px;
        }
        .nav-pills .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        .form-label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn {
            border-radius: 30px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Kuya Card's Catering</a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow text-center">
                <div class="card-body">
                    <div class="logo-container mx-auto mb-3">
                        <img src="pics/logo.png" alt="Logo" class="img-fluid rounded-circle" />
                    </div>
                    <h4 class="mb-4">Kuya Card's Catering</h4>

                    <ul class="nav nav-pills justify-content-center mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo $active_tab === 'login' ? 'active' : ''; ?>" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo $active_tab === 'signup' ? 'active' : ''; ?>" id="pills-signup-tab" data-bs-toggle="pill" data-bs-target="#pills-signup" type="button" role="tab" aria-controls="pills-signup" aria-selected="false">Sign Up</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade <?php echo $active_tab === 'login' ? 'show active' : ''; ?>" id="pills-login" role="tabpanel" aria-labelledby="pills-login-tab">
                            <?php if (isset($signup_success)) { echo '<div class="alert alert-success">' . $signup_success . '</div>'; } ?>
                            <form action="index.php" method="POST">
                                <div class="mb-3">
                                    <label for="login-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="login-email" name="email" required />
                                </div>
                                <div class="mb-3">
                                    <label for="login-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="login-password" name="password" required />
                                </div>
                                <?php if (isset($login_error)) { echo '<div class="alert alert-danger">' . $login_error . '</div>'; } ?>
                                <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
                            </form>
                        </div>

                        <div class="tab-pane fade <?php echo $active_tab === 'signup' ? 'show active' : ''; ?>" id="pills-signup" role="tabpanel" aria-labelledby="pills-signup-tab">
                            <form action="index.php" method="POST">
                                <div class="mb-3">
                                    <label for="signup-full-name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="signup-full-name" name="full_name" required />
                                </div>
                                <div class="mb-3">
                                    <label for="signup-address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="signup-address" name="address" required />
                                </div>
                                <div class="mb-3">
                                    <label for="signup-contact-number" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="signup-contact-number" name="contact_number" required />
                                </div>
                                <div class="mb-3">
                                    <label for="signup-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="signup-email" name="email" required />
                                </div>
                                <div class="mb-3">
                                    <label for="signup-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="signup-password" name="password" required />
                                </div>
                                <?php if (isset($signup_error)) { echo '<div class="alert alert-danger">' . $signup_error . '</div>'; } ?>
                                <button type="submit" class="btn btn-success w-100" name="signup">Sign Up</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
