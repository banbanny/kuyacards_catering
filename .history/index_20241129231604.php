<?php
session_start();
require_once('php/catering_db.php');

// Redirect logged-in users to the home page
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$active_tab = 'login'; // Default tab

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Login Handler
    if (isset($_POST['login'])) {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        if (empty($email) || empty($password)) {
            $login_error = "Please enter both email and password.";
        } else {
            try {
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $password === $user['password']) {
                    // Password matches; create session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];
                    header("Location: login.php");
                    exit();
                } else {
                    $login_error = "Invalid email or password. Please try again.";
                }
            } catch (PDOException $e) {
                $login_error = "Database error. Please try again later.";
            }
        }
    }

    // Signup Handler
    if (isset($_POST['signup'])) {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        if (empty($email) || empty($password)) {
            $signup_error = "Both email and password are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $signup_error = "Invalid email format.";
        } else {
            try {
                // Check if the email already exists
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $signup_error = "Email is already registered. Please log in.";
                } else {
                    // Store plain-text password (not recommended)
                    $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                    if ($stmt->execute([$email, $password])) {
                        $signup_success = "Sign-up successful! Please log in.";
                        $active_tab = 'login'; // Switch to login tab after successful signup
                    } else {
                        $signup_error = "Sign-up failed. Please try again.";
                    }
                }
            } catch (PDOException $e) {
                $signup_error = "Database error. Please try again later.";
            }
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