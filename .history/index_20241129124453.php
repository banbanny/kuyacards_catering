<?php
session_start();
require_once('php/catering_db.php');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php"); // Redirect to home if already logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Sanitize email and password
        $email = htmlspecialchars(strip_tags($_POST['email']));
        $password = $_POST['password'];

        // Check if both email and password are provided
        if (empty($email) || empty($password)) {
            $login_error = "Please enter both email and password.";
            error_log("Login error: Both email and password are required.");
        } else {
            // Fetch user data from the database
            try {
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Direct password comparison (since we are storing plain text passwords)
                    if ($password === $user['password']) {
                        // Password matches, set session and redirect
                        $_SESSION['user_id'] = $user['user_id'];  
                        $_SESSION['email'] = $user['email'];      
                        header("Location: index.php");  
                        exit();
                    } else {
                        $login_error = "Invalid Email or Password!";
                        error_log("Login failed: Incorrect password for email: " . $email);
                    }
                } else {
                    $login_error = "Invalid Email or Password!";
                    error_log("Login failed: No user found with email: " . $email);
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());  // Log database-related errors
                $login_error = "There was an error with the database. Please try again.";
            }
        }
    }

    if (isset($_POST['signup'])) {
        // Check if email and password are provided
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = htmlspecialchars(strip_tags($_POST['password']));
    
            // Insert plain text password (no hashing)
            $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
            $stmtinsert = $db->prepare($sql);
    
            try {
                $result = $stmtinsert->execute([$email, $password]); // Don't hash the password
                if ($result) {
                    header("Location: home.php?signup=success"); // Redirect to login form
                    exit();
                } else {
                    echo '<div class="alert alert-danger">Signup failed. Please try again.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Both email and password are required.</div>';
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
        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }
    </style>
</head>
<body>

<!-- Navbar -->
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
                    <!-- Logo -->
                    <div class="logo-container mx-auto mb-4">
                        <img src=pics\logo.png" alt="Logo" class="img-fluid rounded-circle" />
                    </div>
                    <h4 class="mb-4">Kuya Card's Catering</h4>

                    <!-- Tabs -->
                    <ul class="nav nav-pills justify-content-center mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-signup-tab" data-bs-toggle="pill" data-bs-target="#pills-signup" type="button" role="tab" aria-controls="pills-signup" aria-selected="false">Sign Up</button>
                        </li>
                    </ul>

                    <!-- Form Content -->
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Login Form -->
                        <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="pills-login-tab">
                            <form action="home.php" method="POST">
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

                        <!-- Sign Up Form -->
                        <div class="tab-pane fade" id="pills-signup" role="tabpanel" aria-labelledby="pills-signup-tab">
                            <form action="home.php" method="POST">
                                <div class="mb-3">
                                    <label for="signup-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="signup-email" name="email" required />
                                </div>
                                <div class="mb-3">
                                    <label for="signup-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="signup-password" name="password" required />
                                </div>
                                <button type="submit" class="btn btn-success w-100" name="signup">Sign Up</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<!-- Footer -->
<footer class="text-center mt-5">
    <p>&copy; 2024 Kuya Card's Catering</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const showLogin = document.getElementById('showLogin');
    const showSignup = document.getElementById('showSignup');

    showLogin.addEventListener('click', () => {
        loginForm.classList.add('active');
        signupForm.classList.remove('active');
        // Reset the login form fields
        document.getElementById('login-email').value = '';
        document.getElementById('login-password').value = '';
    });

    showSignup.addEventListener('click', () => {
        signupForm.classList.add('active');
        loginForm.classList.remove('active');
        // Reset the signup form fields
        document.getElementById('signup-email').value = '';
        document.getElementById('signup-password').value = '';
    });
</script>

</body>
</html>
