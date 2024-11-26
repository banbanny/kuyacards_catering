<?php
session_start();
require_once('php/catering_db.php');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to home if already logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Login Process
        $email = htmlspecialchars(strip_tags($_POST['email']));
        $password = $_POST['password'];

        // Fetch user data from database
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Check the hashed password in the database
        echo 'Database Password Hash: ' . $user['password'] . '<br>';
        echo 'Input Password: ' . $password . '<br>';

        if ($user && password_verify($password, $user['password'])) {
            // Set session and redirect to index.php
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Invalid Email or Password!";
        }
    } 
    
    if (isset($_POST['signup'])) {
        // Check if email and password are provided
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = htmlspecialchars(strip_tags($_POST['password']));
    
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
            // Prepare SQL and insert into database
            $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
            $stmtinsert = $db->prepare($sql);
    
            try {
                $result = $stmtinsert->execute([$email, $hashed_password]);
                if ($result) {
                    header("Location: login_signup.php?signup=success"); // Redirect to login form
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
    <title>Login / Sign Up</title>
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
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center">Login or Sign Up</h3>

                     <!-- Display Success Message if Signup is successful -->
                     <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success') { ?>
                        <div class="alert alert-success">Signup successful! Please log in.</div>
                    <?php } ?>

                  <!-- Login Form -->
                            <form id="loginForm" class="form-container active" action="login_signup.php" method="POST">
                                <h4>Login</h4>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required />
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required />
                                </div>
                                <?php if (isset($login_error)) { echo '<div class="alert alert-danger">' . $login_error . '</div>'; } ?>
                                <button type="submit" class="btn btn-primary mb-3" name="login">Login</button>

                                <!-- Sign Up Button -->
                                <button type="button" id="showSignup" class="btn btn-success w-100">Sign Up</button>
                            </form>

                            <!-- Sign Up Form -->
                            <form id="signupForm" class="form-container" action="login_signup.php" method="POST">
                                <h4>Sign Up</h4>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required />
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required />
                                </div>
                                <button type="submit" class="btn btn-success" name="signup">Sign Up</button>
                                
                                <!-- Back to Login Button -->
                                <button type="button" id="showLogin" class="btn btn-primary w-100 mt-3">Back to Login</button>
                            </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
    });

    showSignup.addEventListener('click', () => {
        signupForm.classList.add('active');
        loginForm.classList.remove('active');
    });
</script>
</body>
</html>
